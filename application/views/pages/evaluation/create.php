<?php $this->load->view('includes/header'); ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-plus-circle"></i> Create New Evaluation
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <a href="<?= base_url('evaluation') ?>">Evaluation</a> /
                <span class="text-muted">Create</span>
            </nav>
        </div>
        <a href="<?= base_url('evaluation') ?>" class="nav-btn">
            <i class="fas fa-arrow-left me-2"></i> Back to Evaluations
        </a>
    </div>
</div>

<!-- Create Form -->
<div class="form-card">
    <div class="form-section-title">
        <i class="fas fa-edit"></i> Evaluation Details
    </div>
    <form id="evaluationForm" method="POST" action="<?= base_url('evaluation/store') ?>">
        <div class="row">
            <!-- Title Field -->
            <div class="col-md-12 mb-4">
                <label for="title" class="form-label">
                    Evaluation Title <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control <?= form_error('title') ? 'is-invalid' : '' ?>"
                       id="title"
                       name="title"
                       placeholder="Enter evaluation title (e.g., Q4 2024 Performance Review)"
                       value="<?= set_value('title') ?>"
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
                                <?= in_array($user->id, (array) set_value('assign_users[]')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user->name) ?>
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
                          ><?= set_value('description') ?></textarea>
                <?php if (form_error('description')): ?>
                    <div class="invalid-feedback d-block"><?= form_error('description') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i> Create Evaluation
            </button>
            <a href="<?= base_url('evaluation') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php $this->load->view('includes/footer'); ?>

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
                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...').prop('disabled', true);
                form.submit();
            }
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
    });
</script>
