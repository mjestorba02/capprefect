@extends('layouts.app')

@section('title', 'Infraction Logging')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Infraction Logging</h2>
            <p class="text-muted mb-0">Monitor, record, and manage student infractions and sanctions.</p>
        </div>
        <button id="openAddModal" type="button" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Log New Infraction
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
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i> Infraction Records</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Infraction ID</th>
                        <th>Student</th>
                        <th>Category</th>
                        <th>Date / Time</th>
                        <th>Severity</th>
                        <th>Reported By</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($infractions as $infraction)
                        <tr>
                            <td>INF{{ str_pad($infraction->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ optional($infraction->student)->fullname ?? 'Unknown' }}</td>
                            <td>{{ $infraction->violation_category }}</td>
                            <td>{{ \Carbon\Carbon::parse($infraction->datetime)->format('M d, Y h:i A') }}</td>
                            <td>
                                <span class="badge rounded-pill 
                                    @if(strtolower($infraction->severity) == 'minor') bg-success
                                    @elseif(strtolower($infraction->severity) == 'moderate') bg-warning text-dark
                                    @else bg-danger @endif">
                                    {{ ucfirst($infraction->severity) }}
                                </span>
                            </td>
                            <td>{{ $infraction->reported_by }}</td>
                            <td>
                                <span class="badge rounded-pill {{ strtolower($infraction->status) == 'open' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ ucfirst($infraction->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-info btn-view"
                                    data-id="{{ $infraction->id }}"
                                    data-student="{{ optional($infraction->student)->fullname ?? 'Unknown' }}"
                                    data-category="{{ $infraction->violation_category }}"
                                    data-datetime="{{ \Carbon\Carbon::parse($infraction->datetime)->format('M d, Y h:i A') }}"
                                    data-severity="{{ $infraction->severity }}"
                                    data-reported_by="{{ $infraction->reported_by }}"
                                    data-sanction="{{ $infraction->sanction_assigned }}"
                                    data-parent_notified="{{ $infraction->parent_notified ? 'Yes' : 'No' }}"
                                    data-status="{{ $infraction->status }}"
                                    data-description="{{ $infraction->description }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit"
                                    data-id="{{ $infraction->id }}"
                                    data-student="{{ $infraction->student_id }}"
                                    data-category="{{ $infraction->violation_category }}"
                                    data-severity="{{ $infraction->severity }}"
                                    data-reported_by="{{ $infraction->reported_by }}"
                                    data-sanction="{{ $infraction->sanction_assigned }}"
                                    data-parent_notified="{{ $infraction->parent_notified ? '1' : '0' }}"
                                    data-status="{{ $infraction->status }}"
                                    data-description="{{ $infraction->description }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                    data-id="{{ $infraction->id }}"
                                    data-description="{{ $infraction->description }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">No infractions logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3 px-3">{{ $infractions->links() }}</div>
</div>

{{-- === ADD MODAL === --}}
<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeAddModal">&times;</button>
        <div class="modal-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Add Infraction</h5>
        </div>
        <form action="{{ route('infractions.store') }}" method="POST">
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
                    <label class="form-label">Violation Category</label>
                    <select id="add_violation_category" name="violation_category" class="form-select" required>
                        <option value="">-- Select Violation Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_name }}"
                                data-severity="{{ $category->severity_level }}"
                                data-sanction="{{ $category->default_sanction }}">
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Severity</label>
                    <input type="text" id="add_severity" name="severity" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Assigned Sanction</label>
                    <input id="add_sanction" name="sanction_assigned" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Reported By</label>
                    <input name="reported_by" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="parent_notified" value="0">
                    <input class="form-check-input" type="checkbox" id="parentNotifiedAdd" name="parent_notified" value="1">
                    <label class="form-check-label" for="parentNotifiedAdd">Parent Notified</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelAddModal" class="btn btn-light">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- === EDIT MODAL === --}}
<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeEditModal">&times;</button>
        <div class="modal-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Infraction</h5>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Student</label>
                    <select id="edit_student_id" name="student_id" class="form-select" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->student_id }}">{{ $student->fullname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Violation Category</label>
                    <select id="edit_violation_category" name="violation_category" class="form-select" required>
                        <option value="">-- Select Violation Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_name }}"
                                data-severity="{{ $category->severity_level }}"
                                data-sanction="{{ $category->default_sanction }}">
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Severity</label>
                    <input type="text" id="edit_severity" name="severity" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Assigned Sanction</label>
                    <input id="edit_sanction" name="sanction_assigned" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Reported By</label>
                    <input id="edit_reported_by" name="reported_by" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="parent_notified" value="0">
                    <input class="form-check-input" type="checkbox" id="edit_parent_notified" name="parent_notified" value="1">
                    <label class="form-check-label" for="edit_parent_notified">Parent Notified</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select id="edit_status" name="status" class="form-select" required>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelEditModal" class="btn btn-light">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- === DELETE CONFIRMATION MODAL === --}}
<div id="deleteModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeDeleteModal">&times;</button>
        <div class="modal-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Confirm Deletion</h5>
        </div>
        <div class="modal-body">
            <p id="deleteMessage" class="fs-5 text-center">Are you sure you want to delete this infraction?</p>
        </div>
        <div class="modal-footer">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" id="cancelDeleteModal" class="btn btn-light">Cancel</button>
                <button type="submit" class="btn btn-danger px-4">Delete</button>
            </form>
        </div>
    </div>
</div>

