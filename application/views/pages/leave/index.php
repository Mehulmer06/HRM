<?php $this->load->view('includes/header'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title"><i class="fas fa-user-clock"></i> Leave Management</h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <span class="text-muted">Leave Management</span>
            </nav>
        </div>
        <div>
            <a href="#" class="create-btn" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                <i class="fas fa-plus"></i> Apply Leave
            </a>
            <a href="<?= base_url('extra-day-requests') ?>" class="btn btn-warning ms-2">
                <i class="fas fa-calendar-plus"></i> Extra Day Request
            </a>
            <a href="<?= base_url('ro-extra-day-approval') ?>" class="btn btn-info ms-2">
                <i class="fas fa-user-shield"></i> RO Extra Day Approval
            </a>
        </div>
    </div>
</div>

<!-- Leave Balance Cards -->
<div class="row mb-4">
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #3498db;">
            <div class="stat-number" style="color: #3498db;">12</div>
            <div class="stat-label">Total CL</div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #9b59b6;">
            <div class="stat-number" style="color: #9b59b6;">3</div>
            <div class="stat-label">Extra Days</div>
        </div>
    </div>
</div>

<!-- Leave Tabs -->
<div class="staff-tabs">
    <ul class="nav nav-tabs" id="leaveTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                Pending
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                Approved
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                Rejected
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
                Cancel Leave
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Pending Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Name/Apply Date</th>
                            <th style="width: 12%;">From Date</th>
                            <th style="width: 12%;">To Date</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 10%;">Leave Type</th>
                            <th style="width: 12%;">RO Status</th>
                            <th style="width: 12%;">Admin Status</th>
                            <th style="width: 14%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>001</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/3498db/ffffff?text=JD"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="John Doe">
                                    <div>
                                        <div class="staff-name">John Doe</div>
                                        <small class="text-muted">2024-01-15</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-01-20</td>
                            <td>2024-01-22</td>
                            <td><span class="badge bg-primary">3</span></td>
                            <td><span class="badge bg-info">CL</span></td>
                            <td><span class="status-badge" style="background: #fff3cd; color: #856404;">Pending</span></td>
                            <td><span class="status-badge" style="background: #e2e3e5; color: #6c757d;">N/A</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i> Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-times me-2"></i> Cancel</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>002</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/e74c3c/ffffff?text=JS"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="Jane Smith">
                                    <div>
                                        <div class="staff-name">Jane Smith</div>
                                        <small class="text-muted">2024-01-14</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-01-25</td>
                            <td>2024-01-26</td>
                            <td><span class="badge bg-primary">2</span></td>
                            <td><span class="badge bg-warning">SL</span></td>
                            <td><span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span></td>
                            <td><span class="status-badge" style="background: #fff3cd; color: #856404;">Pending</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>003</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/f39c12/ffffff?text=AB"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="Alex Brown">
                                    <div>
                                        <div class="staff-name">Alex Brown</div>
                                        <small class="text-muted">2024-01-16</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-02-01</td>
                            <td>2024-02-03</td>
                            <td><span class="badge bg-primary">3</span></td>
                            <td><span class="badge bg-success">EL</span></td>
                            <td><span class="status-badge" style="background: #fff3cd; color: #856404;">Pending</span></td>
                            <td><span class="status-badge" style="background: #e2e3e5; color: #6c757d;">N/A</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i> Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-times me-2"></i> Cancel</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Approved Tab -->
        <div class="tab-pane fade" id="approved" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Name/Apply Date</th>
                            <th style="width: 12%;">From Date</th>
                            <th style="width: 12%;">To Date</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 10%;">Leave Type</th>
                            <th style="width: 12%;">RO Status</th>
                            <th style="width: 12%;">Admin Status</th>
                            <th style="width: 14%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>004</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/2ecc71/ffffff?text=MB"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="Mike Brown">
                                    <div>
                                        <div class="staff-name">Mike Brown</div>
                                        <small class="text-muted">2024-01-10</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-01-15</td>
                            <td>2024-01-17</td>
                            <td><span class="badge bg-primary">3</span></td>
                            <td><span class="badge bg-success">EL</span></td>
                            <td><span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span></td>
                            <td><span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i> Download</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>005</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/9b59b6/ffffff?text=LW"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="Lisa Wilson">
                                    <div>
                                        <div class="staff-name">Lisa Wilson</div>
                                        <small class="text-muted">2024-01-08</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-01-12</td>
                            <td>2024-01-14</td>
                            <td><span class="badge bg-primary">3</span></td>
                            <td><span class="badge bg-info">CL</span></td>
                            <td><span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span></td>
                            <td><span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i> Download</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rejected Tab -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Name/Apply Date</th>
                            <th style="width: 12%;">From Date</th>
                            <th style="width: 12%;">To Date</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 10%;">Leave Type</th>
                            <th style="width: 12%;">RO Status</th>
                            <th style="width: 12%;">Admin Status</th>
                            <th style="width: 14%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-danger">
                            <td>006</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/e67e22/ffffff?text=SW"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="Sarah Wilson">
                                    <div>
                                        <div class="staff-name">Sarah Wilson</div>
                                        <small class="text-muted">2024-01-12</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-01-18</td>
                            <td>2024-01-20</td>
                            <td><span class="badge bg-primary">3</span></td>
                            <td><span class="badge bg-info">CL</span></td>
                            <td><span class="status-badge" style="background: #f8d7da; color: #721c24;">Rejected</span></td>
                            <td><span class="status-badge" style="background: #e2e3e5; color: #6c757d;">N/A</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-redo me-2"></i> Reapply</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-danger">
                            <td>007</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/95a5a6/ffffff?text=TJ"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="Tom Johnson">
                                    <div>
                                        <div class="staff-name">Tom Johnson</div>
                                        <small class="text-muted">2024-01-13</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-01-22</td>
                            <td>2024-01-25</td>
                            <td><span class="badge bg-primary">4</span></td>
                            <td><span class="badge bg-warning">SL</span></td>
                            <td><span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span></td>
                            <td><span class="status-badge" style="background: #f8d7da; color: #721c24;">Rejected</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-redo me-2"></i> Reapply</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cancel Leave Tab -->
        <div class="tab-pane fade" id="cancelled" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Name/Apply Date</th>
                            <th style="width: 12%;">From Date</th>
                            <th style="width: 12%;">To Date</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 10%;">Leave Type</th>
                            <th style="width: 12%;">RO Status</th>
                            <th style="width: 12%;">Admin Status</th>
                            <th style="width: 14%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-warning">
                            <td>008</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/f39c12/ffffff?text=DM"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="David Miller">
                                    <div>
                                        <div class="staff-name">David Miller</div>
                                        <small class="text-muted">2024-01-11</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-01-19</td>
                            <td>2024-01-21</td>
                            <td><span class="badge bg-primary">3</span></td>
                            <td><span class="badge bg-info">CL</span></td>
                            <td><span class="status-badge" style="background: #f8d7da; color: #721c24;">Cancelled</span></td>
                            <td><span class="status-badge" style="background: #e2e3e5; color: #6c757d;">N/A</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-warning dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-ban"></i> Cancelled
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-warning">
                            <td>009</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/35x35/27ae60/ffffff?text=ER"
                                         class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                         alt="Emma Roberts">
                                    <div>
                                        <div class="staff-name">Emma Roberts</div>
                                        <small class="text-muted">2024-01-09</small>
                                    </div>
                                </div>
                            </td>
                            <td>2024-01-16</td>
                            <td>2024-01-17</td>
                            <td><span class="badge bg-primary">2</span></td>
                            <td><span class="badge bg-warning">SL</span></td>
                            <td><span class="status-badge" style="background: #f8d7da; color: #721c24;">Cancelled</span></td>
                            <td><span class="status-badge" style="background: #e2e3e5; color: #6c757d;">N/A</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-warning dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-ban"></i> Cancelled
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> View</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-labelledby="applyLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyLeaveModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Apply for Leave
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="applyLeaveForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Type of Leave -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="leaveType" class="form-label">Type of Leave *</label>
                            <select class="form-select" id="leaveType" name="leave_type" required>
                                <option value="">Select Leave Type</option>
                                <option value="fullday">Full Day</option>
                                <option value="halfday">Half Day</option>
                            </select>
                        </div>
                    </div>

                    <!-- Full Day Fields -->
                    <div id="fullDayFields" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fromDate" class="form-label">From Date *</label>
                                <input type="date" class="form-control" id="fromDate" name="from_date">
                            </div>
                            <div class="col-md-6">
                                <label for="toDate" class="form-label">To Date *</label>
                                <input type="date" class="form-control" id="toDate" name="to_date">
                            </div>
                        </div>
                    </div>

                    <!-- Half Day Fields -->
                    <div id="halfDayFields" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="halfDayDate" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="halfDayDate" name="half_day_date">
                            </div>
                            <div class="col-md-6">
                                <label for="timePeriod" class="form-label">Time Period *</label>
                                <select class="form-select" id="timePeriod" name="time_period">
                                    <option value="">Select Period</option>
                                    <option value="first_half">First Half (Morning)</option>
                                    <option value="second_half">Second Half (Afternoon)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Number of Days (Auto-calculated) -->
                    <div class="row mb-3" id="numberOfDaysField" style="display: none;">
                        <div class="col-md-6">
                            <label for="numberOfDays" class="form-label">Number of Days</label>
                            <input type="text" class="form-control" id="numberOfDays" name="number_of_days" readonly>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason *</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required
                                  placeholder="Enter reason for leave"></textarea>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Address During Leave *</label>
                        <textarea class="form-control" id="address" name="address" rows="2" required
                                  placeholder="Enter your address during leave period"></textarea>
                    </div>

                    <!-- Attachment -->
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" class="form-control" id="attachment" name="attachment"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Supported formats: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>
    $(document).ready(function() {
        // Initialize DataTables for all tables
        $('.table').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']], // Order by ID descending
            columnDefs: [
                { targets: -1, orderable: false }, // Actions column not sortable
            ],
            language: {
                search: "Search Leaves:",
                lengthMenu: "Show _MENU_ leaves per page",
                info: "Showing _START_ to _END_ of _TOTAL_ leaves",
            }
        });

        // Handle tab switching and refresh tables
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            setTimeout(function () {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }, 100);
        });

        // Handle Leave Type Change
        $('#leaveType').change(function() {
            const leaveType = $(this).val();

            if (leaveType === 'fullday') {
                $('#fullDayFields').show();
                $('#halfDayFields').hide();
                $('#numberOfDaysField').show();
                $('#fromDate, #toDate').attr('required', true);
                $('#halfDayDate, #timePeriod').attr('required', false);
                calculateDays();
            } else if (leaveType === 'halfday') {
                $('#fullDayFields').hide();
                $('#halfDayFields').show();
                $('#numberOfDaysField').show();
                $('#fromDate, #toDate').attr('required', false);
                $('#halfDayDate, #timePeriod').attr('required', true);
                $('#numberOfDays').val('0.5');
            } else {
                $('#fullDayFields').hide();
                $('#halfDayFields').hide();
                $('#numberOfDaysField').hide();
                $('#numberOfDays').val('');
            }
        });

        // Calculate days for full day leave
        function calculateDays() {
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            if (fromDate && toDate) {
                const startDate = new Date(fromDate);
                const endDate = new Date(toDate);

                if (endDate >= startDate) {
                    const timeDiff = endDate.getTime() - startDate.getTime();
                    const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                    $('#numberOfDays').val(dayDiff);
                } else {
                    $('#numberOfDays').val('');
                    alert('To date should be greater than or equal to from date');
                }
            }
        }

        // Date change events for calculation
        $('#fromDate, #toDate').change(function() {
            if ($('#leaveType').val() === 'fullday') {
                calculateDays();
            }
        });

        // Initialize jQuery Validation
        $('#applyLeaveForm').validate({
            rules: {
                leave_type: {
                    required: true
                },
                from_date: {
                    required: function() {
                        return $('#leaveType').val() === 'fullday';
                    }
                },
                to_date: {
                    required: function() {
                        return $('#leaveType').val() === 'fullday';
                    }
                },
                half_day_date: {
                    required: function() {
                        return $('#leaveType').val() === 'halfday';
                    }
                },
                time_period: {
                    required: function() {
                        return $('#leaveType').val() === 'halfday';
                    }
                },
                reason: {
                    required: true,
                    minlength: 10
                },
                address: {
                    required: true,
                    minlength: 10
                },
                attachment: {
                    extension: "pdf|doc|docx|jpg|jpeg|png"
                }
            },
            messages: {
                leave_type: {
                    required: "Please select a leave type"
                },
                from_date: {
                    required: "Please select from date"
                },
                to_date: {
                    required: "Please select to date"
                },
                half_day_date: {
                    required: "Please select date for half day leave"
                },
                time_period: {
                    required: "Please select time period"
                },
                reason: {
                    required: "Please enter reason for leave",
                    minlength: "Reason must be at least 10 characters long"
                },
                address: {
                    required: "Please enter your address during leave",
                    minlength: "Address must be at least 10 characters long"
                },
                attachment: {
                    extension: "Please upload a valid file (PDF, DOC, DOCX, JPG, PNG)"
                }
            },
            errorClass: 'text-danger',
            errorElement: 'small',
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            submitHandler: function(form) {
                // Check if number of days is calculated
                const numberOfDays = $('#numberOfDays').val();
                if (!numberOfDays || numberOfDays === '0') {
                    alert('Number of days must be calculated');
                    return false;
                }

                // File size validation (5MB)
                const fileInput = $('#attachment')[0];
                if (fileInput.files.length > 0) {
                    const fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
                    if (fileSize > 5) {
                        alert('File size should not exceed 5MB');
                        return false;
                    }
                }

                // Here you would normally submit to server
                // const formData = new FormData(form);
                // $.ajax({
                //     url: '<?= base_url('leave/apply') ?>',
                //     type: 'POST',
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     success: function(response) {
                //         // Handle success
                //     }
                // });

                alert('Leave application submitted successfully!');
                $('#applyLeaveModal').modal('hide');
                form.reset();
                $('#fullDayFields').hide();
                $('#halfDayFields').hide();
                $('#numberOfDaysField').hide();
                $('#numberOfDays').val('');
                return false; // Prevent actual form submission for demo
            }
        });

        // Reset form when modal is closed
        $('#applyLeaveModal').on('hidden.bs.modal', function() {
            $('#applyLeaveForm')[0].reset();
            $('#applyLeaveForm').validate().resetForm();
            $('#fullDayFields').hide();
            $('#halfDayFields').hide();
            $('#numberOfDaysField').hide();
            $('#numberOfDays').val('');

            // Remove validation classes
            $('#applyLeaveForm').find('.is-invalid').removeClass('is-invalid');
            $('#applyLeaveForm').find('.text-danger').remove();

            // Remove required attributes
            $('#fromDate, #toDate, #halfDayDate, #timePeriod').attr('required', false);
        });

        // Set minimum date to today for all date inputs
        const today = new Date().toISOString().split('T')[0];
        $('#fromDate, #toDate, #halfDayDate').attr('min', today);

        // Prevent selecting past dates
        $('#fromDate').change(function() {
            const fromDate = $(this).val();
            $('#toDate').attr('min', fromDate);
        });
    });
</script>