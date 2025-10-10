@extends('layouts.app')

@section('title', 'Clearance Hold Flagging')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Clearance Hold Flagging</h2>
            <p class="text-muted mb-0">Monitor and manage clearance holds assigned to students.</p>
        </div>
        <button id="openAddModal" type="button" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Add Clearance Hold
        </button>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient bg-primary text-white fw-semibold">
            <i class="fas fa-ban me-2"></i> Clearance Hold Records
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Hold ID</th>
                        <th>Student</th>
                        <th>Department</th>
                        <th>Reason</th>
                        <th>Date Flagged</th>
                        <th>Status</th>
                        <th>Cleared Date</th>
                        <th>Cleared By</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($holds as $hold)
                        <tr>
                            <td>CH0{{ $hold->id }}</td>
                            <td>{{ $hold->student->fullname ?? 'N/A' }}</td>
                            <td>{{ $hold->department }}</td>
                            <td>{{ $hold->reason }}</td>
                            <td>{{ \Carbon\Carbon::parse($hold->date_flagged)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $hold->status === 'Active' ? 'bg-danger' : 'bg-success' }}">
                                    {{ $hold->status }}
                                </span>
                            </td>
                            <td>{{ $hold->cleared_date ? \Carbon\Carbon::parse($hold->cleared_date)->format('M d, Y') : '-' }}</td>
                            <td>{{ $hold->cleared_by ?? '-' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit"
                                    data-id="{{ $hold->id }}"
                                    data-student="{{ $hold->student_id }}"
                                    data-department="{{ $hold->department }}"
                                    data-reason="{{ $hold->reason }}"
                                    data-date="{{ $hold->date_flagged }}"
                                    data-status="{{ $hold->status }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                    data-id="{{ $hold->id }}"
                                    data-reason="{{ $hold->reason }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                @if($hold->status === 'Active')
                                    <form action="{{ route('clearance_holds.lift', $hold->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check"></i> Lift
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-3">No clearance holds found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $holds->links() }}
    </div>
</div>

{{-- === ADD MODAL === --}}
<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeAddModal">&times;</button>
        <div class="modal-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Add Clearance Hold</h5>
        </div>
        <form action="{{ route('clearance_holds.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->student_id }}">{{ $student->fullname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date Flagged</label>
                    <input type="date" name="date_flagged" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Active">Active</option>
                        <option value="Cleared">Cleared</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelAddModal" class="btn btn-light">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- === EDIT CLEARANCE HOLD MODAL === -->
<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeEditModal">&times;</button>
        <div class="modal-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Clearance Hold</h5>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" id="edit_student_id" class="form-select" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->student_id }}">{{ $student->fullname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <input type="text" id="edit_department" name="department" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea id="edit_reason" name="reason" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date Flagged</label>
                    <input type="date" id="edit_date_flagged" name="date_flagged" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select id="edit_status" name="status" class="form-select" required>
                        <option value="Active">Active</option>
                        <option value="Cleared">Cleared</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelEditModal" class="btn btn-light">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- === CONFIRM DELETE MODAL === --}}
<div id="confirmDeleteModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeConfirmModal">&times;</button>
        <div class="modal-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Confirm Deletion</h5>
        </div>
        <div class="modal-body">
            <p id="deleteMessage" class="fs-5 text-center">Are you sure you want to delete this record?</p>
        </div>
        <div class="modal-footer">
            <form id="confirmDeleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" id="cancelConfirmModal" class="btn btn-light">Cancel</button>
                <button type="submit" class="btn btn-danger px-4">Delete</button>
            </form>
        </div>
    </div>
</div>

{{-- === Scripts === --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addModal = document.getElementById('addModal');
    const confirmModal = document.getElementById('confirmDeleteModal');
    const confirmForm = document.getElementById('confirmDeleteForm');
    const deleteMessage = document.getElementById('deleteMessage');

    window.openModal = (m) => { m.classList.add('active'); document.body.style.overflow = 'hidden'; };
    window.closeModal = (m) => { m.classList.remove('active'); document.body.style.overflow = ''; };

    // Add Modal
    document.getElementById('openAddModal').onclick = () => openModal(addModal);
    document.getElementById('closeAddModal').onclick = () => closeModal(addModal);
    document.getElementById('cancelAddModal').onclick = () => closeModal(addModal);
    addModal.addEventListener('click', e => { if (e.target === addModal) closeModal(addModal); });

    // Delete Modal
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            deleteMessage.textContent = `Are you sure you want to delete "${btn.dataset.reason}" (ID: ${btn.dataset.id})?`;
            confirmForm.action = `/clearance_holds/${btn.dataset.id}`;
            openModal(confirmModal);
        });
    });
    document.getElementById('closeConfirmModal').onclick = () => closeModal(confirmModal);
    document.getElementById('cancelConfirmModal').onclick = () => closeModal(confirmModal);

    // === EDIT MODAL ===
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            // Populate fields
            document.getElementById('edit_student_id').value = btn.dataset.student;
            document.getElementById('edit_department').value = btn.dataset.department;
            document.getElementById('edit_reason').value = btn.dataset.reason;
            document.getElementById('edit_date_flagged').value = btn.dataset.date;
            document.getElementById('edit_status').value = btn.dataset.status;

            // Update form action
            editForm.action = `/clearance_holds/${btn.dataset.id}`;

            // Show modal
            openModal(editModal);
        });
    });

    document.getElementById('closeEditModal').onclick = () => closeModal(editModal);
    document.getElementById('cancelEditModal').onclick = () => closeModal(editModal);
    editModal.addEventListener('click', e => { if (e.target === editModal) closeModal(editModal); });

});
</script>

{{-- === Styles === --}}
<style>
.modal-overlay { display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.65); justify-content: center; align-items: center; z-index: 9999; }
.modal-overlay.active { display: flex; }
.modal-content { background: #fff; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.35); width: 600px; max-height: 90vh; overflow-y: auto; position: relative; animation: fadeIn 0.25s ease-in-out; }
.modal-close { position: absolute; top: 10px; right: 15px; font-size: 1.8rem; color: #888; background: none; border: none; cursor: pointer; }
.modal-header { padding: 15px 20px; border-bottom: 1px solid #ddd; border-radius: 12px 12px 0 0; }
.modal-body { padding: 20px; }
.modal-footer { padding: 15px 20px; border-top: 1px solid #ddd; background-color: #f9fafb; display: flex; justify-content: flex-end; gap: 10px; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection