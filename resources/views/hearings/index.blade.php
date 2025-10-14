@extends('layouts.app')

@section('title', 'Hearing Schedules')

@section('content')
<div class="container">
    <div class="container-fluid py-4">

        {{-- === HEADER === --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Hearing Schedule Management</h2>
                <p class="text-muted mb-0">Manage and monitor all student hearing schedules efficiently.</p>
            </div>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus me-1"></i> Add Hearing
            </button>
        </div>

        {{-- === FLASH MESSAGES === --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- === HEARING TABLE === --}}
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-gavel me-2"></i> Hearing Schedule List</h5>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Record No</th>
                            <th>Respondent</th>
                            <th>Year & Section</th>
                            <th>Offense</th>
                            <th>Complainant</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Venue</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hearings as $hearing)
                            <tr>
                                <td class="fw-semibold text-muted">{{ $hearing->record_no }}</td>
                                <td>
                                    ST00{{ $hearing->respondent->id ?? $hearing->respondent_id }} -
                                    {{ $hearing->respondent->fullname ?? 'N/A' }}
                                </td>
                                <td>{{ $hearing->respondent->year_level ?? 'N/A' }}</td>
                                <td>
                                    {{ $hearing->offense ?? 'N/A' }}
                                </td>
                                <td>{{ $hearing->complainant }}</td>
                                <td>{{ \Carbon\Carbon::parse($hearing->date_of_hearing)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($hearing->time)->format('h:i A') }}</td>
                                <td>{{ $hearing->venue }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2
                                        @if($hearing->status == 'Completed') bg-success
                                        @elseif($hearing->status == 'Pending') bg-warning text-dark
                                        @elseif($hearing->status == 'Rescheduled') bg-info
                                        @else bg-secondary @endif">
                                        {{ $hearing->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning editBtn"
                                        data-id="{{ $hearing->id }}"
                                        data-respondent="{{ $hearing->respondent_id }}"
                                        data-complainant="{{ $hearing->complainant }}"
                                        data-date="{{ $hearing->date_of_hearing }}"
                                        data-time="{{ $hearing->time }}"
                                        data-venue="{{ $hearing->venue }}"
                                        data-officer="{{ $hearing->officer_panel }}"
                                        data-status="{{ $hearing->status }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('hearings.destroy', $hearing->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">No hearing records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-3">
                {{ $hearings->links() }}
            </div>
        </div>
    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('hearings.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add Hearing Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            <input type="hidden" name="violation_id" id="addViolationId">

            <div class="col-md-6">
              <label class="form-label">Respondent</label>
              <select name="respondent_id" id="addStudentSelect" class="form-select" required>
                <option value="">Select Respondent</option>
                @foreach($students as $student)
                  <option value="{{ $student->student_id }}">{{ $student->fullname }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Year & Section</label>
              <input type="text" id="addStudentYear" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nature of Offense</label>
              <input type="text" name="offense" id="addOffense" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Complainant</label>
              <input type="text" name="complainant" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Date of Hearing</label>
              <input type="date" name="date_of_hearing" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Time</label>
              <input type="time" name="time" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Venue</label>
              <input type="text" name="venue" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Officer / Panel</label>
              <input type="text" name="officer_panel" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option>Pending</option>
                <option>Rescheduled</option>
                <option>Completed</option>
              </select>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Hearing</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="editForm" method="POST">
        @csrf
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Edit Hearing Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            <input type="hidden" name="violation_id" id="editViolationId">

            <div class="col-md-6">
              <label class="form-label">Respondent</label>
              <select name="respondent_id" id="editStudentSelect" class="form-select" required>
                <option value="">Select Respondent</option>
                @foreach($students as $student)
                  <option value="{{ $student->student_id }}">{{ $student->fullname }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Year & Section</label>
              <input type="text" id="editStudentYear" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nature of Offense</label>
              <input type="text" name="offense" id="editOffense" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Complainant</label>
              <input type="text" name="complainant" id="editComplainant" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Date of Hearing</label>
              <input type="date" name="date_of_hearing" id="editDate" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Time</label>
              <input type="time" name="time" id="editTime" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Venue</label>
              <input type="text" name="venue" id="editVenue" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Officer / Panel</label>
              <input type="text" name="officer_panel" id="editOfficer" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" id="editStatus" class="form-select">
                <option>Pending</option>
                <option>Rescheduled</option>
                <option>Completed</option>
              </select>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS: improved auto-fill using named route and safe listeners -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Build a template URL using Laravel route helper (replace placeholder STUB)
    const studentInfoRouteTemplate = "{{ route('hearings.student-info', ['id' => 'STUB']) }}";

    function buildStudentInfoUrl(studentId) {
        return studentInfoRouteTemplate.replace('STUB', encodeURIComponent(studentId));
    }

    // fetch student info and fill year/offense inputs
    function fetchStudentInfoAndFill(studentId, yearInputId, offenseInputId) {
        if (!studentId) {
            const yEl = document.getElementById(yearInputId);
            const oEl = document.getElementById(offenseInputId);
            if (yEl) yEl.value = '';
            if (oEl) oEl.value = '';
            return;
        }

        const url = buildStudentInfoUrl(studentId);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                const yEl = document.getElementById(yearInputId);
                const oEl = document.getElementById(offenseInputId);
                if (yEl) yEl.value = (data.year_level && data.year_level !== 'N/A') ? data.year_level : '';
                if (oEl) oEl.value = (data.offense && data.offense !== 'N/A') ? data.offense : '';
            })
            .catch(err => {
                console.error('Error fetching student info:', err);
                const yEl = document.getElementById(yearInputId);
                const oEl = document.getElementById(offenseInputId);
                if (yEl) yEl.value = '';
                if (oEl) oEl.value = '';
            });
    }

    // --- ADD modal wiring ---
    const addStudentSelect = document.getElementById('addStudentSelect');
    if (addStudentSelect) {
        addStudentSelect.addEventListener('change', function () {
            fetchStudentInfoAndFill(this.value, 'addStudentYear', 'addOffense');
        });

        if (addStudentSelect.value) {
            fetchStudentInfoAndFill(addStudentSelect.value, 'addStudentYear', 'addOffense');
        }
    }

    // --- EDIT modal wiring ---
    const editStudentSelect = document.getElementById('editStudentSelect');
    if (editStudentSelect) {
        editStudentSelect.addEventListener('change', function () {
            fetchStudentInfoAndFill(this.value, 'editStudentYear', 'editOffense');
        });
    }

    // --- Edit Modal population ---
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const editForm = document.getElementById('editForm');

            if (editForm) {
                // ✅ Use correct parameter name (hearing) to match route-model binding
                const updateRouteTemplate = "{{ route('hearings.update', ['hearing' => '__ID__']) }}";
                editForm.action = updateRouteTemplate.replace('__ID__', id);
            }

            // Populate modal fields
            const respondent = this.dataset.respondent || '';
            const complainant = this.dataset.complainant || '';
            const date = this.dataset.date || '';
            const time = this.dataset.time || '';
            const venue = this.dataset.venue || '';
            const officer = this.dataset.officer || '';
            const status = this.dataset.status || '';

            if (document.getElementById('editStudentSelect')) document.getElementById('editStudentSelect').value = respondent;
            if (document.getElementById('editComplainant')) document.getElementById('editComplainant').value = complainant;
            if (document.getElementById('editDate')) document.getElementById('editDate').value = date;
            if (document.getElementById('editTime')) document.getElementById('editTime').value = time;
            if (document.getElementById('editVenue')) document.getElementById('editVenue').value = venue;
            if (document.getElementById('editOfficer')) document.getElementById('editOfficer').value = officer;
            if (document.getElementById('editStatus')) document.getElementById('editStatus').value = status;

            // Auto-fill fields based on respondent selection
            if (respondent) {
                fetchStudentInfoAndFill(respondent, 'editStudentYear', 'editOffense');
            } else {
                const ey = document.getElementById('editStudentYear');
                const eo = document.getElementById('editOffense');
                if (ey) ey.value = '';
                if (eo) eo.value = '';
            }
        });
    });

    // --- Modal cancel / close button fix ---
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modal = bootstrap.Modal.getInstance(openModal);
                if (modal) modal.hide();
            }
        });
    });
});
</script>
@endsection