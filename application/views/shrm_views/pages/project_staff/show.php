<?php $this->load->view('shrm_views/includes/header'); ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user"></i>
                Staff Details
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard'); ?>">Dashboard</a> /
                <a href="<?= base_url('project-staff'); ?>">Project Staff</a> /
                <span class="text-muted"><?= !empty($user['name']) ? $user['name'] : '-' ?></span>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('project-staff'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Staff Profile Card -->
<div class="detail-card">
    <div class="detail-header">
        <?php if (!empty($user['photo'])): ?>
            <img src="<?= base_url('uploads/photo/' . $user['photo']) ?>" class="detail-photo" alt="photo">
        <?php else: ?>
            <div class="detail-photo" style="background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                <i class="fas fa-user fa-2x"></i>
            </div>
        <?php endif; ?>
        <div class="detail-info">
            <h1><?= !empty($user['name']) ? $user['name'] : '-' ?></h1>
            <p><?= (!empty($contract['designation']) ? $contract['designation'] : '-') . ' • ' . (!empty($user['department']) ? $user['department'] : '-') ?></p>
            <div class="mt-2">
                <span class="status-badge status-<?= ($user['status'] ?? '') === 'Y' ? 'active' : 'inactive' ?>">
                    <?= ($user['status'] ?? '') === 'Y' ? 'Active' : 'Inactive' ?>
                </span>
                <span class="badge bg-primary ms-2">Employee ID: <?= !empty($user['employee_id']) ? $user['employee_id'] : '-' ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Personal Information -->
<div class="detail-card">
    <div class="detail-body">
        <h3 class="form-section-title">
            <i class="fas fa-user"></i>
            Personal Information
        </h3>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Full Name</div>
                <div class="info-value"><?= !empty($user['name']) ? $user['name'] : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Email Address</div>
                <div class="info-value"><?= !empty($user['email']) ? $user['email'] : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Date of Birth</div>
                <div class="info-value"><?= !empty($user['dob']) ? $user['dob'] : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Gender</div>
                <div class="info-value"><?= !empty($user['gender']) ? $user['gender'] : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Mobile Number</div>
                <div class="info-value"><?= !empty($user['phone']) ? $user['phone'] : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">PAN Number</div>
                <div class="info-value"><?= !empty($user['pan_number']) ? $user['pan_number'] : '-' ?></div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="info-item">
                    <div class="info-label">Address</div>
                    <div class="info-value"><?= !empty($user['address']) ? $user['address'] : '-' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Professional Information -->
<div class="detail-card">
    <div class="detail-body">
        <h3 class="form-section-title">
            <i class="fas fa-briefcase"></i>
            Professional Information
        </h3>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Department</div>
                <div class="info-value"><?= !empty($user['department']) ? $user['department'] : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Current Designation</div>
                <div class="info-value"><?= !empty($contract['designation']) ? $contract['designation'] : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Reporting Officer</div>
                <div class="info-value">
                    <?php
                    $reportingOfficer = '';
                    if (!empty($user['reporting_officer_name']) || !empty($user['reporting_officer_designation'])) {
                        $reportingOfficer = (!empty($user['reporting_officer_name']) ? $user['reporting_officer_name'] : '') .
                            (!empty($user['reporting_officer_designation']) ? '-' . $user['reporting_officer_designation'] : '');
                        $reportingOfficer = trim($reportingOfficer, '-');
                    }
                    echo !empty($reportingOfficer) ? $reportingOfficer : '-';
                    ?>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Years of Service</div>
                <div class="info-value">
                    <?php
                    if (!empty($contract['contract_month'])) {
                        $months = (int) $contract['contract_month'];
                        $years = floor($months / 12);
                        $remainingMonths = $months % 12;

                        $parts = [];
                        if ($years > 0) {
                            $parts[] = $years . ' ' . ($years === 1 ? 'Year' : 'Years');
                        }
                        if ($remainingMonths > 0) {
                            $parts[] = $remainingMonths . ' ' . ($remainingMonths === 1 ? 'Month' : 'Months');
                        }
                        echo !empty($parts) ? implode(' ', $parts) : '-';
                    } else {
                        echo '-';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Contract Information -->
<div class="detail-card">
    <div class="detail-body">
        <h3 class="form-section-title">
            <i class="fas fa-file-contract"></i>
            Current Contract Information
        </h3>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Contract Start Date</div>
                <div class="info-value"><?= !empty($contract['join_date']) ? date('Y M d', strtotime($contract['join_date'])) : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Contract End Date</div>
                <div class="info-value"><?= !empty($contract['end_date']) ? date('Y M d', strtotime($contract['end_date'])) : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Monthly Salary</div>
                <div class="info-value"><?= !empty($contract['salary']) ? $contract['salary'] : '-' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Current Project</div>
                <div class="info-value"><?= !empty($contract['project_name']) ? $contract['project_name'] : '-' ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Documents -->
<div class="detail-card">
    <div class="detail-body">
        <h3 class="form-section-title">
            <i class="fas fa-folder"></i>
            Documents
        </h3>

        <div class="row">
            <div class="col-md-4">
                <div class="info-item">
                    <div class="info-label">Profile Photo</div>
                    <div class="info-value">
                        <?php if (!empty($user['photo'])): ?>
                            <img src="<?= base_url('uploads/photo/' . $user['photo']) ?>" alt="Profile Photo"
                                 style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 80px; height: 80px; border-radius: 8px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-item">
                    <div class="info-label">Digital Signature</div>
                    <div class="info-value">
                        <?php if (!empty($user['signature'])): ?>
                            <img src="<?= base_url('uploads/signature/' . $user['signature']) ?>" alt="Digital Signature"
                                 style="width: 120px; height: 40px; border-radius: 4px; border: 1px solid #dee2e6;">
                        <?php else: ?>
                            <span style="color: #6c757d;">-</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-item">
                    <div class="info-label">Offer Letter</div>
                    <div class="info-value">
                        <?php if (!empty($contract['offer_latter'])): ?>
                            <a href="<?= base_url('uploads/offer_latter/' . $contract['offer_latter']) ?>" download
                               style="text-decoration: none; color: #dc3545; font-size: 24px;">
                                <i class="fas fa-file-pdf"></i> Download Now
                            </a>
                        <?php else: ?>
                            <span style="color: #6c757d;">-</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contract History -->
<div class="contract-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>
            <i class="fas fa-history"></i>
            Contract History
        </h3>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addContractModal">
            <i class="fas fa-plus me-2"></i>
            Add New Contract
        </button>
    </div>

    <div class="table-container">
        <table id="contractHistoryTable" class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Sr No.</th>
                <th>Designation</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Month</th>
                <th>Monthly Salary</th>
                <th>Project</th>
                <th>Status</th>
                <!-- <th>Actions</th> -->
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($contractList)): ?>
                <?php foreach ($contractList as $index => $contract): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= !empty($contract['designation']) ? $contract['designation'] : '-' ?></td>
                        <td><?= !empty($contract['join_date']) ? date('Y M d', strtotime($contract['join_date'])) : '-' ?></td>
                        <td><?= !empty($contract['end_date']) ? date('Y M d', strtotime($contract['end_date'])) : '-' ?></td>
                        <td><?= !empty($contract['contract_month']) ? $contract['contract_month'] : '-' ?></td>
                        <td><?= !empty($contract['salary']) ? $contract['salary'] : '-' ?></td>
                        <td><?= !empty($contract['project_name']) ? $contract['project_name'] : '-' ?></td>
                        <td>
                            <?php if (!empty($contract['status'])): ?>
                                <span class="status-badge status-<?= strtolower($contract['status']) ?>">
                                        <?= ucfirst($contract['status']) ?>
                                    </span>
                            <?php else: ?>
                                <span style="color: #6c757d;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Add Contract Modal -->
<div class="modal fade" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Add New Contract
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addContractForm" action="<?= base_url('project-staff/renewal-contract/') . $user['id']; ?>"
                      method="POST" enctype="multipart/form-data">
                    <?php
                    $csrf = array(
                        'name' => $this->security->get_csrf_token_name(),
                        'hash' => $this->security->get_csrf_hash()
                    );
                    ?>
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modal_designation" class="form-label">Designation *</label>
                            <select class="form-select" id="modal_designation" name="modal_designation">
                                <option value="">Select Designation</option>
                                <option value="Assistant">Assistant</option>
                                <option value="Software Developer">Software Developer</option>
                                <option value="Consultant">Consultant</option>
                                <option value="Library Officer">Library Officer</option>
                                <option value="Executive">Executive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" id="modal_start_date" name="start_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contract_months" class="form-label">Month *</label>
                            <input type="number" class="form-control" id="contract_months" name="contract_months"
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="modal_end_date" name="end_date">
                        </div>
                    </div>

                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label for="modal_salary" class="form-label">Monthly Salary *</label>
                            <input type="number" class="form-control" id="modal_salary" name="salary" required min="0"
                                   step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Location *</label>
                            <select class="form-select select2" id="location" name="location">
                                <option value="">Select location</option>
                                <option value="INFLIBNET">INFLIBNET</option>
                                <option value="UGC New Delhi">UGC New Delhi</option>
                                <option value="Assam">Assam</option>
                                <option value="AIU New Delhi">AIU New Delhi</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
						<div class="col-md-6">
							<label for="project_name" class="form-label">Assigned Project *</label>
							<select class="form-select select2" id="project_name" name="project_name">
								<option value="">Select Project</option>
								<?php if (!empty($projects)): ?>
									<?php foreach ($projects as $project): ?>
										<option value="<?= $project->id ?>"><?= $project->project_name ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>
                        <div class="col-md-6 d-none">
                            <label for="modal_status" class="form-label">Status *</label>
                            <select class="form-select" id="modal_status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active" selected>Active</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="offer_latter" class="form-label">offer latter *</label>
                            <input type="file" class="form-control" id="offer_latter" name="offer_latter" accept="application/pdf">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveContractBtn">
                    <i class="fas fa-save me-2"></i>
                    Save Contract
                </button>

            </div>
        </div>
    </div>
</div>
<?php $this->load->view('shrm_views/includes/footer'); ?>
<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#contractHistoryTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "Search Contracts:",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });

        // Set default start date to today
        $('#modal_start_date').val(new Date().toISOString().split('T')[0]);

        // Salary tooltip formatting
        $('#modal_salary').on('input', function () {
            let value = parseFloat(this.value);
            if (!isNaN(value)) {
                $(this).attr('title', `₹${value.toLocaleString('en-IN')}`);
            }
        });

        // jQuery Validation
        $('#addContractForm').validate({
            rules: {
                modal_designation: "required",
                start_date: "required",
                modal_month: "required",
                end_date: "required",
                location: "required",
                offer_latter:"required",
                salary: {
                    required: true,
                    number: true,
                    min: 0
                },
                status: "required",
				project_name: "required"
            },
            messages: {
                offer_latter:"please upload offer latter",
                modal_designation: "Please enter a designation",
                start_date: "Start date is required",
                modal_month: "Please select a month",
                end_date: "End date is required",
                location: "Please select a location",
                salary: {
                    required: "Salary is required",
                    number: "Enter a valid number",
                    min: "Salary must be positive"
                },
                status: "Please select a status",
				project_name: "Please select a project",

            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function (form) {
                // Form passed validation - now submit
                const modal = bootstrap.Modal.getInstance(document.getElementById('addContractModal'));
                modal.hide();
                form.submit();
            }
        });

        // Save button click triggers form submit
        $('#saveContractBtn').on('click', function () {
            $('#addContractForm').submit();
        });


        // Auto-calculate contract end date
        $('#modal_start_date, #contract_months').on('change input', function () {
            const joinDate = $('#modal_start_date').val();
            const contractMonths = $('#contract_months').val();

            if (joinDate && contractMonths) {
                const startDate = new Date(joinDate);
                const endDate = new Date(startDate);
                endDate.setMonth(startDate.getMonth() + parseInt(contractMonths));

                const formattedEndDate = endDate.toISOString().split('T')[0];
                $('#modal_end_date').val(formattedEndDate);
            } else {
                $('#modal_end_date').val('');
            }
        });

        // Auto-set join date to today if empty
        const $joinDateInput = $('#modal_start_date');
        if (!$joinDateInput.val()) {
            const today = new Date().toISOString().split('T')[0];
            $joinDateInput.val(today);
        }
    });
</script>
