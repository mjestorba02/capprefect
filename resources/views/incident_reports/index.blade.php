@extends('layouts.app')

@section('title', 'Incident Reports')

@section('content')
<div class="container">
    <div class="container-fluid py-4">

        {{-- === HEADER === --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Incident Report Generation</h2>
                <p class="text-muted mb-0">Manage and track all reported incidents efficiently.</p>
            </div>
            <button id="openAddModal" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Add New Report
            </button>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- === FILTERS === --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <form method="GET" action="{{ route('incident_reports.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="from" class="form-label">From</label>
                        <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to" class="form-label">To</label>
                        <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->category_name }} ({{ ucfirst($cat->severity_level) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('incident_reports.export', request()->query()) }}" class="btn btn-danger flex-fill">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a>
                        <a href="{{ route('incident_reports.index') }}" class="btn btn-secondary flex-fill">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- === INCIDENT REPORTS TABLE === --}}
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i> Incident Reports</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Incident ID</th>
                            <th>Student</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td class="fw-semibold">{{ $report->incident_id }}</td>
                            <td>
                                ST00{{ $report->student->id ?? $report->student_id }} -
                                {{ $report->student->fullname ?? 'Unknown Student' }}
                            </td>
                            <td>
                                VC00{{ $report->category->sanction_id ?? $report->category_id }} -
                                {{ $report->category->offense ?? 'Unknown Category' }}
                            </td>
                            <td>{{ $report->incident_date }}</td>
                            <td>{{ $report->location }}</td>
                            <td>
                                <span class="badge 
                                    @if($report->status == 'Under Investigation') bg-warning text-dark 
                                    @elseif($report->status == 'Resolved') bg-success
                                    @else bg-secondary @endif">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary btn-view" data-report='@json($report)'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning btn-edit" data-report='@json($report)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $report->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">No incident reports found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $reports->links() }}</div>
    </div>
</div>

{{-- === VIEW MODAL === --}}
<div id="viewModal" class="modal-overlay">
    <div class="modal-content" style="max-width:600px;">
        <button type="button" class="modal-close" id="closeViewModal">&times;</button>
        <div class="modal-header">
            <h2>View Incident Details</h2>
        </div>
        <div class="modal-body">
            <p><strong>Incident ID:</strong> <span id="view_incident_id"></span></p>
            <p><strong>Student:</strong> <span id="view_student"></span></p>
            <p><strong>Category:</strong> <span id="view_category"></span></p>
            <p><strong>Date:</strong> <span id="view_date"></span></p>
            <p><strong>Location:</strong> <span id="view_location"></span></p>
            <p><strong>Description:</strong> <span id="view_description"></span></p>
            <p><strong>Reported By:</strong> <span id="view_reported_by"></span></p>
            <p><strong>Action Taken:</strong> <span id="view_action_taken"></span></p>
            <p><strong>Status:</strong> <span id="view_status"></span></p>
        </div>
        <div class="modal-footer">
            <button type="button" id="closeViewBtn" class="btn-cancel">Close</button>
        </div>
    </div>
</div>

{{-- === ADD MODAL === --}}
<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeAddModal">&times;</button>
        <div class="modal-header">
            <h2>Add Incident Report</h2>
        </div>
        <form id="addForm" action="{{ route('incident_reports.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <label>Incident ID</label>
                <input type="text" name="incident_id" value="{{ 'IR' . str_pad($nextId, 3, '0', STR_PAD_LEFT) }}" readonly>

                <label>Student</label>
                <select name="student_id" required>
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->student_id }}">{{ $student->student_id }} - {{ $student->fullname }}</option>
                    @endforeach
                </select>

                <label>Violation Category</label>
                <select name="category_id" required>
                    <option value="">Select Violation Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }} ({{ ucfirst($cat->severity_level) }})</option>
                    @endforeach
                </select>

                <label>Date</label>
                <input type="date" name="incident_date" required>

                <label>Location</label>
                <input type="text" name="location" required>

                <label>Reported By</label>
                <input type="text" name="reported_by" required>

                <label>Description</label>
                <textarea name="description"></textarea>

                <label>Action Taken</label>
                <textarea name="action_taken"></textarea>

                <label>Status</label>
                <select name="status" required>
                    <option value="Under Investigation">Under Investigation</option>
                    <option value="Resolved">Resolved</option>
                    <option value="Dismissed">Dismissed</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelAddModal" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- === EDIT MODAL === --}}
