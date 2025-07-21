<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php');
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-edit"></i> Edit Work Progress
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <a href="<?= base_url('work-progress') ?>">Work Progress</a> /
                <span class="text-muted">Edit</span>
            </nav>
        </div>
        <a href="<?= base_url('work-progress') ?>" class="nav-btn bg-dark">
            <i class="fas fa-arrow-left me-2"></i> Back to Work Progress
        </a>
    </div>
</div>

<!-- Edit Form -->
<div class="form-card">
    <div class="form-section-title">
        <i class="fas fa-edit"></i> Work Progress Details
    </div>
    <form id="evaluationForm" method="POST" action="<?= base_url('work-progress/update/' . $evaluation->id) ?>"
          enctype="multipart/form-data">
        <?php
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        ?>
        <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
        <div class="row">
            <!-- Title Field -->
            <div class="col-md-12 mb-4">
                <label for="title" class="form-label">
                    Work Title <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control <?= form_error('title') ? 'is-invalid' : '' ?>"
                       id="title"
                       name="title"
                       placeholder="Enter Work Title (e.g., Q4 2024 Performance Review)"
                       value="<?= set_value('title', $evaluation->title) ?>"
                >
                <?php if (form_error('title')): ?>
                    <div class="invalid-feedback"><?= form_error('title') ?></div>
                <?php endif; ?>
            </div>

            <!-- Assign Users Field -->
            <div class="col-md-12 mb-4">
                <label for="assign_users" class="form-label">
                    Assign Users <span class="text-danger">*</span>
                </label>
                <select class="form-select <?= form_error('assign_users[]') ? 'is-invalid' : '' ?>"
                        id="assign_users"
                        name="assign_users[]"
                        multiple="multiple"
                        data-placeholder="Select users to evaluate..."
                >
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user->id ?>"
                                <?= in_array($user->id, $assigned_user_ids ?? []) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user->name) ?> - <?= htmlspecialchars($user->designation??'') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option disabled>No users available</option>
                    <?php endif; ?>
                </select>
                <?php if (form_error('assign_users[]')): ?>
                    <div class="invalid-feedback d-block"><?= form_error('assign_users[]') ?></div>
                <?php endif; ?>
            </div>

            <!-- Description Field -->
            <div class="col-md-12 mb-4">
                <label for="description" class="form-label">
                    Description <span class="text-danger">*</span>
                </label>
                <textarea id="description"
                          name="description"
                          class="form-control <?= form_error('description') ? 'is-invalid' : '' ?>"
                ><?= set_value('description', $evaluation->description) ?></textarea>
                <?php if (form_error('description')): ?>
                    <div class="invalid-feedback d-block"><?= form_error('description') ?></div>
                <?php endif; ?>
            </div>

            <!-- Current Attachment Display -->
            <?php if (!empty($evaluation->attachment)): ?>
                <div class="col-md-12 mb-4">
                    <label class="form-label">Current Attachment</label>
                    <div class="current-attachment">
                        <div class="attachment-info">
                            <i class="fas fa-file-alt me-2 text-primary"></i>
                            <span class="attachment-name me-2">
                                <?= isset($evaluation->attachment_original_name) ? $evaluation->attachment_original_name : basename($evaluation->attachment) ?>
                            </span>
                            <a href="<?= base_url($evaluation->attachment) ?>" target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <div class="row">
            <!-- Attachment Field -->
            <div class="col-md-6 mb-4">
                <label for="attachment" class="form-label">
                    <?= !empty($evaluation->attachment) ? 'Update Attachment (Optional)' : 'Attachment (Optional)' ?>
                </label>
                <input type="file" name="attachment" id="attachment" class="form-control"
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls">
                <?php if (!empty($evaluation->attachment)): ?>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Selecting a new file will replace the current attachment.
                    </small>
                <?php endif; ?>
                <?php if (form_error('attachment')): ?>
                    <div class="invalid-feedback d-block"><?= form_error('attachment') ?></div>
                <?php endif; ?>
            </div>

            <!-- Category Field -->
            <div class="col-md-6 mb-4">
                <label for="category" class="form-label">Priority *</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="routine" <?= ($evaluation->category == 'routine') ? 'selected' : '' ?>>Routine</option>
                    <option value="urgent" <?= ($evaluation->category == 'urgent') ? 'selected' : '' ?>>Urgent</option>
                    <option value="addon" <?= ($evaluation->category == 'addon') ? 'selected' : '' ?>>Add-on</option>
                    <option value="support" <?= ($evaluation->category == 'support') ? 'selected' : '' ?>>Support</option>
                </select>
            </div>

            <!-- Project Field -->
            <div class="col-md-6 mb-4">
                <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                <select name="project_id" id="project_id" class="form-select <?= form_error('project_id') ? 'is-invalid' : '' ?>">
                    <option value="">Select Project</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= $project['id'] ?>" <?= set_select('project_id', $project['id'], $project['id'] == $evaluation->project_id) ?>>
                            <?= htmlspecialchars($project['project_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (form_error('project_id')): ?>
                    <div class="invalid-feedback"><?= form_error('project_id') ?></div>
                <?php endif; ?>
            </div>

            <!-- Activity Field -->
            <div class="col-md-6 mb-4">
                <label for="activity_id" class="form-label">Activity <span class="text-danger">*</span></label>
                <select name="activity_id" id="activity_id" class="form-select <?= form_error('activity_id') ? 'is-invalid' : '' ?>">
                    <option value="">Select Activity</option>
                    <?php foreach ($activity as $act): ?>
                        <option value="<?= $act['id'] ?>" <?= set_select('activity_id', $act['id'], $act['id'] == $evaluation->activity_id) ?>>
                            <?= htmlspecialchars($act['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (form_error('activity_id')): ?>
                    <div class="invalid-feedback"><?= form_error('activity_id') ?></div>
                <?php endif; ?>
            </div>

        </div>


        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i> Update Work Progress
            </button>
            <a href="<?= base_url('work-progress') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    $(document).ready(function () {
        $('#assign_users').select2({
            placeholder: 'Select users to evaluate...',
            allowClear: true,
            closeOnSelect: false
        });

        $('#description').summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['codeview', 'help']]
            ],
            placeholder: 'Enter evaluation description...'
        });

        $('#evaluationForm').validate({
            ignore: [],
            rules: {
                title: {
                    required: true,
                    minlength: 5,
                    maxlength: 255
                },
                'assign_users[]': {
                    required: true
                },
                description: {
                    summernoteRequired: true,
                    summernoteMinLength: 10
                },
                attachment: {
                    fileSize: 10485760, // 10MB in bytes
                    fileType: 'pdf|doc|docx|jpg|jpeg|png|xlsx|xls'
                },
                project_id: {
                    required: true
                },
                activity_id: {
                    required: true
                }

            },
            messages: {
                title: {
                    required: "Please enter an evaluation title",
                    minlength: "Title must be at least 5 characters long",
                    maxlength: "Title cannot exceed 255 characters"
                },
                'assign_users[]': {
                    required: "Please select at least one user"
                },
                description: {
                    summernoteRequired: "Please enter a description",
                    summernoteMinLength: "Description must be at least 10 characters long"
                },
                attachment: {
                    fileSize: "File size must be less than 10MB",
                    fileType: "Please select a valid file type (PDF, DOC, DOCX, JPG, JPEG, PNG, XLS, XLSX)"
                },
                project_id: {
                    required: "Please select a project"
                },
                activity_id: {
                    required: "Please select an activity"
                }

            },
            errorElement: 'span',
            errorClass: 'invalid-feedback',
            validClass: 'is-valid',
            errorPlacement: function (error, element) {
                if (element.attr('name') === 'assign_users[]') {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.attr('name') === 'description') {
                    error.insertAfter(element.next('.note-editor'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass(validClass);
                if ($(element).attr('name') === 'assign_users[]') {
                    $(element).next('.select2-container').find('.select2-selection').addClass('is-invalid');
                } else if ($(element).attr('name') === 'description') {
                    $(element).next('.note-editor').addClass('is-invalid');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass(validClass);
                if ($(element).attr('name') === 'assign_users[]') {
                    $(element).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                } else if ($(element).attr('name') === 'description') {
                    $(element).next('.note-editor').removeClass('is-invalid');
                }
            },
            submitHandler: function (form) {
                const submitBtn = $(form).find('button[type="submit"]');
                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...').prop('disabled', true);
                form.submit();
            }
        });

        // Custom validation methods for file upload
        $.validator.addMethod("fileSize", function (value, element, maxSize) {
            if (element.files.length === 0) {
                return true; // No file selected, so it's valid (optional field)
            }
            return element.files[0].size <= maxSize;
        });

        $.validator.addMethod("fileType", function (value, element, allowedTypes) {
            if (element.files.length === 0) {
                return true; // No file selected, so it's valid (optional field)
            }

            const file = element.files[0];
            const fileName = file.name.toLowerCase();
            const allowedExtensions = allowedTypes.split('|');

            return allowedExtensions.some(ext => fileName.endsWith('.' + ext));
        });

        $.validator.addMethod("summernoteRequired", function (value, element) {
            var content = $(element).summernote('code');
            var textContent = $('<div>').html(content).text().trim();
            return textContent.length > 0;
        });

        $.validator.addMethod("summernoteMinLength", function (value, element, minLength) {
            var content = $(element).summernote('code');
            var textContent = $('<div>').html(content).text().trim();
            return textContent.length >= minLength;
        });

        $('#assign_users').on('change', function () {
            $(this).valid();
        });

        $('#description').on('summernote.change', function () {
            $(this).valid();
        });

        // Trigger validation on file input change
        $('#attachment').on('change', function () {
            $(this).valid();
        });
    });
</script>

<style>
    .current-attachment {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .attachment-info {
        display: flex;
        align-items: center;
    }

    .attachment-name {
        font-weight: 500;
        color: #495057;
    }

    .btn-outline-primary {
        border-color: #007bff;
        color: #007bff;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }
</style>