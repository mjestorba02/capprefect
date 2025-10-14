@extends('layouts.app')

@section('title', 'Sanctions Management')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Sanctions Management</h2>
            <p class="text-muted mb-0">Monitor and manage disciplinary sanctions assigned to students.</p>
        </div>
        <button id="openAddModal" type="button" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Add Sanction
        </button>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- === STUDENT TABLE === --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-gradient bg-primary text-white fw-semibold">
            <i class="fas fa-users me-2"></i> Student List
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student ID</th>
                        <th>Fullname</th>
                        <th>Program</th>
                        <th>Year Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>ST00{{ $student->student_id }}</td>
                            <td>{{ $student->fullname }}</td>
                            <td>{{ $student->program }}</td>
                            <td>{{ $student->year_level }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $student->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- === SANCTION TABLE === --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient bg-danger text-white fw-semibold">
            <i class="fas fa-exclamation-triangle me-2"></i> Sanction Records
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sanction ID</th>
                        <th>Student Name</th>
                        <th>Offense</th>
                        <th>Date Issued</th>
                        <th>Sanction Type</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sanctions as $sanction)
                        <tr>
                            <td>SNC00{{ $sanction->sanction_id }}</td>
                            <td>{{ $sanction->student->fullname ?? 'N/A' }}</td>
                            <td>{{ $sanction->offense }}</td>
                            <td>{{ \Carbon\Carbon::parse($sanction->date_issued)->format('M d, Y') }}</td>
                            <td>{{ $sanction->sanction_type }}</td>
                            <td>
                                <span class="badge rounded-pill 
                                    @if($sanction->severity == 'Minor') bg-success 
                                    @elseif($sanction->severity == 'Moderate') bg-warning text-dark 
                                    @else bg-danger @endif">
                                    {{ ucfirst($sanction->severity) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $sanction->status == 'resolved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($sanction->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                    class="btn btn-sm btn-outline-primary btn-edit"
                                    data-id="{{ $sanction->sanction_id }}"
                                    data-student="{{ $sanction->student_id }}"
                                    data-offense="{{ $sanction->offense }}"
                                    data-date="{{ $sanction->date_issued }}"
                                    data-type="{{ $sanction->sanction_type }}"
                                    data-severity="{{ $sanction->severity }}"
                                    data-status="{{ $sanction->status }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" 
                                    class="btn btn-sm btn-outline-danger btn-delete"
                                    data-id="{{ $sanction->sanction_id }}"
                                    data-offense="{{ $sanction->offense }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">No sanctions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $sanctions->links() }}
    </div>
</div>

