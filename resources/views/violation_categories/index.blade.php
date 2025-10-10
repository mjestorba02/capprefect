@extends('layouts.app')

@section('title', 'Violation Category Setup')

@section('content')
<div class="container">
    <div class="container-fluid py-4">

        {{-- === HEADER === --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Violation Category Setup</h2>
                <p class="text-muted mb-0">Manage all violation categories and their corresponding sanctions.</p>
            </div>
            <button id="openAddModal" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Add Category
            </button>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- === VIOLATION CATEGORY TABLE === --}}
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Violation Categories</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Severity Level</th>
                            <th>Default Sanction</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="fw-semibold">{{ $category->category_id }}</td>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ $category->description }}</td>
                                <td>
                                    <span class="badge 
                                        @if($category->severity_level == 'High') bg-danger
                                        @elseif($category->severity_level == 'Medium') bg-warning text-dark
                                        @else bg-secondary
                                        @endif">
                                        {{ $category->severity_level }}
                                    </span>
                                </td>
                                <td>{{ $category->default_sanction }}</td>
                                <td>
                                    <span class="badge {{ $category->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $category->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-danger btn-delete"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->category_name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">No categories found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>
</div>

{{-- === ADD CATEGORY MODAL === --}}
<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeAddModal">&times;</button>

        <div class="modal-header">
            <h2>Add Violation Category</h2>
        </div>

        <form action="{{ route('violation_categories.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div>
                    <label for="category_id">Category ID</label>
                    <input type="text" id="category_id" name="category_id" required>
                </div>

                <div>
                    <label for="category_name">Category Name</label>
                    <input type="text" id="category_name" name="category_name" required>
                </div>

                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>

                <div>
                    <label for="severity_level">Severity Level</label>
                    <select id="severity_level" name="severity_level" required>
                        <option value="">-- Select Level --</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>

                <div>
                    <label for="default_sanction">Default Sanction</label>
                    <input type="text" id="default_sanction" name="default_sanction" required>
                </div>

                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="cancelAddModal" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
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
            <p id="deleteMessage">Are you sure you want to delete this category?</p>
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

{{-- === MODAL SCRIPT === --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addModal = document.getElementById('addModal');
    const confirmModal = document.getElementById('confirmDeleteModal');

    // === Add Modal Logic ===
    document.getElementById('openAddModal').addEventListener('click', () => addModal.classList.add('active'));
    document.getElementById('closeAddModal').addEventListener('click', () => addModal.classList.remove('active'));
    document.getElementById('cancelAddModal').addEventListener('click', () => addModal.classList.remove('active'));

    // === Delete Confirmation ===
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name || 'this category';
            document.getElementById('deleteMessage').textContent = `Are you sure you want to delete "${name}"?`;
            document.getElementById('confirmDeleteForm').action = `/violation_categories/${id}`;
            confirmModal.classList.add('active');
        });
    });

    document.getElementById('closeConfirmModal').addEventListener('click', () => confirmModal.classList.remove('active'));
    document.getElementById('cancelConfirmModal').addEventListener('click', () => confirmModal.classList.remove('active'));
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