<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeEditModal">&times;</button>
        <div class="modal-header">
            <h2>Edit Incident Report</h2>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <label>Incident ID</label>
                <input type="text" id="edit_incident_id" name="incident_id" readonly>

                <label>Student</label>
                <select id="edit_student_id" name="student_id" required>
                    @foreach($students as $student)
                        <option value="{{ $student->student_id }}">{{ $student->student_id }} - {{ $student->fullname }}</option>
                    @endforeach
                </select>

                <label>Category</label>
                <select id="edit_category_id" name="category_id" required>
                    <option value="">Select Violation Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }} ({{ ucfirst($cat->severity_level) }})</option>
                    @endforeach
                </select>

                <label>Date</label>
                <input type="date" id="edit_incident_date" name="incident_date" required>

                <label>Location</label>
                <input type="text" id="edit_location" name="location" required>

                <label>Reported By</label>
                <input type="text" id="edit_reported_by" name="reported_by" required>

                <label>Description</label>
                <textarea id="edit_description" name="description"></textarea>

                <label>Action Taken</label>
                <textarea id="edit_action_taken" name="action_taken"></textarea>

                <label>Status</label>
                <select id="edit_status" name="status">
                    <option value="Under Investigation">Under Investigation</option>
                    <option value="Resolved">Resolved</option>
                    <option value="Dismissed">Dismissed</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelEditModal" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- === DELETE MODAL === --}}
<div id="deleteModal" class="modal-overlay">
    <div class="modal-content" style="max-width:400px;">
        <div class="modal-header">
            <h2>Confirm Deletion</h2>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this incident report?</p>
        </div>
        <div class="modal-footer">
            <button type="button" id="cancelDeleteModal" class="btn-cancel">Cancel</button>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn-save" style="background:#dc2626;">Delete</button>
            </form>
        </div>
    </div>
</div>

{{-- === JS LOGIC === --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const get = id => document.getElementById(id);

    // --- Add Modal ---
    get('openAddModal').onclick = () => get('addModal').classList.add('active');
    get('closeAddModal').onclick = () => get('addModal').classList.remove('active');
    get('cancelAddModal').onclick = () => get('addModal').classList.remove('active');

    // --- Edit Modal ---
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.onclick = () => {
            const r = JSON.parse(btn.dataset.report);
            get('editForm').action = `/incident_reports/${r.id}`;
            get('edit_incident_id').value = r.incident_id;
            get('edit_student_id').value = r.student_id;
            get('edit_category_id').value = r.category_id;
            get('edit_incident_date').value = r.incident_date;
            get('edit_location').value = r.location;
            get('edit_reported_by').value = r.reported_by;
            get('edit_description').value = r.description || '';
            get('edit_action_taken').value = r.action_taken || '';
            get('edit_status').value = r.status;
            get('editModal').classList.add('active');
        };
    });
    get('closeEditModal').onclick = () => get('editModal').classList.remove('active');
    get('cancelEditModal').onclick = () => get('editModal').classList.remove('active');

    // --- View Modal ---
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.onclick = () => {
            const r = JSON.parse(btn.dataset.report);

            // Handle nested relationships and proper formatting
            get('view_incident_id').textContent = r.incident_id ?? '-';
            get('view_student').textContent =
                r.student?.fullname
                    ? `ST00${r.student.student_id} - ${r.student.fullname}`
                    : (r.student_id ? `ST00${r.student_id}` : 'Unknown Student');
            get('view_category').textContent =
                r.category?.offense
                    ? `VC00${r.category.sanction_id} - ${r.category.offense}`
                    : (r.category_id ? `VC00${r.category_id}` : 'Unknown Category');
            get('view_date').textContent = r.incident_date
                ? new Date(r.incident_date).toLocaleDateString()
                : '-';
            get('view_location').textContent = r.location ?? '-';
            get('view_description').textContent = r.description ?? '-';
            get('view_reported_by').textContent = r.reported_by ?? '-';
            get('view_action_taken').textContent = r.action_taken ?? '-';
            get('view_status').textContent = r.status ?? '-';

            get('viewModal').classList.add('active');
        };
    });
    get('closeViewModal')?.addEventListener('click', () => get('viewModal').classList.remove('active'));
    get('closeViewBtn')?.addEventListener('click', () => get('viewModal').classList.remove('active'));

    // --- Delete Modal ---
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.onclick = () => {
            get('deleteForm').action = `/incident_reports/${btn.dataset.id}`;
            get('deleteModal').classList.add('active');
        };
    });
    get('cancelDeleteModal').onclick = () => get('deleteModal').classList.remove('active');
});
</script>

{{-- === MODAL STYLES === --}}
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
    width: 600px;
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
.modal-body select,
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