{{-- === VIEW MODAL === --}}
<div id="viewModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeViewModal">&times;</button>
        <div class="modal-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-eye me-2"></i> View Infraction Details</h5>
        </div>
        <div class="modal-body">
            <dl class="row">
                <dt class="col-sm-4">Student:</dt>
                <dd class="col-sm-8" id="view_student"></dd>

                <dt class="col-sm-4">Violation Category:</dt>
                <dd class="col-sm-8" id="view_category"></dd>

                <dt class="col-sm-4">Date / Time:</dt>
                <dd class="col-sm-8" id="view_datetime"></dd>

                <dt class="col-sm-4">Severity:</dt>
                <dd class="col-sm-8" id="view_severity"></dd>

                <dt class="col-sm-4">Reported By:</dt>
                <dd class="col-sm-8" id="view_reported_by"></dd>

                <dt class="col-sm-4">Sanction Assigned:</dt>
                <dd class="col-sm-8" id="view_sanction"></dd>

                <dt class="col-sm-4">Parent Notified:</dt>
                <dd class="col-sm-8" id="view_parent_notified"></dd>

                <dt class="col-sm-4">Status:</dt>
                <dd class="col-sm-8" id="view_status"></dd>

                <dt class="col-sm-4">Description:</dt>
                <dd class="col-sm-8" id="view_description"></dd>
            </dl>
        </div>
        <div class="modal-footer">
            <button type="button" id="closeViewBtn" class="btn btn-light">Close</button>
        </div>
    </div>
</div>

{{-- === SCRIPT === --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const open = m => m.classList.add('active');
    const close = m => m.classList.remove('active');

    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');

    // ADD MODAL
    document.getElementById('openAddModal').onclick = () => open(addModal);
    document.getElementById('closeAddModal').onclick = () => close(addModal);
    document.getElementById('cancelAddModal').onclick = () => close(addModal);

    // DELETE MODAL
    const deleteForm = document.getElementById('deleteForm');
    const deleteMessage = document.getElementById('deleteMessage');
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            deleteForm.action = `/infractions/${btn.dataset.id}`;
            deleteMessage.textContent = `Are you sure you want to delete "${btn.dataset.description}"?`;
            open(deleteModal);
        });
    });
    document.getElementById('closeDeleteModal').onclick = () => close(deleteModal);
    document.getElementById('cancelDeleteModal').onclick = () => close(deleteModal);

    // EDIT MODAL
    const editForm = document.getElementById('editForm');
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            editForm.action = `/infractions/${btn.dataset.id}`;
            document.getElementById('edit_student_id').value = btn.dataset.student;
            document.getElementById('edit_violation_category').value = btn.dataset.category;
            document.getElementById('edit_severity').value = btn.dataset.severity;
            document.getElementById('edit_reported_by').value = btn.dataset.reported_by;
            document.getElementById('edit_sanction').value = btn.dataset.sanction;
            document.getElementById('edit_status').value = btn.dataset.status;
            document.getElementById('edit_description').value = btn.dataset.description;
            document.getElementById('edit_parent_notified').checked = btn.dataset.parent_notified === '1';
            open(editModal);
        });
    });
    document.getElementById('closeEditModal').onclick = () => close(editModal);
    document.getElementById('cancelEditModal').onclick = () => close(editModal);

    // AUTO-FILL SEVERITY (Add + Edit)
    function attachCategoryListener(selectId, severityId, sanctionId) {
        const select = document.getElementById(selectId);
        const sev = document.getElementById(severityId);
        const sanc = document.getElementById(sanctionId);
        if (select) {
            select.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                sev.value = selected.getAttribute('data-severity') || '';
                sanc.value = selected.getAttribute('data-sanction') || '';
                sev.setAttribute('readonly', true);
            });
        }
    }
    attachCategoryListener('add_violation_category', 'add_severity', 'add_sanction');
    attachCategoryListener('edit_violation_category', 'edit_severity', 'edit_sanction');

    // VIEW MODAL
    const viewModal = document.getElementById('viewModal');
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('view_student').textContent = btn.dataset.student;
            document.getElementById('view_category').textContent = btn.dataset.category;
            document.getElementById('view_datetime').textContent = btn.dataset.datetime;
            document.getElementById('view_severity').textContent = btn.dataset.severity;
            document.getElementById('view_reported_by').textContent = btn.dataset.reported_by;
            document.getElementById('view_sanction').textContent = btn.dataset.sanction || '-';
            document.getElementById('view_parent_notified').textContent = btn.dataset.parent_notified;
            document.getElementById('view_status').textContent = btn.dataset.status;
            document.getElementById('view_description').textContent = btn.dataset.description;
            open(viewModal);
        });
    });
    document.getElementById('closeViewModal').onclick = () => close(viewModal);
    document.getElementById('closeViewBtn').onclick = () => close(viewModal);
    viewModal.addEventListener('click', e => { if (e.target === viewModal) close(viewModal); });
});
</script>

{{-- === MODAL STYLE === --}}
<style>
.modal-overlay { display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.65); justify-content: center; align-items: center; z-index: 9999; }
.modal-overlay.active { display: flex; }
.modal-content { background: #fff; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.35); width: 600px; max-height: 90vh; overflow-y: auto; position: relative; animation: fadeIn 0.25s ease-in-out; }
.modal-close { position: absolute; top: 10px; right: 15px; font-size: 1.8rem; color: #888; background: none; border: none; cursor: pointer; }
.modal-header { padding: 15px 20px; border-bottom: 1px solid #ddd; border-radius: 12px 12px 0 0; }
.modal-body { padding: 20px; }
.modal-footer { padding: 15px 20px; border-top: 1px solid #ddd; background-color: #f9fafb; display: flex; justify-content: flex-end; gap: 10px; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
#viewModal dl dt { font-weight: 600; color: #1E3A8A; }
#viewModal dl dd { margin-bottom: .5rem; word-break: break-word; }
</style>
@endsection