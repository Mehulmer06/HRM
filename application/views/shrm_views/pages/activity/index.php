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
                <i class="fas fa-tasks"></i>
                Activity Management
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> / <span
                        class="text-muted">Activity Management</span>
            </nav>
        </div>
    </div>
</div>

<?php
// Get session data
$role = $this->session->userdata('role');
$category = $this->session->userdata('category');
$userId = $this->session->userdata('user_id');

// Check if user has permission to add activities (same logic as finance)
if (
    (
        ($role === 'e' && $category === 'e') ||
        ($role === 'admin') ||
        ($role === 'employee' && $category === 'admin')
    )
) {
    ?>
    <!-- Activity Form Section -->
    <div class="form-card">
        <div class="form-section-title">
            <i class="fas fa-plus"></i>
            Add New Activity
        </div>

        <form action="<?= base_url('activity/store') ?>" method="post" id="activityForm">
            <?php
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
            <input type="hidden" id="activity_id" name="activity_id" value="">

            <div class="row form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="name">Activity Name</label>
                        <input type="text"
                               class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
                               name="name"
                               id="name"
                               placeholder="Enter activity name"
                               value="<?= set_value('name') ?>">
                        <?= form_error('name', '<div class="invalid-feedback">', '</div>') ?>
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
                            <i class="fas fa-save me-1"></i> <span id="submitText">Save Activity</span>
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

<!-- Activities Table Section -->
<div class="out-of-office-section">
    <h2 class="section-title">
        <i class="fas fa-list"></i>
        Activities List
    </h2>

    <div class="table-container">
        <table id="activitiesTable" class="table-modern table table-striped table-hover">
            <thead>
            <tr>
                <th>S.No</th>
                <th>Activity Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($activities)): ?>
                <?php $i = 1; ?>
                <?php foreach ($activities as $activity): ?>
                    <?php if ($activity['deleted_at'] == null): // Only show non-deleted activities ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td>
                                <strong class="staff-name"><?= htmlspecialchars($activity['name']) ?></strong>
                            </td>
                            <td>
                                <?php if ($activity['status'] == 'active'): ?>
                                    <span class="status-badge status-active">Active</span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $activity['created_at'] ? date('M d, Y H:i', strtotime($activity['created_at'])) : '-' ?></td>
                            <td><?= $activity['updated_at'] ? date('M d, Y H:i', strtotime($activity['updated_at'])) : '-' ?></td>
                            <td>
                                <?php if (
                                    ($role === 'e' && $category === 'e') ||
                                    ($role === 'admin') ||
                                    ($role === 'employee' && $category === 'admin')
                                ): ?>
                                    <!-- Edit Button -->
                                    <button type="button"
                                            class="action-btn btn-edit editActivityBtn"
                                            data-id="<?= $activity['encrypted_id'] ?>"
                                            title="Edit Activity">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button"
                                            class="action-btn btn-toggle deleteActivityBtn"
                                            data-id="<?= $activity['encrypted_id'] ?>"
                                            data-name="<?= htmlspecialchars($activity['name']) ?>"
                                            title="Delete Activity">
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

<!-- Edit Activity Modal -->
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-labelledby="editActivityModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editActivityModalLabel">Edit Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_name" class="form-label">Activity Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit_name" name="edit_name" required>
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
                <button type="button" class="btn-primary" id="updateActivityBtn">Update Activity</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteActivityModal" tabindex="-1" aria-labelledby="deleteActivityModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('activity/delete') ?>" method="post">
                <?php
                $csrf = array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                );
                ?>
                <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>

                <div class="modal-header">
                    <h5 class="modal-title" id="deleteActivityModalLabel">Delete Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delete_activity_id" name="activity_id">
                    <p>Are you sure you want to delete the activity "<strong id="delete_activity_name"></strong>"?</p>
                    <p class="text-muted">This action can be undone by restoring the activity later.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="action-btn btn-toggle">Delete Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    $(document).ready(function () {
        let isEditMode = false;
        let editingActivityId = null;

        // Form validation
        $("#activityForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 255
                },
                status: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Please enter activity name.",
                    minlength: "Activity name must be at least 2 characters.",
                    maxlength: "Activity name cannot exceed 255 characters."
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
                    $('#activityForm').attr('action', '<?= base_url('activity/update') ?>');
                } else {
                    $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);
                    $('#activityForm').attr('action', '<?= base_url('activity/store') ?>');
                }

                form.submit();
            }
        });

        // DataTable initialization
        $('#activitiesTable').DataTable({
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
                search: "Search activities:",
                lengthMenu: "Show _MENU_ activities per page",
                info: "Showing _START_ to _END_ of _TOTAL_ activities",
                emptyTable: "No activities found"
            }
        });

        // Edit activity button click
        $(document).on('click', '.editActivityBtn', function () {
            const encryptedId = $(this).data('id');
            editingActivityId = encryptedId;

            // Fetch activity data via AJAX
            $.ajax({
                url: '<?= base_url('activity/get_activity') ?>',
                type: 'POST',
                data: {
                    activity_id: encryptedId,
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
                        $('#activity_id').val(encryptedId);
                        $('#name').val(response.data.name);
                        $('#status').val(response.data.status);
                        $('#submitText').text('Update Activity');
                        $('#submitBtn').removeClass('btn-primary').addClass('btn-warning');

                        // Scroll to form
                        $('html, body').animate({
                            scrollTop: $('#activityForm').offset().top - 100
                        }, 500);

                        // Focus on name field
                        $('#name').focus();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function () {
                    alert('Error fetching activity data. Please try again.');
                },
                complete: function () {
                    $('.editActivityBtn').html('<i class="fas fa-edit"></i>').prop('disabled', false);
                }
            });
        });

        // Reset button click
        $('#resetBtn').click(function () {
            resetForm();
        });

        // Delete activity button click
        $(document).on('click', '.deleteActivityBtn', function () {
            const encryptedId = $(this).data('id');
            const activityName = $(this).data('name');

            if (confirm('Are you sure you want to delete the activity "' + activityName + '"?\n\nThis action can be undone by restoring the activity later.')) {
                // Create a temporary form for deletion
                const deleteForm = $('<form>', {
                    method: 'POST',
                    action: '<?= base_url('activity/delete') ?>'
                });

                deleteForm.append($('<input>', {
                    type: 'hidden',
                    name: 'activity_id',
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
            editingActivityId = null;
            $('#activity_id').val('');
            $('#name').val('');
            $('#status').val('');
            $('#submitText').text('Save Activity');
            $('#submitBtn').removeClass('btn-warning').addClass('btn-primary');
            $('#activityForm').attr('action', '<?= base_url('activity/store') ?>');

            // Clear validation
            $('#activityForm').find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('#activityForm').find('.text-danger').remove();

            // Reset validator
            $('#activityForm').validate().resetForm();
        }
    });
</script>