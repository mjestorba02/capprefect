@extends('layouts.app')

@section('title', 'Disciplinary Hearing Schedule')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Disciplinary Hearing Schedule</h2>
            <p class="text-muted mb-0">Summary of all scheduled disciplinary hearings.</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i> Add Hearing
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fas fa-gavel me-2"></i>
            <h5 class="mb-0 fw-semibold">Hearing Schedule List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center align-middle">
                        <tr class="fw-semibold text-secondary">
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">Respondent</th>
                            <th style="width: 20%;">Offense</th>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 10%;">Time</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hearings as $hearing)
                            <tr class="text-center">
                                <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>
                                <td class="text-start fw-semibold text-dark">{{ $hearing->respondent->fullname ?? 'N/A' }}</td>
                                <td class="text-start">{{ $hearing->violation->category_name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($hearing->date_of_hearing)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($hearing->time)->format('h:i A') }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 
                                        @if($hearing->status == 'Completed') bg-success
                                        @elseif($hearing->status == 'Pending') bg-warning text-dark
                                        @elseif($hearing->status == 'Rescheduled') bg-info
                                        @else bg-secondary @endif">
                                        {{ $hearing->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal{{ $hearing->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $hearing->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $hearing->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Modal -->
                            <div class="modal fade" id="viewModal{{ $hearing->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">View Hearing Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Name of Respondent:</strong> {{ $hearing->respondent->fullname ?? 'N/A' }}</p>
                                            <p><strong>Position / Year & Section:</strong> {{ $hearing->respondent->year_level ?? 'N/A' }}</p>
                                            <p><strong>Nature of Offense:</strong> {{ $hearing->violation->category_name ?? 'N/A' }}</p>
                                            <p><strong>Complainant:</strong> {{ $hearing->complainant }}</p>
                                            <p><strong>Date of Hearing:</strong> {{ $hearing->date_of_hearing }}</p>
                                            <p><strong>Time:</strong> {{ $hearing->time }}</p>
                                            <p><strong>Venue:</strong> {{ $hearing->venue }}</p>
                                            <p><strong>Hearing Officer / Panel:</strong> {{ $hearing->officer_panel }}</p>
                                            <p><strong>Remarks / Status:</strong> {{ $hearing->status }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $hearing->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('hearings.update', $hearing->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title">Edit Hearing Schedule</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Name of Respondent</label>
                                                        <select name="respondent_id" class="form-select" required>
                                                            @foreach ($students as $student)
                                                                <option value="{{ $student->student_id }}" 
                                                                    {{ $hearing->respondent_id == $student->student_id ? 'selected' : '' }}>
                                                                    {{ $student->fullname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Nature of Offense</label>
                                                        <select name="violation_id" class="form-select" required>
                                                            @foreach ($violations as $violation)
                                                                <option value="{{ $violation->id }}" 
                                                                    {{ $hearing->violation_id == $violation->id ? 'selected' : '' }}>
                                                                    {{ $violation->category_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Complainant</label>
                                                        <input type="text" name="complainant" value="{{ $hearing->complainant }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Venue</label>
                                                        <input type="text" name="venue" value="{{ $hearing->venue }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Date of Hearing</label>
                                                        <input type="date" name="date_of_hearing" value="{{ $hearing->date_of_hearing }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Time</label>
                                                        <input type="time" name="time" value="{{ $hearing->time }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option {{ $hearing->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                            <option {{ $hearing->status == 'Rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                                            <option {{ $hearing->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Hearing Officer / Panel</label>
                                                        <input type="text" name="officer_panel" value="{{ $hearing->officer_panel }}" class="form-control">
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

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $hearing->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('hearings.destroy', $hearing->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this hearing record for 
                                                <strong>{{ $hearing->respondent->name ?? 'N/A' }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No hearing schedules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center py-3">
        {{ $hearings->links() }}
    </div>
</div>

<!-- Add Modal -->
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
                        <div class="col-md-6">
                            <label class="form-label">Name of Respondent</label>
                            <select name="respondent_id" id="studentSelect" class="form-select" required>
                                <option value="">Select Respondent</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->student_id }}" data-position="{{ $student->year_level }}">{{ $student->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Position / Year & Section</label>
                            <input type="text" id="studentPosition" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nature of Offense</label>
                            <select name="violation_id" class="form-select" required>
                                <option value="">Select Offense</option>
                                @foreach ($violations as $violation)
                                    <option value="{{ $violation->id }}">{{ $violation->category_name }}</option>
                                @endforeach
                            </select>
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
                            <label class="form-label">Hearing Officer / Panel</label>
                            <input type="text" name="officer_panel" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remarks / Status</label>
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
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script: Auto-display Position -->
<script>
document.getElementById('studentSelect').addEventListener('change', function () {
    const position = this.options[this.selectedIndex].getAttribute('data-position');
    document.getElementById('studentPosition').value = position || '';
});
</script>
@endsection