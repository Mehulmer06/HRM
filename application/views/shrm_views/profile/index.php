<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user"></i>
                My Profile
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <span class="text-muted">Profile</span>
            </nav>
        </div>
    </div>
</div>
<!-- Profile Header Card -->
<div class="detail-card">
    <div class="detail-header">
        <?php if (!empty($profiles->photo)): ?>
            <img src="<?= base_url('uploads/photo/' . $profiles->photo) ?>" alt="Profile" class="detail-photo">
        <?php else: ?>
            <div class="detail-photo"
                 style="background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                <i class="fas fa-user fa-2x"></i>
            </div>
        <?php endif; ?>
        <div class="detail-info">
            <h1><?= !empty($profiles->name) ? ucfirst($profiles->name) : '-' ?></h1>
            <p><?= (!empty($current_contract['designation']) ? ucfirst($current_contract['designation']) : '-') . ' • ' . (!empty($profiles->department) ? ucfirst($profiles->department) : '-') ?></p>
            <?php if (!empty($profiles->status)): ?>
                <span class="badge bg-success"><?= ucfirst($profiles->status) ?></span>
            <?php else: ?>
                <span class="badge bg-secondary">-</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Profile Tabs -->
<div class="staff-tabs">
    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
                    type="button" role="tab">
                <i class="fas fa-user me-2"></i>Personal Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional"
                    type="button" role="tab">
                <i class="fas fa-briefcase me-2"></i>Professional
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contracts-tab" data-bs-toggle="tab" data-bs-target="#contracts" type="button"
                    role="tab">
                <i class="fas fa-file-contract me-2"></i>Contracts
            </button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabContent">
        <!-- Personal Info Tab -->
        <div class="tab-pane fade show active" id="personal" role="tabpanel">
            <div class="form-card">
                <h3 class="form-section-title">
                    <i class="fas fa-user"></i>
                    Personal Information
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?= !empty($profiles->name) ? $profiles->name : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value"><?= !empty($profiles->dob) ? $profiles->dob : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?= !empty($profiles->gender) ? $profiles->gender : '-' ?></div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h3 class="form-section-title">
                    <i class="fas fa-address-card"></i>
                    Contact Information
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?= !empty($profiles->email) ? $profiles->email : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Mobile Number</div>
                        <div class="info-value"><?= !empty($profiles->phone) ? $profiles->phone : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">PAN Number</div>
                        <div class="info-value"><?= !empty($profiles->pan_number) ? $profiles->pan_number : '-' ?></div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h3 class="form-section-title">
                    <i class="fas fa-home"></i>
                    Address
                </h3>

                <div class="info-item">
                    <div class="info-label">Permanent Address</div>
                    <div class="info-value"><?= !empty($profiles->address) ? $profiles->address : '-' ?></div>
                </div>
            </div>
        </div>

        <!-- Professional Tab -->
        <div class="tab-pane fade" id="professional" role="tabpanel">
            <div class="form-card">
                <h3 class="form-section-title">
                    <i class="fas fa-briefcase"></i>
                    Current Position
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Designation</div>
                        <div class="info-value"><?= !empty($current_contract['designation']) ? $current_contract['designation'] : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Department</div>
                        <div class="info-value"><?= !empty($profiles->department) ? $profiles->department : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Employee ID</div>
                        <div class="info-value"><?= !empty($profiles->employee_id) ? $profiles->employee_id : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Reporting Manager</div>
                        <div class="info-value">
                            <?php
                            $reportingManager = '';
                            if (!empty($profiles->reporting_officer_name) || !empty($profiles->reporting_officer_designation)) {
                                $reportingManager = (!empty($profiles->reporting_officer_name) ? $profiles->reporting_officer_name : '') .
                                    (!empty($profiles->reporting_officer_designation) ? ' - ' . $profiles->reporting_officer_designation : '');
                                $reportingManager = trim($reportingManager, ' -');
                            }
                            echo !empty($reportingManager) ? $reportingManager : '-';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h3 class="form-section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Employment Details
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Join Date</div>
                        <div class="info-value"><?= !empty($current_contract['join_date']) ? date('Y M d', strtotime($current_contract['join_date'])) : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Experience</div>
                        <div class="info-value">
                            <?php
                            if (!empty($current_contract['contract_month'])) {
                                $months = (int)$current_contract['contract_month'];
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
                    <div class="info-item">
                        <div class="info-label">Work Location</div>
                        <div class="info-value"><?= !empty($current_contract['location']) ? $current_contract['location'] : '-' ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contracts Tab -->
        <div class="tab-pane fade" id="contracts" role="tabpanel">
            <div class="contract-section">
                <h3><i class="fas fa-file-contract"></i>Current Contract</h3>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Active Contract</h5>
                            <span class="status-badge status-active">Active</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-label">Start Date</div>
                                <div class="info-value"><?= !empty($current_contract['join_date']) ? date('Y M d', strtotime($current_contract['join_date'])) : '-' ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Project Name</div>
                                <div class="info-value"><?= !empty($current_contract['project_name']) ? $current_contract['project_name'] : '-' ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Salary</div>
                                <div class="info-value"><?= !empty($current_contract['salary']) ? '₹' . $current_contract['salary'] . '/month' : '-' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contract-section mt-4">
                <h3><i class="fas fa-history"></i>Contract History</h3>
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Previous Contracts</h5>
                            <span class="status-badge status-inactive">History</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($contract_history)) : ?>
                            <?php foreach ($contract_history as $contract) : ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="info-label">Start Date</div>
                                                <div class="info-value"><?= !empty($contract->join_date) ? date('Y M d', strtotime($contract->join_date)) : '-' ?></div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-label">End Date</div>
                                                <div class="info-value"><?= !empty($contract->end_date) ? date('Y M d', strtotime($contract->end_date)) : '-' ?></div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-label">Project Name</div>
                                                <div class="info-value"><?= !empty($contract->project_name) ? htmlspecialchars($contract->project_name) : '-' ?></div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-label">Salary</div>
                                                <div class="info-value"><?= !empty($contract->salary) ? '₹' . number_format($contract->salary) . '/month' : '-' ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>
                                No contract history found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control"
                                   value="<?= !empty($profiles->name) ? htmlspecialchars($profiles->name) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control"
                                   value="<?= !empty($profiles->email) ? htmlspecialchars($profiles->email) : '' ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control"
                                   value="<?= !empty($profiles->phone) ? htmlspecialchars($profiles->phone) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employee ID</label>
                            <input type="text" class="form-control"
                                   value="<?= !empty($profiles->employee_id) ? htmlspecialchars($profiles->employee_id) : '' ?>"
                                   readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control"
                                  rows="3"><?= !empty($profiles->address) ? htmlspecialchars($profiles->address) : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" accept="image/*">
                        <?php if (!empty($profiles->photo)): ?>
                            <small class="text-muted">Current photo: <?= htmlspecialchars($profiles->photo) ?></small>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Profile edit form submission
        document.querySelector('#editProfileModal .btn-primary').addEventListener('click', function () {
            // Add loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            this.disabled = true;

            // Simulate save
            setTimeout(() => {
                this.innerHTML = 'Save Changes';
                this.disabled = false;

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('editProfileModal')).hide();

                // Show success message
                showNotification('Profile updated successfully!', 'success');
            }, 1500);
        });
    });

    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }
</script>