{{-- === ADD SANCTION MODAL === --}}
<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeAddModal">&times;</button>
        <div class="modal-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Add Sanction</h5>
        </div>
        <form action="{{ route('sanctions.store') }}" method="POST">
            @csrf
            <div class="modal-body">

                {{-- YEAR LEVEL --}}
                <div class="mb-3">
                    <label class="form-label">Year Level</label>
                    <select id="year_level" class="form-select" required>
                        <option value="">-- Select Year Level --</option>
                        @foreach($students->pluck('year_level')->unique() as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- PROGRAM --}}
                <div class="mb-3">
                    <label class="form-label">Program</label>
                    <select id="program" class="form-select" required disabled>
                        <option value="">-- Select Program --</option>
                    </select>
                </div>

                {{-- STUDENT --}}
                <div class="mb-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" id="student_id" class="form-select" required disabled>
                        <option value="">-- Select Student --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Offense</label>
                    <select name="offense" id="offense" class="form-select" required>
                        <option value="">-- Select Violation Category --</option>
                        @foreach($violationCategories as $category)
                            <option value="{{ $category->category_name }}">
                                {{ $category->category_name }} ({{ ucfirst($category->severity_level) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date Issued</label>
                    <input type="date" name="date_issued" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sanction Type</label>
                    <input type="text" name="sanction_type" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Severity</label>
                    <select name="severity" class="form-select" required>
                        <option value="Minor">Minor</option>
                        <option value="Moderate">Moderate</option>
                        <option value="Severe">Severe</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="resolved">Resolved</option>
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

{{-- === EDIT SANCTION MODAL (MATCHED STYLE) === --}}
<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeEditModal">&times;</button>
        <div class="modal-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Sanction</h5>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">

                {{-- YEAR LEVEL --}}
                <div class="mb-3">
                    <label class="form-label">Year Level</label>
                    <select id="edit_year_level" class="form-select" required>
                        <option value="">-- Select Year Level --</option>
                        @foreach($students->pluck('year_level')->unique() as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- PROGRAM --}}
                <div class="mb-3">
                    <label class="form-label">Program</label>
                    <select id="edit_program" class="form-select" required disabled>
                        <option value="">-- Select Program --</option>
                    </select>
                </div>

                {{-- STUDENT --}}
                <div class="mb-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" id="edit_student_id" class="form-select" required disabled>
                        <option value="">-- Select Student --</option>
                    </select>
                </div>

                {{-- OFFENSE --}}
                <div class="mb-3">
                    <label class="form-label">Offense</label>
                    <select name="offense" id="edit_offense" class="form-select" required>
                        <option value="">-- Select Violation Category --</option>
                        @foreach($violationCategories as $category)
                            <option value="{{ $category->category_name }}">
                                {{ $category->category_name }} ({{ ucfirst($category->severity_level) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date Issued</label>
                    <input type="date" id="edit_date_issued" name="date_issued" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sanction Type</label>
                    <input type="text" id="edit_sanction_type" name="sanction_type" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Severity</label>
                    <select id="edit_severity" name="severity" class="form-select" required>
                        <option value="Minor">Minor</option>
                        <option value="Moderate">Moderate</option>
                        <option value="Severe">Severe</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select id="edit_status" name="status" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelEditModal" class="btn btn-light">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Update</button>
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

{{-- === SCRIPTS === --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const openModal = (m) => { if (!m) return; m.classList.add('active'); document.body.style.overflow = 'hidden'; };
    const closeModal = (m) => { if (!m) return; m.classList.remove('active'); document.body.style.overflow = ''; };

    // === ADD SANCTION MODAL ===
    const addModal = document.getElementById('addModal');
    const openAddModalBtn = document.getElementById('openAddModal');
    const closeAddModalBtn = document.getElementById('closeAddModal');
    const cancelAddModalBtn = document.getElementById('cancelAddModal');

    if (openAddModalBtn && addModal) {
        openAddModalBtn.addEventListener('click', () => openModal(addModal));
    }
    if (closeAddModalBtn && addModal) {
        closeAddModalBtn.addEventListener('click', () => closeModal(addModal));
    }
    if (cancelAddModalBtn && addModal) {
        cancelAddModalBtn.addEventListener('click', () => closeModal(addModal));
    }
    if (addModal) {
        addModal.addEventListener('click', (e) => {
            if (e.target === addModal) closeModal(addModal);
        });
    }

    // === Existing logic for dynamic dropdowns (Add modal) ===
    const students = @json($students);
    const yearSelect = document.getElementById('year_level');
    const programSelect = document.getElementById('program');
    const studentSelect = document.getElementById('student_id');

    if (yearSelect && programSelect && studentSelect) {
        yearSelect.addEventListener('change', function () {
            const selectedYear = this.value;
            programSelect.innerHTML = '<option value="">-- Select Program --</option>';
            studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
            studentSelect.disabled = true;

            if (!selectedYear) {
                programSelect.disabled = true;
                return;
            }

            const programs = [...new Set(students
                .filter(s => s.year_level === selectedYear)
                .map(s => s.program))];

            programs.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p;
                opt.textContent = p;
                programSelect.appendChild(opt);
            });

            programSelect.disabled = false;
        });

        programSelect.addEventListener('change', function () {
            const selectedYear = yearSelect.value;
            const selectedProgram = this.value;
            studentSelect.innerHTML = '<option value="">-- Select Student --</option>';

            if (!selectedProgram) {
                studentSelect.disabled = true;
                return;
            }

            const filteredStudents = students.filter(
                s => s.year_level === selectedYear && s.program === selectedProgram
            );

            filteredStudents.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.student_id;
                opt.textContent = s.fullname;
                studentSelect.appendChild(opt);
            });

            studentSelect.disabled = false;
        });
    }

    // === EDIT MODAL ELEMENTS ===
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const editYearSelect = document.getElementById('edit_year_level');
    const editProgramSelect = document.getElementById('edit_program');
    const editStudentSelect = document.getElementById('edit_student_id');
    const closeEditBtn = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditModal');

    // safe-guard: only attach edit modal listeners if edit modal exists
    if (editModal) {
        if (closeEditBtn) closeEditBtn.addEventListener('click', () => closeModal(editModal));
        if (cancelEditBtn) cancelEditBtn.addEventListener('click', () => closeModal(editModal));
        editModal.addEventListener('click', (e) => { if (e.target === editModal) closeModal(editModal); });
    }

    // === EDIT YEAR-PROGRAM-STUDENT ===
    if (editYearSelect && editProgramSelect && editStudentSelect && Array.isArray(students)) {
        editYearSelect.addEventListener('change', function () {
            const selectedYear = this.value;
            editProgramSelect.innerHTML = '<option value="">-- Select Program --</option>';
            editStudentSelect.innerHTML = '<option value="">-- Select Student --</option>';
            editStudentSelect.disabled = true;

            if (!selectedYear) {
                editProgramSelect.disabled = true;
                return;
            }

            const programs = [...new Set(students
                .filter(s => s.year_level === selectedYear)
                .map(s => s.program))];

            programs.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p;
                opt.textContent = p;
                editProgramSelect.appendChild(opt);
            });

            editProgramSelect.disabled = false;
        });

        editProgramSelect.addEventListener('change', function () {
            const selectedYear = editYearSelect.value;
            const selectedProgram = this.value;
            editStudentSelect.innerHTML = '<option value="">-- Select Student --</option>';

            if (!selectedProgram) {
                editStudentSelect.disabled = true;
                return;
            }

            const filteredStudents = students.filter(
                s => s.year_level === selectedYear && s.program === selectedProgram
            );

            filteredStudents.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.student_id;
                opt.textContent = s.fullname;
                editStudentSelect.appendChild(opt);
            });

            editStudentSelect.disabled = false;
        });
    }

    // === Modify Edit Modal Button Event ===
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            // ensure edit elements exist
            if (editYearSelect && editProgramSelect && editStudentSelect) {
                const studentData = students.find(s => s.student_id == btn.dataset.student);

                if (studentData) {
                    editYearSelect.value = studentData.year_level;
                    editYearSelect.dispatchEvent(new Event('change'));

                    // small delay to let program options populate
                    setTimeout(() => {
                        editProgramSelect.value = studentData.program;
                        editProgramSelect.dispatchEvent(new Event('change'));

                        setTimeout(() => {
                            editStudentSelect.value = studentData.student_id;
                        }, 150);
                    }, 150);
                }
            }

            // populate other fields (guarded)
            const editOff = document.getElementById('edit_offense');
            const editDate = document.getElementById('edit_date_issued');
            const editSType = document.getElementById('edit_sanction_type');
            const editSev = document.getElementById('edit_severity');
            const editStat = document.getElementById('edit_status');

            if (editOff) editOff.value = btn.dataset.offense || '';
            if (editDate) editDate.value = btn.dataset.date || '';
            if (editSType) editSType.value = btn.dataset.type || '';
            if (editSev) editSev.value = btn.dataset.severity || '';
            if (editStat) editStat.value = btn.dataset.status || '';
            if (editForm) editForm.action = `/sanctions/${btn.dataset.id}`;

            if (editModal) openModal(editModal);
        });
    });

    // DELETE MODAL
    const confirmModal = document.getElementById('confirmDeleteModal');
    const confirmForm = document.getElementById('confirmDeleteForm');
    const deleteMessage = document.getElementById('deleteMessage');
    const closeConfirmBtn = document.getElementById('closeConfirmModal');
    const cancelConfirmBtn = document.getElementById('cancelConfirmModal');

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            if (deleteMessage) deleteMessage.textContent = `Are you sure you want to delete "${btn.dataset.offense}" (ID: ${btn.dataset.id})?`;
            if (confirmForm) confirmForm.action = `/sanctions/${btn.dataset.id}`;
            if (confirmModal) openModal(confirmModal);
        });
    });

    if (closeConfirmBtn && confirmModal) closeConfirmBtn.addEventListener('click', () => closeModal(confirmModal));
    if (cancelConfirmBtn && confirmModal) cancelConfirmBtn.addEventListener('click', () => closeModal(confirmModal));
    if (confirmModal) confirmModal.addEventListener('click', e => { if (e.target === confirmModal) closeModal(confirmModal); });
});
</script>

{{-- === STYLES === --}}
<style>
.modal-overlay {
    display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.65);
    justify-content: center; align-items: center; z-index: 9999;
}
.modal-overlay.active { display: flex; }
.modal-content {
    background: #fff; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.35);
    width: 600px; max-height: 90vh; overflow-y: auto; position: relative; animation: fadeIn .25s ease-in-out;
}
.modal-close {
    position: absolute; top: 10px; right: 15px; font-size: 1.8rem; color: #888;
    background: none; border: none; cursor: pointer;
}
.modal-header { padding: 15px 20px; border-bottom: 1px solid #ddd; border-radius: 12px 12px 0 0; }
.modal-body { padding: 20px; }
.modal-footer {
    padding: 15px 20px; border-top: 1px solid #ddd; background-color: #f9fafb;
    display: flex; justify-content: flex-end; gap: 10px;
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection