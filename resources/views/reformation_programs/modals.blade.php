{{-- ADD PROGRAM MODAL --}}
<div class="modal fade" id="addProgramModal" tabindex="-1" aria-labelledby="addProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New Program</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reformation_programs.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Program ID</label>
                            <input type="text" name="program_id" class="form-control" value="RP{{ str_pad($nextId, 3, '0', STR_PAD_LEFT) }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Name</label>
                            <input type="text" name="program_name" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Duration</label>
                            <input type="text" name="duration" class="form-control" placeholder="e.g., 3 months" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Responsible Office</label>
                            <input type="text" name="responsible_office" class="form-control" placeholder="e.g., Guidance Office" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select type</option>
                                <option value="Behavioral">Behavioral</option>
                                <option value="Educational">Educational</option>
                                <option value="Community Service">Community Service</option>
                                <option value="Counseling">Counseling</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VIEW PROGRAM MODAL --}}
<div class="modal fade" id="viewProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-eye me-2"></i>Program Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-md-4">Program ID</dt>
                    <dd class="col-md-8" id="viewProgramId"></dd>

                    <dt class="col-md-4">Program Name</dt>
                    <dd class="col-md-8" id="viewProgramName"></dd>

                    <dt class="col-md-4">Description</dt>
                    <dd class="col-md-8" id="viewDescription"></dd>

                    <dt class="col-md-4">Duration</dt>
                    <dd class="col-md-8" id="viewDuration"></dd>

                    <dt class="col-md-4">Responsible Office</dt>
                    <dd class="col-md-8" id="viewOffice"></dd>

                    <dt class="col-md-4">Type</dt>
                    <dd class="col-md-8" id="viewType"></dd>

                    <dt class="col-md-4">Status</dt>
                    <dd class="col-md-8" id="viewStatus"></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- EDIT PROGRAM MODAL --}}
<div class="modal fade" id="editProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Program</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProgramForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Program ID</label>
                            <input type="text" id="editProgramId" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Name</label>
                            <input type="text" name="program_name" id="editProgramName" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Duration</label>
                            <input type="text" name="duration" id="editDuration" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Responsible Office</label>
                            <input type="text" name="responsible_office" id="editOffice" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" id="editType" class="form-select" required>
                                <option value="Behavioral">Behavioral</option>
                                <option value="Educational">Educational</option>
                                <option value="Community Service">Community Service</option>
                                <option value="Counseling">Counseling</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="editStatus" class="form-select">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i> Update Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal fade" id="deleteProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-trash me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteProgramForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this reformation program?</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-1"></i> Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.getElementById("openAddModal");
    const addModal = new bootstrap.Modal(document.getElementById("addProgramModal"));
    const viewModal = new bootstrap.Modal(document.getElementById("viewProgramModal"));
    const editModal = new bootstrap.Modal(document.getElementById("editProgramModal"));
    const deleteModal = new bootstrap.Modal(document.getElementById("deleteProgramModal"));

    // Open add modal
    addBtn?.addEventListener("click", () => addModal.show());

    // View Program
    document.querySelectorAll(".btn-view").forEach(btn => {
        btn.addEventListener("click", () => {
            const data = JSON.parse(btn.dataset.program);
            document.getElementById("viewProgramId").innerText = data.program_id;
            document.getElementById("viewProgramName").innerText = data.program_name;
            document.getElementById("viewDescription").innerText = data.description ?? '-';
            document.getElementById("viewDuration").innerText = data.duration;
            document.getElementById("viewOffice").innerText = data.responsible_office;
            document.getElementById("viewType").innerText = data.type;
            document.getElementById("viewStatus").innerText = data.status;
            viewModal.show();
        });
    });

    // Edit Program
    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.addEventListener("click", () => {
            const data = JSON.parse(btn.dataset.program);
            document.getElementById("editProgramForm").action = `/reformation_programs/${data.id}`;
            document.getElementById("editProgramId").value = data.program_id;
            document.getElementById("editProgramName").value = data.program_name;
            document.getElementById("editDescription").value = data.description ?? '';
            document.getElementById("editDuration").value = data.duration;
            document.getElementById("editOffice").value = data.responsible_office;
            document.getElementById("editType").value = data.type;
            document.getElementById("editStatus").value = data.status;
            editModal.show();
        });
    });

    // Delete Program
    document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            document.getElementById("deleteProgramForm").action = `/reformation_programs/${id}`;
            deleteModal.show();
        });
    });
});
</script>