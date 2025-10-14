@extends('layouts.app')

@section('title', 'Parent Notification Tool')

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">
                <i class="fas fa-phone-alt me-2"></i> Parent Notification Tool
            </h2>
            <p class="text-muted mb-0">Notify parents of students with existing sanctions or disciplinary actions.</p>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list-alt me-2"></i> Students with Sanctions
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Violation</th>
                        <th>Status</th>
                        <th>Parent Email</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentsWithCases as $index => $student)
                        @foreach($student->sanctions as $sanction)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $student->fullname }}</td>
                                <td>{{ $sanction->offense }}</td>
                                <td>
                                    <span class="badge rounded-pill 
                                        {{ strtolower($sanction->status) == 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($sanction->status) }}
                                    </span>
                                </td>
                                <td>{{ $student->parent_email ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <button 
                                        class="btn btn-sm btn-outline-primary notify-btn"
                                        data-student="{{ $student->fullname }}"
                                        data-parent="{{ $student->parent_email }}"
                                        data-violation="{{ $sanction->offense }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#notifyModal">
                                        <i class="fas fa-envelope me-1"></i> Notify
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="fas fa-info-circle me-1"></i> No students with sanctions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="notifyModal" tabindex="-1" aria-labelledby="notifyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="notifyModalLabel">Notify Parent</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('notifications.parents.notify') }}">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="student_name" id="studentName">
          <input type="hidden" name="parent_email" id="parentEmail">
          <input type="hidden" name="violation" id="violationName">

          <div class="mb-3">
            <label class="form-label">Parent Email</label>
            <input type="email" class="form-control" id="displayParentEmail" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="4" placeholder="Write your message to the parent..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Send Notification</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.notify-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('studentName').value = btn.dataset.student;
        document.getElementById('parentEmail').value = btn.dataset.parent;
        document.getElementById('violationName').value = btn.dataset.violation;
        document.getElementById('displayParentEmail').value = btn.dataset.parent;
    });
});
</script>
@endsection