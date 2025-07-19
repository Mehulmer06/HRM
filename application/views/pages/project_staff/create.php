<?php $this->load->view('includes/header'); ?>
<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Custom Styling */
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #495057;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }

    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    /* Validation Error Styling */
    .error {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
        display: block;
    }

    input.error,
    textarea.error {
        border-color: #dc3545;
    }

    .select2-container--default .select2-selection--single.error {
        border-color: #dc3545;
    }

    .valid {
        border-color: #28a745;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-plus"></i>
                Add New Staff Member
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?=base_url('dashboard')?>">Dashboard</a> /
                <a href="<?=base_url('project-staff')?>">Project Staff</a> /
                <span class="text-muted">Add New</span>
            </nav>
        </div>
        <a href="<?= base_url('project-staff') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Back to List
        </a>
    </div>
</div>

<!-- Create Form -->
<form id="createStaffForm" action="<?= base_url('project-staff/store'); ?>" enctype="multipart/form-data" method="post">

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
	<?php if ($this->session->flashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<i class="fas fa-check-circle me-2"></i>
			<?= $this->session->flashdata('error') ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	<?php endif; ?>
    <!-- Personal Information -->
    <div class="form-card">
        <h3 class="form-section-title">
            <i class="fas fa-user"></i>
            Personal Information
        </h3>
        <div class="col-md-12">
            <label for="name" class="form-label">Employee Id *</label>
            <input type="number" class="form-control <?= form_error('employee_id') ? 'is-invalid' : '' ?>"
                id="employee_id" name="employee_id" placeholder="Enter employee id"
                value="<?= set_value('employee_id') ?>">
            <?php if (form_error('employee_id')): ?>
                <div class="invalid-feedback">
                    <?= form_error('employee_id') ?>
                </div>
            <?php endif; ?>

        </div>
        <div class="row form-row">
            <div class="col-md-6">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name">
                <small class="text-danger"><?= form_error('name') ?></small>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address">
                <small class="text-danger"><?= form_error('email') ?></small>
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="dob" class="form-label">Date of Birth *</label>
                <input type="date" class="form-control" id="dob" name="dob">
                <small class="text-danger"><?= form_error('dob') ?></small>
            </div>
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender *</label>
                <select class="form-select select2" id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="mobile" class="form-label">Mobile Number *</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="+91 XXXXX XXXXX">
            </div>
            <div class="col-md-6">
                <label for="pan" class="form-label">PAN Number *</label>
                <input type="text" class="form-control" id="pan" name="pan" placeholder="ABCDE1234F"
                    style="text-transform: uppercase;">
            </div>
        </div>

        <div class="row form-row">
            <div class="col-12">
                <label for="address" class="form-label">Address *</label>
                <textarea class="form-control" id="address" name="address" rows="3"
                    placeholder="Enter complete address"></textarea>
            </div>
        </div>
    </div>

    <!-- Professional Information -->
    <div class="form-card">
        <h3 class="form-section-title">
            <i class="fas fa-briefcase"></i>
            Professional Information
        </h3>

		<div class="col-md-12">
			<label for="professional_email" class="form-label">Professional email *</label>
			<input type="email" class="form-control" placeholder="enter profession email" name="professional_email" id="professional_email"  />
		</div>
        <div class="row form-row">
            <div class="col-md-6">
                <label for="department" class="form-label">Department *</label>
                <select class="form-select select2" id="department" name="department">
                    <option value="">Select Department</option>
                    <option value="library science">Library Science</option>
                    <option value="computer science">Computer Science</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="reporting_officer" class="form-label">Reporting Officer *</label>
                <select class="form-select select2" id="reporting_officer" name="reporting_officer">
                    <option value="">Select Reporting Officer</option>
                    <?php if (!empty($reportingOfficers)): ?>
                        <?php foreach ($reportingOfficers as $officer): ?>
                            <option
                                value="<?php echo $officer->id; ?>,'.',<?php echo $officer->name; ?> ,'.',<?php echo $officer->designation; ?>">
                                <?php echo $officer->name; ?> - <?php echo $officer->designation; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No reporting officers found</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="role" class="form-label">Role/Position *</label>
                <select class="form-select select2" id="role" name="role">
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                    <option value="director">Director</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="role" class="form-label">Sub Role *</label>
                <select class="form-select select2" id="sub_role" name="sub_role">
                    <option value="">Select Sub Role</option>
                    <option value="admin">Admin</option>
                    <option value="admin_purchase">Admin Purchase</option>
                    <option value="admin_finance">Admin Finance</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Security Information -->
    <div class="form-card">
        <h3 class="form-section-title">
            <i class="fas fa-lock"></i>
            Security Information
        </h3>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="password" class="form-label">Password *</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Enter secure password">
                <div class="form-text">Password must be at least 8 characters long</div>
            </div>
            <div class="col-md-6">
                <label for="confirm_password" class="form-label">Confirm Password *</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                    placeholder="Confirm password">
            </div>
        </div>
    </div>

    <!-- Document Upload -->
    <div class="form-card">
        <h3 class="form-section-title">
            <i class="fas fa-upload"></i>
            Document Upload
        </h3>

        <div class="row form-row">
            <div class="col-md-6">
                <label class="form-label">Profile Photo *</label>
                <div class="file-upload-container" onclick="document.getElementById('photo').click()">
                    <div class="file-upload-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="file-upload-text">Click to upload profile photo</div>
                    <div class="file-upload-text">JPG, PNG (Max: 2MB)</div>
                    <button type="button" class="file-upload-btn">Choose Photo</button>
                </div>
                <input type="file" id="photo" name="photo" class="file-upload-input" accept="image/*">
                <div id="photo-preview" class="file-preview"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Digital Signature *</label>
                <div class="file-upload-container" onclick="document.getElementById('signature').click()">
                    <div class="file-upload-icon">
                        <i class="fas fa-signature"></i>
                    </div>
                    <div class="file-upload-text">Click to upload signature</div>
                    <div class="file-upload-text">JPG, PNG (Max: 1MB)</div>
                    <button type="button" class="file-upload-btn">Choose Signature</button>
                </div>
                <input type="file" id="signature" name="signature" class="file-upload-input" accept="image/*">
                <div id="signature-preview" class="file-preview"></div>
            </div>
        </div>
    </div>

    <!-- Initial Contract Information -->
    <div class="form-card">
        <h3 class="form-section-title">
            <i class="fas fa-file-contract"></i>
            Initial Contract Information
        </h3>

        <div class="row form-row">
            <div class="col-md-6">
                <label for="designation" class="form-label">Designation *</label>
                <select class="form-select select2" id="designation" name="designation">
                    <option value="">Select Designation</option>
                    <option value="Assistant">Assistant</option>
                    <option value="Software Developer">Software Developer</option>
                    <option value="Consultant">Consultant</option>
                    <option value="Library Officer">Library Officer</option>
                    <option value="Executive">Executive</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="join_date" class="form-label">Join Date *</label>
                <input type="date" class="form-control" id="join_date" name="join_date">
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-4">
                <label for="contract_months" class="form-label">Contract Duration (Months) *</label>
                <input type="number" class="form-control" id="contract_months" name="contract_months"
                    placeholder="Enter number of months">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Contract End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" placeholder="Auto-calculated" readonly>
            </div>
            <div class="col-md-4">
                <label for="salary" class="form-label">Monthly Salary *</label>
                <input type="number" class="form-control" id="salary" name="salary" placeholder="Enter amount in INR"
                    min="0" step="0.01">
            </div>
        </div>

        <div class="row form-row">
            <div class="col-md-4">
                <label for="project_name" class="form-label">Assigned Project *</label>
                <select class="form-select select2" id="project_name" name="project_name">
                    <option value="">Select Project</option>
                    <option value="Shodhganga">Shodhganga</option>
                    <option value="MOOCs">MOOCs</option>
                    <option value="SOUL">SOUL</option>
                    <option value="NIRF Project">NIRF Project</option>
                    <option value="PDS">PDS</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="location" class="form-label">Location *</label>
                <select class="form-select select2" id="location" name="location">
                    <option value="">Select location</option>
                    <option value="INFLIBNET">INFLIBNET</option>
                    <option value="UGC New Delhi">UGC New Delhi</option>
                    <option value="Assam">Assam</option>
                    <option value="AIU New Delhi">AIU New Delhi</option>
                </select>
            </div>
			<div class="col-md-4">
				<label for="offer_latter" class="form-label">offer latter *</label>
				<input type="file" class="form-control" id="offer_latter" name="offer_latter">
			</div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-card">
        <div class="d-flex justify-content-end gap-3">
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo me-2"></i>
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                Create Staff Member
            </button>
        </div>
    </div>
</form>
<?php $this->load->view('includes/footer'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
                    required: true,
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
                    required: true,
                    date: true
                },
                gender: {
                    required: true
                },
                mobile: {
                    required: true,
                    mobileFormat: true
                },
                pan: {
                    required: true,
                    panFormat: true
                },
                address: {
                    required: true,
                    minlength: 10,
                    maxlength: 500
                },
                department: {
                    required: true
                },
                role: {
                    required: true
                },
                sub_role: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 8,
                    maxlength: 50
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password"
                },
                designation: {
                    required: true
                },
                join_date: {
                    required: true,
                    date: true
                },
                contract_months: {
					required:true,
                    digits: true,
                    min: 1,
                    max: 120
                },
                salary: {
                    required: true,
                    number: true,
                    min: 0
                },
                project_name: {
                    required: true
                },
                location: {
                    required: true
                },
				offer_latter:{
					required:true,
					extension: "jpg|jpeg|png|pdf",
					filesize: 2097152 // 2MB in bytes
				},
                photo: {
					required:true,
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2097152 // 2MB in bytes
                },
                signature: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 1048576 // 1MB in bytes
                }
            },
            messages: {
                employee_id: {
                    required: "Please enter your employee ID",
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
                dob: {
                    required: "Please select your date of birth"
                },
                gender: {
                    required: "Please select your gender"
                },
                mobile: {
                    required: "Please enter your mobile number"
                },
                pan: {
                    required: "Please enter your PAN number"
                },
                address: {
                    required: "Please enter your address",
                    minlength: "Address must be at least 10 characters long"
                },
                department: {
                    required: "Please select a department"
                },
                role: {
                    required: "Please select a role"
                },
                sub_role: {
                    required: "Please select a sub role"
                },
                password: {
                    required: "Please enter a password",
                    minlength: "Password must be at least 8 characters long"
                },
                confirm_password: {
                    required: "Please confirm your password",
                    equalTo: "Passwords do not match"
                },
                designation: {
                    required: "Please enter designation"
                },
                join_date: {
                    required: "Please select join date"
                },
                salary: {
                    required: "Please enter monthly salary",
                    number: "Please enter a valid amount",
                    min: "Salary cannot be negative"
                },
                contract_months: {
					required:"please enter contract duration month",
                    digits: "Please enter a valid number of months",
                    min: "Contract duration must be at least 1 month",
                    max: "Contract duration cannot exceed 120 months"
                },
                project_name: {
                    required: "Please select a project"
                },
                location: {
                    required: "Please select a location"
                },
				offer_latter:{
					required:"please upload offer latter",
					extension: "Please select a valid image file (JPG, PNG,PDF)",
					filesize: "File size must be less than 2MB"
				},
                photo: {
					required:"employee photo is required",
                    extension: "Please select a valid image file (JPG, PNG)",
                    filesize: "File size must be less than 2MB"
                },
                signature: {
                    extension: "Please select a valid image file (JPG, PNG)",
                    filesize: "File size must be less than 1MB"
                }
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
                $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...').prop('disabled', true);

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


        // Auto-calculate contract end date
        $('#join_date, #contract_months').on('change input', function () {
            const joinDate = $('#join_date').val();
            const contractMonths = $('#contract_months').val();

            if (joinDate && contractMonths) {
                const startDate = new Date(joinDate);
                const endDate = new Date(startDate);
                endDate.setMonth(startDate.getMonth() + parseInt(contractMonths));

                const formattedEndDate = endDate.toISOString().split('T')[0];
                $('#end_date').val(formattedEndDate);
            } else {
                $('#end_date').val('');
            }
        });

        // Auto-set join date to today if empty
        const $joinDateInput = $('#join_date');
        if (!$joinDateInput.val()) {
            const today = new Date().toISOString().split('T')[0];
            $joinDateInput.val(today);
        }

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
