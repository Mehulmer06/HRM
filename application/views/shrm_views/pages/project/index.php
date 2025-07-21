<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php'); ?>
<style>
    .is-invalid {
        border-color: #dc3545;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-folder-open"></i>
                Project Management
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> / <span
                        class="text-muted">Project Management</span>
            </nav>
        </div>
		<a href="<?= base_url('shrm/dashboard') ?>" class="btn btn-secondary">
			<i class="fas fa-arrow-left me-2"></i>
			Back to List
		</a>
    </div>
</div>

<?php
// Get session data
$role = $this->session->userdata('role');
$category = $this->session->userdata('category');
$userId = $this->session->userdata('user_id');

// Check if user has permission to add projects (same logic as activity)
if (
    (
        ($role === 'e' && $category === 'e') ||
        ($role === 'admin') ||
        ($role === 'employee' && $category === 'admin')
    )
) {
    ?>
    <!-- Project Form Section -->
    <div class="form-card">
        <div class="form-section-title">
            <i class="fas fa-plus"></i>
            Add New Project
        </div>

        <form action="<?= base_url('project/store') ?>" method="post" id="projectForm">
            <?php
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
            <input type="hidden" id="project_id" name="project_id" value="">

            <div class="row form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="project_name">Project Name</label>
                        <input type="text"
                               class="form-control <?= form_error('project_name') ? 'is-invalid' : '' ?>"
                               name="project_name"
                               id="project_name"
                               placeholder="Enter project name"
                               value="<?= set_value('project_name') ?>">
                        <?= form_error('project_name', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-select <?= form_error('status') ? 'is-invalid' : '' ?>"
                                name="status"
                                id="status">
                            <option value="">Select Status</option>
                            <option value="active" <?= set_select('status', 'active') ?>>Active</option>
                            <option value="inactive" <?= set_select('status', 'inactive') ?>>Inactive</option>
                        </select>
                        <?= form_error('status', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-primary me-2" id="submitBtn">
                            <i class="fas fa-save me-1"></i> <span id="submitText">Save Project</span>
                        </button>
                        <button type="button" class="btn btn-secondary" id="resetBtn">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } ?>

<!-- Projects Table Section -->
<div class="out-of-office-section">
    <h2 class="section-title">
        <i class="fas fa-list"></i>
        Projects List
    </h2>

    <div class="table-container">
        <table id="projectsTable" class="table-modern table table-striped table-hover">
            <thead>
            <tr>
                <th>S.No</th>
                <th>Project Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($projects)): ?>
                <?php $i = 1; ?>
                <?php foreach ($projects as $project): ?>
                    <?php if ($project['deleted_at'] == null): // Only show non-deleted projects ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td>
                                <strong class="staff-name"><?= htmlspecialchars($project['project_name']) ?></strong>
                            </td>
                            <td>
                                <?php if ($project['status'] == 'active'): ?>
                                    <span class="status-badge status-active">Active</span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $project['created_at'] ? date('M d, Y H:i', strtotime($project['created_at'])) : '-' ?></td>
                            <td><?= $project['updated_at'] ? date('M d, Y H:i', strtotime($project['updated_at'])) : '-' ?></td>
                            <td>
                                <?php if (
                                    ($role === 'e' && $category === 'e') ||
                                    ($role === 'admin') ||
                                    ($role === 'employee' && $category === 'admin')
                                ): ?>
                                    <!-- Edit Button -->
                                    <button type="button"
                                            class="action-btn btn-edit editProjectBtn"
                                            data-id="<?= $project['encrypted_id'] ?>"
                                            title="Edit Project">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button"
                                            class="action-btn btn-toggle deleteProjectBtn"
                                            data-id="<?= $project['encrypted_id'] ?>"
                                            data-name="<?= htmlspecialchars($project['project_name']) ?>"
                                            title="Delete Project">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_project_name" class="form-label">Project Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit_project_name" name="edit_project_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="edit_status" name="edit_status" required>
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-primary" id="updateProjectBtn">Update Project</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-labelledby="deleteProjectModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('project/delete') ?>" method="post">
                <?php
                $csrf = array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                );
                ?>
                <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>

                <div class="modal-header">
                    <h5 class="modal-title" id="deleteProjectModalLabel">Delete Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delete_project_id" name="project_id">
                    <p>Are you sure you want to delete the project "<strong id="delete_project_name"></strong>"?</p>
                    <p class="text-muted">This action can be undone by restoring the project later.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="action-btn btn-toggle">Delete Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    $(document).ready(function () {
        let isEditMode = false;
        let editingProjectId = null;

        // Form validation
        $("#projectForm").validate({
            rules: {
                project_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 255
                },
                status: {
                    required: true
                }
            },
            messages: {
                project_name: {
                    required: "Please enter project name.",
                    minlength: "Project name must be at least 2 characters.",
                    maxlength: "Project name cannot exceed 255 characters."
                },
                status: {
                    required: "Please select a status."
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger small d-block mt-1',
            highlight: function (element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                const $submitBtn = $('#submitBtn');
                const originalText = $('#submitText').text();

                if (isEditMode) {
                    $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...').prop('disabled', true);
                    $('#projectForm').attr('action', '<?= base_url('project/update') ?>');
                } else {
                    $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);
                    $('#projectForm').attr('action', '<?= base_url('project/store') ?>');
                }

                form.submit();
            }
        });

        // DataTable initialization
        $('#projectsTable').DataTable({
            processing: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            lengthChange: true,
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [
                {targets: -1, orderable: false}
            ],
            language: {
                search: "Search projects:",
                lengthMenu: "Show _MENU_ projects per page",
                info: "Showing _START_ to _END_ of _TOTAL_ projects",
                emptyTable: "No projects found"
            }
        });

        // Edit project button click
        $(document).on('click', '.editProjectBtn', function () {
            const encryptedId = $(this).data('id');
            editingProjectId = encryptedId;

            // Fetch project data via AJAX
            $.ajax({
                url: '<?= base_url('project/get_project') ?>',
                type: 'POST',
                data: {
                    project_id: encryptedId,
                    '<?= $csrf['name'] ?>': '<?= $csrf['hash'] ?>'
                },
                dataType: 'json',
                beforeSend: function () {
                    $(this).html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                },
                success: function (response) {
                    if (response.success) {
                        // Switch to edit mode
                        isEditMode = true;
                        $('#project_id').val(encryptedId);
                        $('#project_name').val(response.data.project_name);
                        $('#status').val(response.data.status);
                        $('#submitText').text('Update Project');
                        $('#submitBtn').removeClass('btn-primary').addClass('btn-warning');

                        // Scroll to form
                        $('html, body').animate({
                            scrollTop: $('#projectForm').offset().top - 100
                        }, 500);

                        // Focus on name field
                        $('#project_name').focus();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function () {
                    alert('Error fetching project data. Please try again.');
                },
                complete: function () {
                    $('.editProjectBtn').html('<i class="fas fa-edit"></i>').prop('disabled', false);
                }
            });
        });

        // Reset button click
        $('#resetBtn').click(function () {
            resetForm();
        });

        // Delete project button click
        $(document).on('click', '.deleteProjectBtn', function () {
            const encryptedId = $(this).data('id');
            const projectName = $(this).data('name');

            if (confirm('Are you sure you want to delete the project "' + projectName + '"?\n\nThis action can be undone by restoring the project later.')) {
                // Create a temporary form for deletion
                const deleteForm = $('<form>', {
                    method: 'POST',
                    action: '<?= base_url('project/delete') ?>'
                });

                deleteForm.append($('<input>', {
                    type: 'hidden',
                    name: 'project_id',
                    value: encryptedId
                }));

                deleteForm.append($('<input>', {
                    type: 'hidden',
                    name: '<?= $csrf['name'] ?>',
                    value: '<?= $csrf['hash'] ?>'
                }));

                $('body').append(deleteForm);
                deleteForm.submit();
            }
        });

        // Function to reset form to add mode
        function resetForm() {
            isEditMode = false;
            editingProjectId = null;
            $('#project_id').val('');
            $('#project_name').val('');
            $('#status').val('');
            $('#submitText').text('Save Project');
            $('#submitBtn').removeClass('btn-warning').addClass('btn-primary');
            $('#projectForm').attr('action', '<?= base_url('project/store') ?>');

            // Clear validation
            $('#projectForm').find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('#projectForm').find('.text-danger').remove();

            // Reset validator
            $('#projectForm').validate().resetForm();
        }
    });
</script>
