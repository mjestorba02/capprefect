@extends('layouts.app')

@section('title', 'Behavior Monitoring')

@section('content')
<div class="container">
    <div class="container-fluid py-4">

        {{-- === HEADER === --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Behavior Monitoring</h2>
                <p class="text-muted mb-0">Track, update, and manage student behaviors and interventions.</p>
            </div>
            <button id="openAddModal" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Add Record
            </button>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- === BEHAVIOR TABLE === --}}
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Behavior Records</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Behavior ID</th>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Points</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($behaviors as $b)
                            <tr>
                                <td class="fw-semibold">{{ $b->behavior_id }}</td>
                                <td>{{ $b->student->name ?? $b->student_id }}</td>
                                <td>{{ $b->date_recorded }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $b->behavior_type == 'Positive' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $b->behavior_type }}
                                    </span>
                                </td>
                                <td>{{ $b->behavior_category }}</td>
                                <td>{{ $b->points }}</td>
                                <td>
                                    <span class="badge {{ $b->status == 'Active' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                        {{ $b->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info btn-view" 
                                        data-behavior='@json($b)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning btn-edit" 
                                        data-behavior='@json($b)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete"
                                        data-id="{{ $b->id }}"
                                        data-name="{{ $b->behavior_id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @if($b->status == 'Active')
                                        <form action="{{ route('behaviors.resolve', $b) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-3">No behavior records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $behaviors->links() }}
        </div>
    </div>
</div>

{{-- === ADD BEHAVIOR MODAL === --}}
<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeAddModal">&times;</button>

        <div class="modal-header">
            <h2>Add Behavior Record</h2>
        </div>

        <form action="{{ route('behaviors.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <label for="student_id">Student</label>
                <select name="student_id" id="student_id" required>
                    <option value="">-- Select Student --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->student_id }}">
                            {{ $student->student_id }} - {{ $student->fullname ?? 'Unnamed' }}
                        </option>
                    @endforeach
                </select>

                <label>Date Recorded</label>
                <input type="date" name="date_recorded" required>

                <label>Behavior Type</label>
                <select name="behavior_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="Positive">Positive</option>
                    <option value="Negative">Negative</option>
                </select>

                <label>Behavior Category</label>
                <input type="text" name="behavior_category" required>

                <label>Description</label>
                <textarea name="description" rows="3" required></textarea>

                <label>Recorded By</label>
                <input type="text" name="recorded_by" required>

                <label>Action Taken</label>
                <input type="text" name="action_taken">

                <label>Points</label>
                <input type="number" name="points" required>

                <label>Status</label>
                <select name="status" required>
                    <option value="Active">Active</option>
                    <option value="Resolved">Resolved</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" id="cancelAddModal" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- === EDIT BEHAVIOR MODAL === --}}
<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeEditModal">&times;</button>

        <div class="modal-header">
            <h2>Edit Behavior Record</h2>
        </div>

        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <label>Behavior Category</label>
                <input type="text" id="edit_behavior_category" name="behavior_category" required>

                <label>Description</label>
                <textarea id="edit_description" name="description" rows="3" required></textarea>

                <label>Action Taken</label>
                <input type="text" id="edit_action_taken" name="action_taken">

                <label>Points</label>
                <input type="number" id="edit_points" name="points" required>

                <label>Status</label>
                <select id="edit_status" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Resolved">Resolved</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" id="cancelEditModal" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- === DELETE CONFIRM MODAL === --}}
<div id="confirmDeleteModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeConfirmModal">&times;</button>

        <div class="modal-header">
            <h2>Confirm Deletion</h2>
        </div>

        <div class="modal-body">
            <p id="deleteMessage">Are you sure you want to delete this behavior record?</p>
        </div>

        <div class="modal-footer">
            <form id="confirmDeleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" id="cancelConfirmModal" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save bg-danger border-0">Delete</button>
            </form>
        </div>
    </div>
</div>

{{-- === VIEW BEHAVIOR MODAL === --}}
<div id="viewModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeViewModal">&times;</button>

        <div class="modal-header">
            <h2>View Behavior Record</h2>
        </div>

        <div class="modal-body">
            <p><strong>Behavior ID:</strong> <span id="view_behavior_id"></span></p>
            <p><strong>Student:</strong> <span id="view_student"></span></p>
            <p><strong>Date Recorded:</strong> <span id="view_date"></span></p>
            <p><strong>Type:</strong> <span id="view_type"></span></p>
            <p><strong>Category:</strong> <span id="view_category"></span></p>
            <p><strong>Description:</strong> <span id="view_description"></span></p>
            <p><strong>Recorded By:</strong> <span id="view_recorded_by"></span></p>
            <p><strong>Action Taken:</strong> <span id="view_action_taken"></span></p>
            <p><strong>Points:</strong> <span id="view_points"></span></p>
            <p><strong>Status:</strong> <span id="view_status"></span></p>
        </div>

        <div class="modal-footer">
            <button type="button" id="closeViewBtn" class="btn-cancel">Close</button>
        </div>
    </div>
