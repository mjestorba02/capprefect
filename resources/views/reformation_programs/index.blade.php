@extends('layouts.app')

@section('title', 'Reformation Programs')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Reformation Program Assignment</h2>
            <p class="text-muted mb-0">Manage and assign reformation programs to students.</p>
        </div>
        <button id="openAddModal" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Add New Program
        </button>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- PROGRAMS TABLE --}}
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Reformation Programs</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Program ID</th>
                        <th>Program Name</th>
                        <th>Description</th>
                        <th>Duration</th>
                        <th>Responsible Office</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $program)
                        <tr>
                            <td>{{ $program->program_id }}</td>
                            <td>{{ $program->program_name }}</td>
                            <td>{{ $program->description ?? '-' }}</td>
                            <td>{{ $program->duration }}</td>
                            <td>{{ $program->responsible_office }}</td>
                            <td>{{ $program->type }}</td>
                            <td>
                                <span class="badge {{ $program->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $program->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary btn-view" data-program='@json($program)'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning btn-edit" data-program='@json($program)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $program->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">No programs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $programs->links() }}</div>
</div>

@include('reformation_programs.modals')

@endsection