<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php');
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-edit"></i>
                Edit Staff Member
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?=base_url('shrm/dashboard')?>">Dashboard</a> /
                <a href="<?=base_url('project-staff')?>">Project Staff</a> /
                <span class="text-muted">Edit</span>
            </nav>
        </div>
        <a href="<?= base_url('project-staff') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Back to List
        </a>
    </div>
</div>

<!-- Create Form -->
<form id="createStaffForm" action="<?= base_url('project-staff/update/' . (!empty($user['id']) ? $user['id'] : '')); ?>"
      enctype="multipart/form-data" method="post">
    <?php
    $csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
    );
    ?>
    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>

    <!-- Personal Information -->
    <div class="form-card">
        <h3 class="form-section-title"><i class="fas fa-user"></i> Personal Information</h3>

        <div class="row">
            <div class="col-md-6">
                <label for="employee_id" class="form-label">Employee Id</label>
                <input
                        type="number"
                        class="form-control <?= form_error('employee_id') ? 'is-invalid' : '' ?>"
                        id="employee_id"
                        name="employee_id"
                        value="<?= set_value('employee_id', !empty($user['employee_id']) ? $user['employee_id'] : '') ?>"
                >
                <?php if (form_error('employee_id')): ?>
                    <div class="invalid-feedback">
                        <?= form_error('employee_id') ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($this->session->userdata('role') === 'e'
                && $this->session->userdata('e_mail') === 'abhishek@inflibnet.ac.in'): ?>
                <div class="col-md-6">
                    <label for="ro_flag" class="form-label">RO Flag</label>
                    <select
                            class="form-select <?= form_error('ro_flag') ? 'is-invalid' : '' ?>"
                            id="ro_flag"
                            name="ro_flag"
                    >
                        <option value="">Select RO Flag</option>
                        <option value="hard"   <?= set_select('ro_flag','hard',   (!empty($user['ro_flag']) && $user['ro_flag']==='hard')   ) ?>>Hard coder</option>
                        <option value="medium" <?= set_select('ro_flag','medium', (!empty($user['ro_flag']) && $user['ro_flag']==='medium') ) ?>>Medium coder</option>
                        <option value="low"    <?= set_select('ro_flag','low',    (!empty($user['ro_flag']) && $user['ro_flag']==='low')    ) ?>>Low coder</option>
                    </select>
                    <?php if (form_error('ro_flag')): ?>
                        <div class="invalid-feedback">
                            <?= form_error('ro_flag') ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= set_value('name', !empty($user['name']) ? $user['name'] : '') ?>">
                <small class="text-danger"><?= form_error('name') ?></small>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= set_value('email', !empty($user['email']) ? $user['email'] : '') ?>">
                <small class="text-danger"><?= form_error('email') ?></small>
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob"
                       value="<?= set_value('dob', !empty($user['dob']) ? $user['dob'] : '') ?>">
                <small class="text-danger"><?= form_error('dob') ?></small>
            </div>
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select select2" id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="Male" <?= set_select('gender', 'Male', (!empty($user['gender']) && $user['gender'] == 'Male')) ?>>Male
                    </option>
                    <option value="Female" <?= set_select('gender', 'Female', (!empty($user['gender']) && $user['gender'] == 'Female')) ?>>
                        Female
                    </option>
                    <option value="Other" <?= set_select('gender', 'Other', (!empty($user['gender']) && $user['gender'] == 'Other')) ?>>Other
                    </option>
                </select>
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="mobile" class="form-label">Mobile Number</label>
                <input type="tel" class="form-control" id="mobile" name="mobile"
                       value="<?= set_value('mobile', !empty($user['phone']) ? $user['phone'] : '') ?>">
            </div>
            <div class="col-md-6">
                <label for="pan" class="form-label">PAN Number</label>
                <input type="text" class="form-control" id="pan" name="pan"
                       value="<?= set_value('pan', !empty($user['pan_number']) ? $user['pan_number'] : '') ?>" style="text-transform: uppercase;">
            </div>
        </div>

        <div class="row form-row">
            <div class="col-12">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address"
                          rows="3"><?= set_value('address', !empty($user['address']) ? $user['address'] : '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Professional Information -->
    <div class="form-card">
        <h3 class="form-section-title"><i class="fas fa-briefcase"></i> Professional Information</h3>

        <div class="row form-row">
            <div class="col-md-12">
                <label for="professional_email" class="form-label">Professional Email</label>
                <input type="email" class="form-control" placeholder="Enter professional email" name="professional_email"
                       id="professional_email"
                       value="<?= set_value('professional_email', !empty($user['professional_email']) ? $user['professional_email'] : '') ?>"/>
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="department" class="form-label">Department</label>
                <select class="form-select select2" id="department" name="department">
                    <option value="">Select Department</option>
                    <option
                            value="Administrator" <?= set_select('department', 'Administrator', (!empty($user['department']) && $user['department'] == 'Administrator')) ?>>
                        Administrator
                    </option>
                    <option
                            value="library science" <?= set_select('department', 'library science', (!empty($user['department']) && $user['department'] == 'library science')) ?>>
                        Library Science
                    </option>
                    <option
                            value="computer science" <?= set_select('department', 'computer science', (!empty($user['department']) && $user['department'] == 'computer science')) ?>>
                        Computer Science
                    </option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="reporting_officer" class="form-label">Reporting Officer *</label>
                <select class="form-select select2" id="reporting_officer" name="reporting_officer">
                    <option value="">Select Reporting Officer</option>
                    <?php if (!empty($reportingOfficers)): ?>
                        <?php foreach ($reportingOfficers as $officer): ?>
                            <option value="<?= $officer->id ?>,'.',<?= $officer->name ?>,'.',<?= $officer->designation ?>"
                                <?= set_select('reporting_officer', $officer->id, (!empty($selectedReportingOfficer) && $selectedReportingOfficer == $officer->id)) ?>>
                                <?= !empty($officer->name) ? $officer->name : '-' ?> - <?= !empty($officer->designation) ? $officer->designation : '-' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-6 d-none">
                <label for="role" class="form-label">Role/Position *</label>
                <select class="form-select select2" id="role" name="role">
                    <option value="">Select Role</option>
					<option value="employee" selected>Employee</option>
                    <?php
                    /*$roles = ['employee', 'viswambi'];
                    foreach ($roles as $role): ?>
                        <option value="<?= $role ?>" <?= set_select('role', $role, (!empty($user['role']) && $user['role'] == $role)) ?>>
                            <?= ucfirst($role) ?>
                        </option>
                    <?php endforeach;*/ ?>
                </select>
            </div>

            <div class="col-md-6 d-none">
                <label for="sub_role" class="form-label">Sub Role *</label>
                <select class="form-select select2" id="sub_role" name="sub_role">
                    <option value="">Select Sub Role</option>
					<option value="employee" selected>Employee</option>
                    <?php
                    /*$sub_roles = ['admin', 'employee'];
                    foreach ($sub_roles as $sub): ?>
                        <option
                                value="<?= $sub ?>" <?= set_select('sub_role', $sub, (!empty($user['category']) && $user['category'] == $sub)) ?>>
                            <?= ucfirst(str_replace('_', ' ', $sub)) ?>
                        </option>
                    <?php endforeach;*/ ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Document Upload -->
    <div class="form-card">
        <h3 class="form-section-title"><i class="fas fa-file-upload"></i> Document Upload</h3>
        <div class="row form-row">
            <!-- Profile Photo -->
            <div class="col-md-6">
                <label class="form-label">Profile Photo</label>
                <div class="file-upload-container" onclick="document.getElementById('photo').click()">
                    <div class="file-upload-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="file-upload-text">Click to upload profile photo</div>
                    <div class="file-upload-text">JPG, PNG (Max: 2MB)</div>
                    <button type="button" class="file-upload-btn">Choose Photo</button>
                </div>
                <input type="file" id="photo" name="photo" class="file-upload-input" accept="image/*">

                <!-- Show existing photo if available -->
                <div id="photo-preview" class="file-preview <?= !empty($user['photo']) ? 'show' : '' ?>">
                    <?php if (!empty($user['photo'])): ?>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="<?= base_url('uploads/photo/' . $user['photo']) ?>" alt="Profile Photo"
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
                                <div>
                                    <div style="font-weight: 600; font-size: 14px;"><?= $user['photo'] ?></div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-input="photo"
                                    data-preview="photo-preview">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">No photo uploaded</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Digital Signature -->
            <div class="col-md-6">
                <label class="form-label">Digital Signature</label>
                <div class="file-upload-container" onclick="document.getElementById('signature').click()">
                    <div class="file-upload-icon">
                        <i class="fas fa-signature"></i>
                    </div>
                    <div class="file-upload-text">Click to upload signature</div>
                    <div class="file-upload-text">JPG, PNG (Max: 1MB)</div>
                    <button type="button" class="file-upload-btn">Choose Signature</button>
                </div>
                <input type="file" id="signature" name="signature" class="file-upload-input" accept="image/*">

                <!-- Show existing signature if available -->
                <div id="signature-preview" class="file-preview <?= !empty($user['signature']) ? 'show' : '' ?>">
                    <?php if (!empty($user['signature'])): ?>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="<?= base_url('uploads/signature/' . $user['signature']) ?>" alt="Digital Signature"
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
                                <div>
                                    <div style="font-weight: 600; font-size: 14px;"><?= $user['signature'] ?></div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-input="signature"
                                    data-preview="signature-preview">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">No signature uploaded</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets Information -->
    <div class="form-card">
        <h3 class="form-section-title">
            <i class="fas fa-desktop"></i>
			Assets & Network Information
        </h3>
        <div class="row form-row">
            <div class="col-md-6">
                <label for="sitting_location" class="form-label">Sitting Location</label>
                <input type="text" class="form-control" id="sitting_location" name="sitting_location"
                       placeholder="e.g., Ground Floor - Room 101, 1st Floor - Cabin A2"
                       value="<?= set_value('sitting_location', !empty($assets['sitting_location']) ? $assets['sitting_location'] : '') ?>">
            </div>
			<div class="col-md-6">
				<label for="assets" class="form-label">Assets</label>
				<textarea class="form-control" name="assets" id="assets" rows="3"
						  placeholder="e.g., Laptop - Dell Latitude, Phone - iPhone 13"><?= set_value('assets', !empty($assets['asset_detail']) ? $assets['asset_detail'] : '') ?></textarea>
			</div>
        </div>
		<div class="row form-row">
			<div class="col-md-6">
				<label for="ip_address" class="form-label">IP Address</label>
				<input type="text" class="form-control" id="ip_address" name="ip_address"
					   placeholder="e.g., 192.168.1.100" value="<?= set_value('ip_address',!empty($assets['ip_address'])? $assets['ip_address'] :'')?>">
			</div>
			<div class="col-md-6">
				<label for="connection_type" class="form-label">Internet Connection</label>
				<select class="form-select" id="connection_type" name="connection_type">
					<option value="" disabled selected>Select Internet Connection</option>
					<option value="lan" <?= set_select('internet_connection', 'lan', (!empty($assets['connection_type']) && $assets['connection_type'] == 'lan')) ?>>LAN</option>
					<option value="wifi" <?= set_select('internet_connection', 'wifi', (!empty($assets['connection_type']) && $assets['connection_type'] == 'wifi')) ?>>WiFi</option>
				</select>
			</div>
		</div>
		<div class="row form-row">
			<div class="col-md-12">
				<label for="antivirus" class="form-label">Antivirus</label>
				<input type="text" class="form-control" id="antivirus" name="antivirus" placeholder="e.g., Quick Heal, Kaspersky, Norton" value="<?= set_value('antivirus',!empty($assets['antivirus'])? $assets['antivirus'] :'')?>">
			</div>
		</div>
    </div>

    <!-- Submit -->
    <div class="form-card">
        <div class="d-flex justify-content-end gap-3">
            <button type="reset" class="btn btn-secondary"><i class="fas fa-undo me-2"></i> Reset</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Update Staff</button>
        </div>
    </div>
</form>

<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'default',
            width: '100%',
            placeholder: function () {
                return $(this).find('option:first').text();
            },
            allowClear: true
        });

        // Custom validation methods
        $.validator.addMethod("panFormat", function (value, element) {
            return this.optional(element) || /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(value);
        }, "Please enter a valid PAN number (e.g., ABCDE1234F)");

        $.validator.addMethod("mobileFormat", function (value, element) {
            // Remove all non-digits
            var cleanValue = value.replace(/\D/g, '');
            return this.optional(element) || (cleanValue.length === 10);
        }, "Please enter a valid 10-digit mobile number");

        $.validator.addMethod("filesize", function (value, element, param) {
            if (element.files.length === 0) return true;
            return element.files[0].size <= param;
        }, "File size must be less than {0} bytes");

        $.validator.addMethod("extension", function (value, element, param) {
            if (element.files.length === 0) return true;
            var extension = value.split('.').pop().toLowerCase();
            return $.inArray(extension, param.split('|')) !== -1;
        }, "Please select a file with a valid extension");

        // Initialize jQuery Validation
        $('#createStaffForm').validate({
            ignore: [],
            rules: {
                employee_id: {
                    digits: true
                },
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 100
                },
                dob: {
                    date: true
                },
                mobile: {
                    mobileFormat: true
                },
                pan: {
                    panFormat: true
                },
                address: {
                    minlength: 10,
                    maxlength: 500
                },
                reporting_officer: {
                    required: true,
                },
                role: {
                    required: true
                },
                sub_role: {
                    required: true
                },
                password: {
                    minlength: 6,
                    maxlength: 100
                },
                confirm_password: {
                    equalTo: "#password"
                },
                photo: {
                    extension: "jpg|jpeg|png",
                    filesize: 2097152 // 2MB in bytes
                },
                signature: {
                    extension: "jpg|jpeg|png",
                    filesize: 1048576 // 1MB in bytes
                },
                professional_email: {
                    email: true,
                },
            },
            messages: {
                employee_id: {
                    digits: "Employee ID must be a number"
                },
                name: {
                    required: "Please enter your full name",
                    minlength: "Name must be at least 2 characters long",
                    maxlength: "Name cannot exceed 100 characters"
                },
                email: {
                    required: "Please enter your email address",
                    email: "Please enter a valid email address"
                },
                address: {
                    minlength: "Address must be at least 10 characters long"
                },
                reporting_officer: {
                    required: "Please Select Reporting Officer"
                },
                role: {
                    required: "Please select a role"
                },
                sub_role: {
                    required: "Please select a sub role"
                },
                password: {
                    minlength: "Password must be at least 6 characters long"
                },
                confirm_password: {
                    equalTo: "Passwords do not match"
                },
                photo: {
                    extension: "Please select a valid image file (JPG, PNG)",
                    filesize: "File size must be less than 2MB"
                },
                signature: {
                    extension: "Please select a valid image file (JPG, PNG)",
                    filesize: "File size must be less than 1MB"
                },
                professional_email: {
                    email: "Please enter a valid email address"
                },
            },
            errorElement: 'span',
            errorClass: 'error',
            validClass: 'valid',
            errorPlacement: function (error, element) {
                // Handle Select2 elements
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.hasClass('file-upload-input')) {
                    // Handle file upload elements
                    error.insertAfter(element.closest('.file-upload-container'));
                } else {
                    // Default placement for other elements
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass(errorClass).removeClass(validClass);
                // Handle Select2 elements
                if ($(element).hasClass('select2')) {
                    $(element).next('.select2-container').find('.select2-selection').addClass(errorClass);
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass(errorClass).addClass(validClass);
                // Handle Select2 elements
                if ($(element).hasClass('select2')) {
                    $(element).next('.select2-container').find('.select2-selection').removeClass(errorClass);
                }
            },
            submitHandler: function (form) {
                const $submitBtn = $('button[type="submit"]');
                $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...').prop('disabled', true);

                form.submit();
            }
        });

        // File Upload Handlers
        $('#photo').on('change', function (e) {
            handleFileUpload(e, 'photo-preview', 'Profile Photo');
            // Trigger validation for file input
            $(this).valid();
        });

        $('#signature').on('change', function (e) {
            handleFileUpload(e, 'signature-preview', 'Digital Signature');
            // Trigger validation for file input
            $(this).valid();
        });

        function handleFileUpload(event, previewId, fileName) {
            const file = event.target.files[0];
            const $preview = $('#' + previewId);

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $preview.html(`
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="${e.target.result}" alt="${fileName}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
                        <div>
                            <div style="font-weight: 600; font-size: 14px;">${file.name}</div>
                            <div style="font-size: 12px; color: #6c757d;">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-input="${event.target.id}" data-preview="${previewId}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `);
                    $preview.addClass('show');
                };
                reader.readAsDataURL(file);
            }
        }

        // Remove file handler
        $(document).on('click', '.btn-outline-danger', function () {
            const inputId = $(this).data('input');
            const previewId = $(this).data('preview');
            $('#' + inputId).val('').trigger('change');
            $('#' + previewId).removeClass('show').html('');
        });

        // PAN Number Formatting
        $('#pan').on('input', function () {
            let value = $(this).val().toUpperCase();
            value = value.replace(/[^A-Z0-9]/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            $(this).val(value);
        });

        // Reset form handler
        $('button[type="reset"]').on('click', function () {
            // Reset Select2 elements
            $('.select2').val(null).trigger('change');
            // Clear file previews
            $('.file-preview').removeClass('show').html('');
            // Clear validation errors
            $('#createStaffForm').validate().resetForm();
            // Reset form
            $('#createStaffForm')[0].reset();
        });

        $('.select2').on('change', function () {
            $(this).valid(); // Trigger validation to remove error if valid
        });
    });
</script>