</div>

{{-- === JS LOGIC FOR MODALS === --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    const confirmModal = document.getElementById('confirmDeleteModal');

    // === VIEW MODAL ===
    const viewModal = document.getElementById('viewModal');
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const b = JSON.parse(this.dataset.behavior);
            document.getElementById('view_behavior_id').textContent = b.behavior_id;
            document.getElementById('view_student').textContent = b.student?.name ?? b.student_id;
            document.getElementById('view_date').textContent = b.date_recorded;
            document.getElementById('view_type').textContent = b.behavior_type;
            document.getElementById('view_category').textContent = b.behavior_category;
            document.getElementById('view_description').textContent = b.description;
            document.getElementById('view_recorded_by').textContent = b.recorded_by;
            document.getElementById('view_action_taken').textContent = b.action_taken ?? 'None';
            document.getElementById('view_points').textContent = b.points;
            document.getElementById('view_status').textContent = b.status;
            viewModal.classList.add('active');
        });
    });

    document.getElementById('closeViewModal').addEventListener('click', () => viewModal.classList.remove('active'));
    document.getElementById('closeViewBtn').addEventListener('click', () => viewModal.classList.remove('active'));

    // === ADD MODAL ===
    document.getElementById('openAddModal').addEventListener('click', () => addModal.classList.add('active'));
    document.getElementById('closeAddModal').addEventListener('click', () => addModal.classList.remove('active'));
    document.getElementById('cancelAddModal').addEventListener('click', () => addModal.classList.remove('active'));

    // === EDIT MODAL ===
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const behavior = JSON.parse(this.dataset.behavior);
            document.getElementById('edit_behavior_category').value = behavior.behavior_category;
            document.getElementById('edit_description').value = behavior.description;
            document.getElementById('edit_action_taken').value = behavior.action_taken ?? '';
            document.getElementById('edit_points').value = behavior.points;
            document.getElementById('edit_status').value = behavior.status;
            document.getElementById('editForm').action = `/behaviors/${behavior.id}`;
            editModal.classList.add('active');
        });
    });

    document.getElementById('closeEditModal').addEventListener('click', () => editModal.classList.remove('active'));
    document.getElementById('cancelEditModal').addEventListener('click', () => editModal.classList.remove('active'));

    // === DELETE CONFIRMATION ===
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            document.getElementById('deleteMessage').textContent = `Are you sure you want to delete behavior "${name}"?`;
            document.getElementById('confirmDeleteForm').action = `/behaviors/${id}`;
            confirmModal.classList.add('active');
        });
    });

    document.getElementById('closeConfirmModal').addEventListener('click', () => confirmModal.classList.remove('active'));
    document.getElementById('cancelConfirmModal').addEventListener('click', () => confirmModal.classList.remove('active'));
});
</script>

{{-- === MODAL STYLES (same as Violation Category) === --}}
<style>
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background-color: rgba(0,0,0,0.55);
    justify-content: center;
    align-items: center;
    z-index: 1050;
}
.modal-overlay.active { display: flex; }
.modal-content {
    background: #fff;
    border-radius: 10px;
    width: 550px;
    padding: 25px 30px;
    position: relative;
    animation: fadeIn 0.25s ease-in-out;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
}
.modal-close {
    position: absolute;
    top: 12px;
    right: 15px;
    border: none;
    background: none;
    font-size: 1.8rem;
    color: #555;
    cursor: pointer;
}
.modal-header h2 {
    font-size: 1.4rem;
    font-weight: 600;
    color: #1E3A8A;
    margin-bottom: 10px;
    border-bottom: 2px solid #E5E7EB;
    padding-bottom: 5px;
}
.modal-body label {
    font-weight: 500;
    color: #333;
}
.modal-body input,
.modal-body select {
    width: 100%;
    margin-top: 5px;
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
}
.modal-body textarea {
    width: 100%;
    margin-top: 5px;
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid #ddd;
    padding-top: 15px;
}
.btn-cancel {
    background: #E5E7EB;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    cursor: pointer;
}
.btn-save {
    background: #2563EB;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    cursor: pointer;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection