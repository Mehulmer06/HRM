<?php $this->load->view('includes/header'); ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title"><i class="fas fa-plus-circle"></i> Create New Note</h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <a href="<?= base_url('note') ?>">Note Management</a> /
                <span class="text-muted">Create Note</span>
            </nav>
        </div>
        <a href="<?= base_url('note') ?>" class="nav-btn">
            <i class="fas fa-arrow-left"></i> Back to Notes
        </a>
    </div>
</div>

<!-- Flash Message -->
<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
<?php elseif ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
<?php endif; ?>

<!-- Create Note Form -->
<div class="form-card">
    <form id="noteForm" method="POST" action="<?= base_url('note/store') ?>" enctype="multipart/form-data">
        <div class="form-section-title">
            <i class="fas fa-edit"></i>
            Note Details
        </div>

        <!-- Title Field -->
        <div class="row form-row">
            <div class="col-12">
                <div class="form-group">
                    <label for="noteTitle" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="noteTitle" name="title"
                           placeholder="Enter note title" value="<?= set_value('title') ?>" required>
                </div>
            </div>
        </div>

        <!-- Description Field -->
        <div class="row form-row">
            <div class="col-12">
                <div class="form-group">
                    <label for="noteDescription" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea id="noteDescription" name="description" class="form-control summernote"><?= set_value('description') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Submitted To Field -->
        <div class="row form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="submittedTo" class="form-label">Submitted To <span class="text-danger">*</span></label>
                    <select class="form-select" id="submittedTo" name="submitted_to" required>
                        <option value="">Select Reporting Officer/Department</option>
                        <option value="ro" <?= set_select('submitted_to', 'ro') ?>>Reporting Officer (RO)</option>
                        <option value="ro_admin" <?= set_select('submitted_to', 'ro_admin') ?>>RO → Admin</option>
                        <option value="ro_admin_vishwambi" <?= set_select('submitted_to', 'ro_admin_vishwambi') ?>>RO → Admin → Vishwambi</option>
                        <option value="ro_vishwambi" <?= set_select('submitted_to', 'ro_vishwambi') ?>>RO → Vishwambi</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Any Attachment? <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="has_attachment"
                                   id="attachmentYes" value="yes" <?= set_radio('has_attachment', 'yes') ?>>
                            <label class="form-check-label" for="attachmentYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="has_attachment"
                                   id="attachmentNo" value="no" <?= set_radio('has_attachment', 'no', TRUE) ?>>
                            <label class="form-check-label" for="attachmentNo">No</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachment Section -->
        <div class="row form-row">
            <div class="col-12">
                <div id="attachmentSection" style="display: none;">
                    <div class="attachment-container">
                        <div class="attachment-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="attachment_titles[]" placeholder="Attachment title">
                                </div>
                                <div class="col-md-6">
                                    <input type="file" class="form-control" name="attachments[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-attachment" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addAttachment">
                        <i class="fas fa-plus"></i> Add More
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <div class="d-flex gap-3 justify-content-end flex-wrap">
                <button type="button" class="btn btn-secondary" onclick="history.back()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary" name="action" value="submit" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Submit Note
                </button>
                <button type="submit" class="btn btn-warning" name="action" value="draft" id="saveDraftBtn">
                    <i class="fas fa-save"></i> Save as Draft
                </button>
            </div>
        </div>
    </form>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function () {
        // Custom Summernote validation
        $.validator.addMethod("summernoteRequired", function (value, element) {
            var content = $('.summernote').summernote('code');
            return content && content !== '<p><br></p>' && content.trim() !== '';
        }, "Please enter a description");

        // File size validation
        $.validator.addMethod("maxFileSize", function (val, element, maxSizeInMB) {
            var maxSizeInBytes = maxSizeInMB * 1048576;
            if (element.files && element.files[0]) {
                return element.files[0].size <= maxSizeInBytes;
            }
            return true;
        }, $.validator.format("File size must be less than {0} MB."));

        // File extension validation
        $.validator.addMethod("fileExtension", function (value, element, allowedExtensions) {
            if (element.files && element.files[0]) {
                var ext = element.files[0].name.split('.').pop().toLowerCase();
                return allowedExtensions.includes(ext);
            }
            return true;
        }, "Invalid file type.");

        // Initialize Summernote
        $('.summernote').summernote({
            height: 200,
            placeholder: 'Enter detailed description of your note...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function (contents) {
                    $('#noteDescription').val(contents).valid();
                }
            }
        });

        // Handle show/hide attachment section
        $('input[name="has_attachment"]').change(function () {
            if ($(this).val() === 'yes') {
                $('#attachmentSection').slideDown(300);
            } else {
                $('#attachmentSection').slideUp(300);
                clearAllAttachments();
            }
            $("#noteForm").valid();
        });

        // Proper way to dynamically add fields and apply validation
        $(document).on('click', '#addAttachment', function () {
            const newField = $(`
        <div class="attachment-item mt-2">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control attachment-title" name="attachment_titles[]" placeholder="Attachment title">
                </div>
                <div class="col-md-6">
                    <input type="file" class="form-control attachment-file" name="attachments[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-attachment">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `);

            $('.attachment-container').append(newField);

            if ($('.attachment-item').length > 1) {
                $('.remove-attachment').show();
            }

            // ✅ Add jQuery Validate rules to the new fields
            newField.find('.attachment-title').rules("add", {
                required: function () {
                    return $('input[name="has_attachment"]:checked').val() === 'yes';
                },
                minlength: 3,
                messages: {
                    required: "Please enter a title for the attachment",
                    minlength: "Title must be at least 3 characters"
                }
            });

            newField.find('.attachment-file').rules("add", {
                required: function () {
                    return $('input[name="has_attachment"]:checked').val() === 'yes';
                },
                maxFileSize: 10,
                fileExtension: ["pdf", "doc", "docx", "jpg", "jpeg", "png", "xlsx", "xls"],
                messages: {
                    required: "Please select a file to upload",
                    maxFileSize: "File must be less than 10 MB",
                    fileExtension: "Invalid file type"
                }
            });

            $("#noteForm").valid();
        });


        // Remove attachment field
        $(document).on('click', '.remove-attachment', function () {
            $(this).closest('.attachment-item').remove();
            if ($('.attachment-item').length === 1) $('.remove-attachment').hide();
            $("#noteForm").validate().settings.ignore = [];
            $("#noteForm").valid();
        });

        // Clear attachments
        function clearAllAttachments() {
            $('.attachment-container').html(`
                <div class="attachment-item">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="attachment_titles[]" placeholder="Attachment title" required minlength="3">
                        </div>
                        <div class="col-md-6">
                            <input type="file" class="form-control" name="attachments[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-attachment" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
        }



        // jQuery Validate
        $("#noteForm").validate({
            ignore: [],
            rules: {
                title: {
                    required: true,
                    minlength: 5,
                    maxlength: 200
                },
                description: {
                    summernoteRequired: true,
                    minlength: 10
                },
                submitted_to: {
                    required: true
                },
                has_attachment: {
                    required: true
                },
                "attachment_titles[]": {
                    required: function () {
                        return $('input[name="has_attachment"]:checked').val() === 'yes';
                    },
                    minlength: 3
                },
                "attachments[]": {
                    required: function () {
                        return $('input[name="has_attachment"]:checked').val() === 'yes';
                    },
                    maxFileSize: 10,
                    fileExtension: ["pdf", "doc", "docx", "jpg", "jpeg", "png", "xlsx", "xls"]
                }
            },
            messages: {
                title: {
                    required: "Please enter a title",
                    minlength: "At least 5 characters",
                    maxlength: "Max 200 characters"
                },
                description: {
                    required: "Please enter a description",
                    minlength: "At least 10 characters"
                },
                submitted_to: {
                    required: "Please select an option"
                },
                has_attachment: {
                    required: "Please select Yes or No"
                },
                "attachment_titles[]": {
                    required: "Enter attachment title",
                    minlength: "At least 3 characters"
                },
                "attachments[]": {
                    required: "Select a file",
                    maxFileSize: "Max 10MB",
                    fileExtension: "Allowed types: pdf, doc, docx, jpg, png, xls, xlsx"
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            errorPlacement: function (error, element) {
                if (element.attr('type') === 'radio') {
                    error.insertAfter(element.closest('.d-flex'));
                } else if (element.hasClass('summernote')) {
                    error.insertAfter(element.next('.note-editor'));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                submitNote('submit');
            }
        });

        // Submit logic
        function submitNote(action) {
            const isSubmit = action === 'submit';
            const btn = isSubmit ? $('#submitBtn') : $('#saveDraftBtn');
            const loadingText = isSubmit
                ? '<i class="fas fa-spinner fa-spin"></i> Submitting...'
                : '<i class="fas fa-spinner fa-spin"></i> Saving Draft...';

            btn.prop('disabled', true).html(loadingText);
            form.submit();
        }

        // Init first field
        clearAllAttachments();
    });
</script>
