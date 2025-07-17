<?php $this->load->view('includes/header');
include('./application/views/pages/message.php');
?>


<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-clock"></i> Leave Management
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <span class="text-muted">Leave Management</span>
            </nav>
        </div>
        <div>
            <a href="#" class="create-btn" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                <i class="fas fa-plus"></i> Apply Leave
            </a>
            <a href="<?= base_url('casual-leave') ?>" class="create-btn btn btn-success ms-2">
                <i class="fas fa-plus-circle"></i> CL Add Module
            </a>
            <a href="<?= base_url('extra-day-requests') ?>" class="create-btn btn btn-warning ms-2">
                <i class="fas fa-calendar-plus"></i> Extra Day Request
            </a>
            <a href="<?= base_url('ro-extra-day-approval') ?>" class="create-btn btn btn-info ms-2">
                <i class="fas fa-user-shield"></i> RO Extra Day Approval
            </a>
        </div>
    </div>
</div>

<!-- Leave Balance Cards -->
<div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #3498db;">
            <div class="stat-number" style="color: #3498db;">
                <?= isset($leave_balance['total_cl']) ? $leave_balance['total_cl'] : '0' ?>
            </div>
            <div class="stat-label">Total CL</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #2ecc71;">
            <div class="stat-number" style="color: #2ecc71;">
                <?= isset($leave_balance['used_cl']) ? $leave_balance['used_cl'] : '0' ?>
            </div>
            <div class="stat-label">CL Used</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #9b59b6;">
            <div class="stat-number" style="color: #9b59b6;">
                <?= isset($leave_balance['available_extra']) ? $leave_balance['available_extra'] : '0' ?>
            </div>
            <div class="stat-label">Extra Days</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #e67e22;">
            <div class="stat-number" style="color: #e67e22;">
                <?= isset($leave_balance['used_extra']) ? $leave_balance['used_extra'] : '0' ?>
            </div>
            <div class="stat-label">Extra Days Used</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #e74c3c;">
            <div class="stat-number" style="color: #e74c3c;">
                <?= isset($leave_balance['used_paid']) ? $leave_balance['used_paid'] : '0' ?>
            </div>
            <div class="stat-label">Paid Leave Used</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #f39c12;">
            <div class="stat-number" style="color: #f39c12;">
                <?= isset($leave_balance['remaining_cl']) ? $leave_balance['remaining_cl'] : '0' ?>
            </div>
            <div class="stat-label">Remaining CL</div>
        </div>
    </div>
</div>

<!-- Leave Tabs -->
<div class="staff-tabs">
    <ul class="nav nav-tabs" id="leaveTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                Pending (<?= !empty($pending_leaves) ? count($pending_leaves) : 0 ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                Approved (<?= !empty($approved_leaves) ? count($approved_leaves) : 0 ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                Rejected (<?= !empty($rejected_leaves) ? count($rejected_leaves) : 0 ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
                Cancelled (<?= !empty($cancelled_leaves) ? count($cancelled_leaves) : 0 ?>)
            </button>
        </li>
        <!-- ✅ NEW TAB FOR RO CANCELLATION REQUESTS -->

        <li class="nav-item">
            <button class="nav-link text-warning" data-bs-toggle="tab" data-bs-target="#pending_cancellations"
                    type="button" role="tab">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Pending Cancellations (<?= !empty($pending_cancellations) ? count($pending_cancellations) : 0 ?>)
            </button>
        </li>

    </ul>

    <div class="tab-content">

        <!-- ✅ NEW PENDING CANCELLATIONS TAB FOR RO -->

        <div class="tab-pane fade" id="pending_cancellations" role="tabpanel">
            <div class="table-container">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>RO Action Required:</strong> Review and take action on pending leave cancellation requests
                    below.
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="pendingCancellationsTable">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Employee</th>
                            <th style="width: 12%;">Cancellation Type</th>
                            <th style="width: 10%;">Original Leave</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 12%;">Requested On</th>
                            <th style="width: 15%;">Remarks</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pending_cancellations)): ?>
                            <?php foreach ($pending_cancellations as $cancellation): ?>
                                <tr class="table-warning">
                                    <td><?= $cancellation->cancel_id ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/30x30/f39c12/ffffff?text=<?= substr($cancellation->requester_name, 0, 2) ?>"
                                                 class="rounded-circle me-2" style="width: 30px; height: 30px;"
                                                 alt="<?= $cancellation->requester_name ?>">
                                            <div>
                                                <div class="fw-bold"
                                                     style="font-size: 0.9rem;"><?= $cancellation->requester_name ?></div>
                                                <small class="text-muted">Leave
                                                    ID: <?= $cancellation->leave_request_id ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                <span class="badge <?= $cancellation->leave_request_day_id ? 'bg-info' : 'bg-warning' ?>">
                                    <?= $cancellation->cancellation_type ?>
                                </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?= date('M d', strtotime($cancellation->start_date)) ?> -
                                            <?= date('M d, Y', strtotime($cancellation->end_date)) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?= $cancellation->total_days ?></span>
                                    </td>
                                    <td>
                                        <small><?= date('M d, Y', strtotime($cancellation->requested_at)) ?></small>
                                        <br>
                                        <small class="text-muted"><?= date('H:i', strtotime($cancellation->requested_at)) ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= $cancellation->cancel_remarks ? substr($cancellation->cancel_remarks, 0, 50) . (strlen($cancellation->cancel_remarks) > 50 ? '...' : '') : 'No remarks' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="status-badge bg-warning text-dark">Pending</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                                onclick="openRoCancelActionModal(<?= $cancellation->cancel_id ?>)">
                                            <i class="fas fa-gavel me-1"></i> Take Action
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Pending Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="pendingTable">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 18%;">Name/Apply Date</th>
                            <th style="width: 10%;">From Date</th>
                            <th style="width: 10%;">To Date</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 8%;">CL Used</th>
                            <th style="width: 8%;">Extra Used</th>
                            <th style="width: 8%;">Paid Used</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 13%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pending_leaves)): ?>
                            <?php foreach ($pending_leaves as $leave): ?>
                                <tr>
                                    <td><?= $leave->id ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/3498db/ffffff?text=<?= substr($leave->name, 0, 2) ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $leave->name ?>">
                                            <div>
                                                <div class="staff-name"><?= $leave->name ?></div>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($leave->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($leave->start_date)) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave->end_date)) ?></td>
                                    <td><span class="badge bg-primary"><?= $leave->total_days ?></span></td>
                                    <td><span class="badge bg-info"><?= $leave->cl_used ?></span></td>
                                    <td>
                                        <span class="badge bg-purple"><?= isset($leave->extra_used) ? $leave->extra_used : '0' ?></span>
                                    </td>
                                    <td><span class="badge bg-danger"><?= $leave->paid_used ?></span></td>
                                    <td>
                                        <span class="status-badge bg-warning text-dark"><?= ucfirst($leave->status) ?></span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                       onclick="viewLeave(<?= $leave->id ?>)">
                                                        <i class="fas fa-eye me-2"></i> View
                                                    </a>
                                                </li>
                                                <?php if ($leave->user_id == $this->session->userdata('user_id')): ?>
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           onclick="editLeave(<?= $leave->id ?>)">
                                                            <i class="fas fa-edit me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                           onclick="deleteLeave(<?= $leave->id ?>)">
                                                            <i class="fas fa-trash me-2"></i> Delete
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                                <li>
                                                    <a class="dropdown-item text-success" href="#"
                                                       onclick="openActionModal(<?= $leave->id ?>)">
                                                        <i class="fas fa-check-circle me-2"></i> Take Action
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Approved Tab -->
        <div class="tab-pane fade" id="approved" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="approvedTable">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 18%;">Name/Apply Date</th>
                            <th style="width: 10%;">From Date</th>
                            <th style="width: 10%;">To Date</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 8%;">CL Used</th>
                            <th style="width: 8%;">Extra Used</th>
                            <th style="width: 8%;">Paid Used</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 13%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($approved_leaves)): ?>
                            <?php foreach ($approved_leaves as $leave): ?>
                                <tr>
                                    <td><?= $leave->id ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/2ecc71/ffffff?text=<?= substr($leave->name, 0, 2) ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $leave->name ?>">
                                            <div>
                                                <div class="staff-name"><?= $leave->name ?></div>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($leave->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($leave->start_date)) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave->end_date)) ?></td>
                                    <td><span class="badge bg-primary"><?= $leave->total_days ?></span></td>
                                    <td><span class="badge bg-info"><?= $leave->cl_used ?></span></td>
                                    <td>
                                        <span class="badge bg-purple"><?= isset($leave->extra_used) ? $leave->extra_used : '0' ?></span>
                                    </td>
                                    <td><span class="badge bg-danger"><?= $leave->paid_used ?></span></td>
                                    <td>
                                        <span class="status-badge bg-success"><?= ucfirst($leave->status) ?></span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                       onclick="viewLeave(<?= $leave->id ?>)">
                                                        <i class="fas fa-eye me-2"></i> View
                                                    </a>
                                                </li>
                                                <?php if ($leave->user_id == $this->session->userdata('user_id')): ?>
                                                    <li>
                                                        <a class="dropdown-item text-warning" href="#"
                                                           onclick="openCancelModal(<?= $leave->id ?>)">
                                                            <i class="fas fa-minus-circle me-2"></i> Request
                                                            Cancellation
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Rejected Tab -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="rejectedTable">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 18%;">Name/Apply Date</th>
                            <th style="width: 10%;">From Date</th>
                            <th style="width: 10%;">To Date</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 8%;">CL Used</th>
                            <th style="width: 8%;">Extra Used</th>
                            <th style="width: 8%;">Paid Used</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 13%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($rejected_leaves)): ?>
                            <?php foreach ($rejected_leaves as $leave): ?>
                                <tr class="table-danger">
                                    <td><?= $leave->id ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/e67e22/ffffff?text=<?= substr($leave->name, 0, 2) ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $leave->name ?>">
                                            <div>
                                                <div class="staff-name"><?= $leave->name ?></div>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($leave->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($leave->start_date)) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave->end_date)) ?></td>
                                    <td><span class="badge bg-primary"><?= $leave->total_days ?></span></td>
                                    <td><span class="badge bg-info"><?= $leave->cl_used ?></span></td>
                                    <td>
                                        <span class="badge bg-purple"><?= isset($leave->extra_used) ? $leave->extra_used : '0' ?></span>
                                    </td>
                                    <td><span class="badge bg-danger"><?= $leave->paid_used ?></span></td>
                                    <td>
                                        <span class="status-badge bg-danger"><?= ucfirst($leave->status) ?></span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                       onclick="viewLeave(<?= $leave->id ?>)">
                                                        <i class="fas fa-eye me-2"></i> View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                       onclick="reapplyLeave(<?= $leave->id ?>)">
                                                        <i class="fas fa-redo me-2"></i> Reapply
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cancelled Tab -->
        <div class="tab-pane fade" id="cancelled" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="cancelledTable">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 18%;">Name/Apply Date</th>
                            <th style="width: 10%;">From Date</th>
                            <th style="width: 10%;">To Date</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 8%;">CL Used</th>
                            <th style="width: 8%;">Extra Used</th>
                            <th style="width: 8%;">Paid Used</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 13%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($cancelled_leaves)): ?>
                            <?php foreach ($cancelled_leaves as $leave): ?>
                                <tr class="table-warning">
                                    <td><?= $leave->id ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/f39c12/ffffff?text=<?= substr($leave->name, 0, 2) ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $leave->name ?>">
                                            <div>
                                                <div class="staff-name"><?= $leave->name ?></div>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($leave->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($leave->start_date)) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave->end_date)) ?></td>
                                    <td><span class="badge bg-primary"><?= $leave->total_days ?></span></td>
                                    <td><span class="badge bg-info"><?= $leave->cl_used ?></span></td>
                                    <td>
                                        <span class="badge bg-purple"><?= isset($leave->extra_used) ? $leave->extra_used : '0' ?></span>
                                    </td>
                                    <td><span class="badge bg-danger"><?= $leave->paid_used ?></span></td>
                                    <td>
                                        <span class="status-badge bg-warning">Cancelled</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-warning dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ban"></i> Cancelled
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                       onclick="viewLeave(<?= $leave->id ?>)">
                                                        <i class="fas fa-eye me-2"></i> View
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
            <form id="applyLeaveForm" enctype="multipart/form-data" method="post"
                  action="<?= base_url('leave/apply') ?>">
                <div class="modal-body">
                    <!-- Leave Type -->
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
                                <input type="date" class="form-control" id="fromDate" name="start_date">
                            </div>
                            <div class="col-md-6">
                                <label for="toDate" class="form-label">To Date *</label>
                                <input type="date" class="form-control" id="toDate" name="end_date">
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

                    <!-- Number of Days -->
                    <div class="row mb-3" id="numberOfDaysField" style="display: none;">
                        <div class="col-md-6">
                            <label for="numberOfDays" class="form-label">Number of Days</label>
                            <input type="text" class="form-control" id="numberOfDays" name="total_days" readonly>
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
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane me-1"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Leave Modal -->
<div class="modal fade" id="editLeaveModal" tabindex="-1" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editLeaveForm" method="post" action="<?= base_url('leave/update') ?>"
                  enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLeaveModalLabel">
                        <i class="fas fa-edit me-2"></i> Edit Leave Application
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="leave_id" id="edit_leave_id">
                    <input type="hidden" name="leave_type" id="edit_leave_type">

                    <!-- Full Day Fields -->
                    <div id="edit_fullday_fields" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_start_date" class="form-label">From Date *</label>
                                <input type="date" class="form-control" name="start_date" id="edit_start_date">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_end_date" class="form-label">To Date *</label>
                                <input type="date" class="form-control" name="end_date" id="edit_end_date">
                            </div>
                        </div>
                    </div>

                    <!-- Half Day Fields -->
                    <div id="edit_halfday_fields" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_half_day_date" class="form-label">Date *</label>
                                <input type="date" class="form-control" name="half_day_date" id="edit_half_day_date">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_time_period" class="form-label">Time Period *</label>
                                <select class="form-select" name="time_period" id="edit_time_period">
                                    <option value="">Select Period</option>
                                    <option value="first_half">First Half (Morning)</option>
                                    <option value="second_half">Second Half (Afternoon)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Number of Days -->
                    <div class="row mb-3" id="edit_number_of_days_field" style="display: none;">
                        <div class="col-md-6">
                            <label for="edit_total_days" class="form-label">Number of Days</label>
                            <input type="text" class="form-control" name="total_days" id="edit_total_days" readonly>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="mb-3">
                        <label for="edit_reason" class="form-label">Reason *</label>
                        <textarea class="form-control" name="reason" id="edit_reason" rows="3" required></textarea>
                    </div>

                    <!-- Edit Reason -->
                    <div class="mb-3">
                        <label for="edit_edit_reason" class="form-label">Edit Reason *</label>
                        <textarea class="form-control" name="edit_reason" id="edit_edit_reason" rows="2"
                                  required></textarea>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address During Leave *</label>
                        <textarea class="form-control" name="address" id="edit_address" rows="2" required></textarea>
                    </div>

                    <!-- Attachment -->
                    <div class="mb-3">
                        <label for="edit_attachment" class="form-label">Attachment</label>
                        <input type="file" class="form-control" name="attachment" id="edit_attachment"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Supported formats: PDF, DOC, JPG, PNG. Max: 5MB</small>
                        <div id="current_file_text" class="text-primary mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Leave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="takeActionModal" tabindex="-1" aria-labelledby="takeActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="actionForm" method="post" action="<?= base_url('leave/take_action') ?>">
            <input type="hidden" name="leave_id" id="action_leave_id">
            <input type="hidden" name="action" id="action_type">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="takeActionModalLabel">
                        <i class="fas fa-check-circle me-2"></i> Take Action on Leave
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="action_remark" class="form-label">Remark *</label>
                        <textarea class="form-control" id="action_remark" name="remark" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" class="btn btn-success" onclick="setActionType('approved')">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                    <button type="submit" class="btn btn-danger" onclick="setActionType('rejected')">
                        <i class="fas fa-times me-1"></i> Reject
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewLeaveModal" tabindex="-1" aria-labelledby="viewLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewLeaveModalLabel">
                    <i class="fas fa-eye me-2"></i> Leave Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Dynamic content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- ✅ UPDATED CANCEL LEAVE MODAL -->
<div class="modal fade" id="cancelLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="<?= base_url('leave/cancel') ?>" id="cancelLeaveForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-ban me-2"></i> Cancel Leave Request
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="leave_id" id="cancel_leave_id">

                    <div class="mb-3">
                        <label class="form-label">Cancellation Type *</label>
                        <select name="cancel_type" class="form-select" id="cancel_type_select" required>
                            <option value="full">Full Leave Cancellation</option>
                            <option value="partial">Partial (Select Specific Days)</option>
                        </select>
                        <small class="text-muted">Choose whether to cancel the entire leave or specific days
                            only.</small>
                    </div>

                    <div class="mb-3" id="partial_dates_box" style="display: none;">
                        <label class="form-label">Select Days to Cancel *</label>
                        <div id="partial_dates_container" class="border rounded p-2"
                             style="max-height: 200px; overflow-y: auto;">
                            <!-- Days will be populated via JavaScript -->
                        </div>
                        <small class="text-muted">Select the specific days you want to cancel from your approved
                            leave.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason for Cancellation</label>
                        <textarea name="remarks" class="form-control" rows="3"
                                  placeholder="Enter reason for cancelling this leave (optional)"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> This will create a cancellation request that needs to be approved by RO.
                        Your CL/Extra days will be restored only after RO approval.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane me-1"></i> Submit Cancellation Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ✅ RO CANCEL ACTION MODAL -->
<div class="modal fade" id="roCancelActionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="<?= base_url('leave/cancel_action') ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-shield me-2"></i> Review Cancellation Request
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="cancel_id" id="ro_cancel_id">
                    <input type="hidden" name="action" id="ro_cancel_action">

                    <div class="mb-3">
                        <label class="form-label">Leave Information</label>
                        <div id="ro_cancel_leave_info" class="border rounded p-2 bg-light">
                            <!-- Leave info will be populated -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ro_cancel_remark" class="form-label">RO Remarks *</label>
                        <textarea class="form-control" name="remark" id="ro_cancel_remark"
                                  rows="3" required placeholder="Enter your remarks for this decision"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" class="btn btn-success" onclick="setRoCancelAction('approved')">
                        <i class="fas fa-check me-1"></i> Approve Cancellation
                    </button>
                    <button type="submit" class="btn btn-danger" onclick="setRoCancelAction('rejected')">
                        <i class="fas fa-times me-1"></i> Reject Cancellation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- ✅ RO CANCEL ACTION MODAL -->
<div class="modal fade" id="roCancelActionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="<?= base_url('leave/cancel_action') ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-shield me-2"></i> Review Cancellation Request
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="cancel_id" id="ro_cancel_id">
                    <input type="hidden" name="action" id="ro_cancel_action">

                    <div class="mb-3">
                        <label class="form-label">Cancellation Request Details</label>
                        <div id="ro_cancel_leave_info" class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-center align-items-center" style="min-height: 100px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ro_cancel_remark" class="form-label">RO Remarks *</label>
                        <textarea class="form-control" name="remark" id="ro_cancel_remark"
                                  rows="3" required placeholder="Enter your remarks for this decision"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" class="btn btn-success" onclick="setRoCancelAction('approved')">
                        <i class="fas fa-check me-1"></i> Approve Cancellation
                    </button>
                    <button type="submit" class="btn btn-danger" onclick="setRoCancelAction('rejected')">
                        <i class="fas fa-times me-1"></i> Reject Cancellation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .badge.bg-purple {
        background-color: #9b59b6 !important;
    }
</style>

<?php $this->load->view('includes/footer'); ?>

<script>
    const holidayDates = <?= $holiday_dates_js ?? '[]' ?>;
    $(document).ready(function () {
        initializeDataTables();
        initializeFormValidation();
        initializeEventHandlers();
    });

    // Initialize DataTables
    function initializeDataTables() {
        const config = {
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            columnDefs: [{targets: -1, orderable: false}],
            language: {
                search: "Search Leaves:",
                lengthMenu: "Show _MENU_ leaves per page",
                info: "Showing _START_ to _END_ of _TOTAL_ leaves"
            }
        };

        $('#pendingTable, #approvedTable, #rejectedTable, #cancelledTable, #pendingCancellationsTable').DataTable(config);
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            setTimeout(() => $.fn.dataTable.tables({visible: true, api: true}).columns.adjust(), 100);
        });
    }

    // Initialize Form Validation
    function initializeFormValidation() {
        // Add custom validation method for file extension
        $.validator.addMethod("extension", function (value, element, param) {
            if (element.files && element.files.length > 0) {
                const fileName = element.files[0].name;
                const extension = fileName.split('.').pop().toLowerCase();
                return param.split('|').includes(extension);
            }
            return true; // No file selected is valid for optional fields
        }, "Please upload a valid file format.");

        // Add custom validation method for file size
        $.validator.addMethod("filesize", function (value, element, param) {
            if (element.files && element.files.length > 0) {
                return element.files[0].size <= param;
            }
            return true; // No file selected is valid for optional fields
        }, "File size must be less than 5MB");

        // Apply Leave Form Validation
        $("#applyLeaveForm").validate({
            rules: {
                leave_type: {
                    required: true
                },
                start_date: {
                    required: function (element) {
                        return $("#leaveType").val() === "fullday";
                    },
                    date: true
                },
                end_date: {
                    required: function (element) {
                        return $("#leaveType").val() === "fullday";
                    },
                    date: true
                },
                half_day_date: {
                    required: function (element) {
                        return $("#leaveType").val() === "halfday";
                    },
                    date: true
                },
                time_period: {
                    required: function (element) {
                        return $("#leaveType").val() === "halfday";
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
                    extension: "pdf|doc|docx|jpg|jpeg|png",
                    filesize: 5242880 // 5MB in bytes
                }
            },
            messages: {
                leave_type: {
                    required: "Please select a leave type"
                },
                start_date: {
                    required: "Please select from date",
                    date: "Please enter a valid date"
                },
                end_date: {
                    required: "Please select to date",
                    date: "Please enter a valid date"
                },
                half_day_date: {
                    required: "Please select date",
                    date: "Please enter a valid date"
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
                    extension: "Please upload a valid file format (PDF, DOC, DOCX, JPG, PNG)",
                    filesize: "File size must be less than 5MB"
                }
            },
            errorElement: "div",
            errorClass: "invalid-feedback",
            validClass: "is-valid",
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.closest('.input-group').length) {
                    error.insertAfter(element.closest('.input-group'));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                // handleFormSubmission(form);
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Submitting...');
                form.submit();
            }
        });

        // Edit Leave Form Validation
        $("#editLeaveForm").validate({
            rules: {
                start_date: {
                    required: function (element) {
                        return $("#edit_leave_type").val() === "fullday";
                    },
                    date: true
                },
                end_date: {
                    required: function (element) {
                        return $("#edit_leave_type").val() === "fullday";
                    },
                    date: true
                },
                half_day_date: {
                    required: function (element) {
                        return $("#edit_leave_type").val() === "halfday";
                    },
                    date: true
                },
                time_period: {
                    required: function (element) {
                        return $("#edit_leave_type").val() === "halfday";
                    }
                },
                reason: {
                    required: true,
                    minlength: 10
                },
                edit_reason: {
                    required: true,
                    minlength: 5
                },
                address: {
                    required: true,
                    minlength: 10
                },
                attachment: {
                    extension: "pdf|doc|docx|jpg|jpeg|png",
                    filesize: 5242880
                }
            },
            messages: {
                reason: {
                    required: "Please enter reason for leave",
                    minlength: "Reason must be at least 10 characters long"
                },
                edit_reason: {
                    required: "Please enter edit reason",
                    minlength: "Edit reason must be at least 5 characters long"
                },
                address: {
                    required: "Please enter your address during leave",
                    minlength: "Address must be at least 10 characters long"
                }
            },
            errorElement: "div",
            errorClass: "invalid-feedback",
            validClass: "is-valid",
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                error.insertAfter(element);
            }
        });

        // Action Form Validation
        $("#actionForm").validate({
            rules: {
                remark: {
                    required: true,
                    minlength: 5
                }
            },
            messages: {
                remark: {
                    required: "Please enter a remark",
                    minlength: "Remark must be at least 5 characters long"
                }
            },
            errorElement: "div",
            errorClass: "invalid-feedback",
            validClass: "is-valid",
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                error.insertAfter(element);
            }
        });
    }

    // Initialize Event Handlers
    function initializeEventHandlers() {
        $('#leaveType').on('change', handleLeaveTypeChange);
        $('#fromDate, #toDate').on('change', calculateDays);
        $('#applyLeaveModal').on('hidden.bs.modal', resetApplyForm);
        $('#edit_start_date, #edit_end_date').on('change', updateEditDayCount);

        const today = new Date().toISOString().split('T')[0];
        // $('#fromDate, #toDate, #halfDayDate').attr('min', today);

        $('#fromDate').on('change', function () {
            const fromDate = $(this).val();
            if (fromDate) {
                $('#toDate').attr('min', fromDate);
                if ($('#toDate').val() && $('#toDate').val() < fromDate) {
                    $('#toDate').val(fromDate);
                    calculateDays();
                }
            }
        });

        // Clear validation when fields change
        $('#leaveType').on('change', function () {
            $('#applyLeaveForm').validate().resetForm();
            $('#applyLeaveForm').find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        });

        // ✅ CANCEL FORM VALIDATION
        $('#cancelLeaveForm').on('submit', function (e) {
            e.preventDefault();

            const cancelType = $('#cancel_type_select').val();
            const selectedDays = $('input[name="cancel_days[]"]:checked').length;

            if (cancelType === 'partial' && selectedDays === 0) {
                alert('Please select at least one day for partial cancellation.');
                return false;
            }

            // Submit the form
            this.submit();
        });
    }

    // Handle Leave Type Change
    function handleLeaveTypeChange() {
        const leaveType = $(this).val();
        resetFormFields();

        if (leaveType === 'fullday') {
            $('#fullDayFields, #numberOfDaysField').show();
            $('#halfDayFields').hide();
            $('#fromDate, #toDate').prop('required', true);
            $('#halfDayDate, #timePeriod').prop('required', false);

            // Update validation rules dynamically
            $('#fromDate, #toDate').rules("add", {
                required: true,
                date: true
            });
            $('#halfDayDate, #timePeriod').rules("remove");

        } else if (leaveType === 'halfday') {
            $('#halfDayFields, #numberOfDaysField').show();
            $('#fullDayFields').hide();
            $('#fromDate, #toDate').prop('required', false);
            $('#halfDayDate, #timePeriod').prop('required', true);
            $('#numberOfDays').val('0.5');

            // Update validation rules dynamically
            $('#halfDayDate').rules("add", {
                required: true,
                date: true
            });
            $('#timePeriod').rules("add", {
                required: true
            });
            $('#fromDate, #toDate').rules("remove");

        } else {
            $('#fullDayFields, #halfDayFields, #numberOfDaysField').hide();
            $('#numberOfDays').val('');
            $('#fromDate, #toDate, #halfDayDate, #timePeriod').prop('required', false);
            $('#fromDate, #toDate, #halfDayDate, #timePeriod').rules("remove");
        }
    }

    // Calculate Days
    function calculateDays() {
        if ($('#leaveType').val() !== 'fullday') return;

        const fromDate = $('#fromDate').val();
        const toDate = $('#toDate').val();

        if (fromDate && toDate) {
            const startDate = new Date(fromDate);
            const endDate = new Date(toDate);

            if (endDate >= startDate) {
                // Calculate working days excluding weekends and holidays
                let workingDays = 0;
                let currentDate = new Date(startDate);

                while (currentDate <= endDate) {
                    const day = currentDate.getDay();
                    const dateString = currentDate.toISOString().split('T')[0];

                    // Only count if it's not weekend (0=Sunday, 6=Saturday) and not holiday
                    if (day !== 0 && day !== 6 && !holidayDates.includes(dateString)) {
                        workingDays++;
                    }

                    currentDate.setDate(currentDate.getDate() + 1);
                }

                $('#numberOfDays').val(workingDays);
            } else {
                $('#numberOfDays').val('');
                $('#toDate').addClass('is-invalid');
                alert('To date should be greater than or equal to from date');
            }
        }
    }


    // Handle Form Submission
    function handleFormSubmission(form) {
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Submitting...');

        const formData = new FormData(form);

        // Handle half day submission
        if ($('#leaveType').val() === 'halfday') {
            formData.set('start_date', $('#halfDayDate').val());
            formData.set('end_date', $('#halfDayDate').val());
        }

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    if (response.success) {
                        alert('Leave application submitted successfully!');
                        $('#applyLeaveModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                    }
                } catch (e) {
                    alert('Leave application submitted successfully!');
                    $('#applyLeaveModal').modal('hide');
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                alert('An error occurred while submitting the form: ' + error);
            },
            complete: function () {
                submitBtn.prop('disabled', false)
                    .html('<i class="fas fa-paper-plane me-1"></i> Submit Application');
            }
        });
    }

    // Reset Functions
    function resetFormFields() {
        $('#numberOfDays').val('');
        const validator = $('#applyLeaveForm').validate();
        validator.resetForm();
        $('#applyLeaveForm').find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('#applyLeaveForm').find('.invalid-feedback').remove();
    }

    function resetApplyForm() {
        $('#applyLeaveForm')[0].reset();
        resetFormFields();
        $('#fullDayFields, #halfDayFields, #numberOfDaysField').hide();
        $('#fromDate, #toDate, #halfDayDate, #timePeriod').prop('required', false);
    }

    // Leave Management Functions
    function viewLeave(id) {
        $.ajax({
            url: `<?= base_url('leave/get_by_id/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if (res.success) {
                    displayLeaveDetails(res.data, res.cancelled_days); // 👈 pass cancelled_days too
                } else {
                    alert("Leave details not found.");
                }
            },
            error: function () {
                alert("An error occurred while fetching leave details.");
            }
        });
    }


    function editLeave(id) {
        $.ajax({
            url: `<?= base_url('leave/get_by_id/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data && res.data.leave) {
                    try {
                        populateEditForm(res.data);
                    } catch (error) {
                        console.error('Error populating edit form:', error);
                        alert('Error loading leave data for editing.');
                    }
                } else {
                    alert('Failed to fetch leave data: ' + (res.message || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error fetching leave data: ' + error);
            }
        });
    }

    function openActionModal(leaveId) {
        $('#action_leave_id').val(leaveId);
        $('#action_remark').val('');
        $('#takeActionModal').modal('show');
    }

    function setActionType(type) {
        $('#action_type').val(type);
    }

    // ✅ UPDATED CANCEL MODAL FUNCTIONS
    function openCancelModal(leaveId) {
        $('#cancel_leave_id').val(leaveId);
        $('#partial_dates_box').hide();
        $('#cancel_type_select').val('full');
        $('#partial_dates_container').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading days...</div>');

        console.log('Loading leave data for ID:', leaveId); // Debug log

        // Load leave days for partial cancellation
        $.ajax({
            url: '<?= base_url('leave/get_by_id/') ?>' + leaveId,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                console.log('Response received:', res); // Debug log

                if (res.success && res.data && res.data.days) {
                    let html = '';

                    if (res.data.days.length > 0) {
                        res.data.days.forEach(function (day, index) {
                            console.log('Processing day:', day); // Debug log

                            const halfText = day.half_type ? ` (${day.half_type})` : '';
                            const leaveType = day.leave_type || 'N/A';
                            const dayType = day.day_type || 'full';

                            html += `
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="cancel_days[]" value="${day.id}" id="day_${day.id}">
                                <label class="form-check-label" for="day_${day.id}">
                                    <strong>${day.leave_date}</strong> - ${dayType.charAt(0).toUpperCase() + dayType.slice(1)} Day${halfText}
                                    <br><small class="text-muted">Leave Type: ${leaveType}</small>
                                </label>
                            </div>
                        `;
                        });
                    } else {
                        html = '<div class="alert alert-info">No leave days found for this request.</div>';
                    }

                    $('#partial_dates_container').html(html);
                } else {
                    console.error('Invalid response structure:', res);
                    $('#partial_dates_container').html('<div class="alert alert-danger">Failed to load leave days. Please try again.</div>');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error, xhr.responseText);
                $('#partial_dates_container').html('<div class="alert alert-danger">Error loading leave days: ' + error + '</div>');
            }
        });

        // Show/hide partial dates based on selection
        $('#cancel_type_select').off('change').on('change', function () {
            const selectedType = $(this).val();
            console.log('Cancel type changed to:', selectedType); // Debug log
            $('#partial_dates_box').toggle(selectedType === 'partial');
        });

        $('#cancelLeaveModal').modal('show');
    }


    // ✅ RO CANCEL ACTION FUNCTIONS
    function openRoCancelActionModal(cancelId, leaveInfo) {
        $('#ro_cancel_id').val(cancelId);
        $('#ro_cancel_leave_info').html(leaveInfo);
        $('#roCancelActionModal').modal('show');
    }

    function setRoCancelAction(action) {
        $('#ro_cancel_action').val(action);
    }

    // ✅ UPDATED displayLeaveDetails function in index.php
    function displayLeaveDetails(data, cancelledDays = []) {
        const leave = data.leave;
        const days = data.days; // These are only active days now

        let html = `
    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Name:</strong> ${leave.name}</li>
        <li class="list-group-item"><strong>Apply Date:</strong> ${leave.created_at}</li>
        <li class="list-group-item"><strong>Leave Period:</strong> ${leave.start_date} to ${leave.end_date}</li>
        <li class="list-group-item"><strong>Reason:</strong> ${leave.reason}</li>
        <li class="list-group-item"><strong>Address:</strong> ${leave.address}</li>
        <li class="list-group-item"><strong>Status:</strong> ${leave.status}</li>
        <li class="list-group-item"><strong>Total Days:</strong> ${leave.total_days}</li>
        <li class="list-group-item"><strong>CL Used:</strong> ${leave.cl_used}</li>
        <li class="list-group-item"><strong>Extra Used:</strong> ${leave.extra_used || 0}</li>
        <li class="list-group-item"><strong>Paid Used:</strong> ${leave.paid_used}</li>
        ${leave.attachment ? `<li class="list-group-item"><strong>Attachment:</strong> <a href="${'<?= base_url() ?>'}uploads/leave_attachments/${leave.attachment}" target="_blank">Download</a></li>` : ''}
    </ul>
    `;

        // ✅ Active Leave Days Table
        if (days && days.length > 0) {
            html += `
        <h6 class="text-success"><i class="fas fa-calendar-check me-2"></i>Active Leave Days</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-success">
                    <tr>
                        <th>Date</th>
                        <th>Leave Type</th>
                        <th>Day Type</th>
                        <th>Half Type</th>
                        <th>Source Reference</th>
                    </tr>
                </thead>
                <tbody>
        `;

            days.forEach(day => {
                html += `
            <tr class="table-light">
                <td>${day.leave_date}</td>
                <td><span class="badge bg-primary">${day.leave_type}</span></td>
                <td>${day.day_type}</td>
                <td>${day.half_type ?? '-'}</td>
                <td>${day.source_reference ?? '-'}</td>
            </tr>
        `;
            });

            html += `
                </tbody>
            </table>
        </div>
        `;
        }

        // ✅ Canceled Days Table (if any)
        if (cancelledDays && cancelledDays.length > 0) {
            html += `
        <h6 class="text-danger mt-4"><i class="fas fa-ban me-2"></i>Canceled Leave Days</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-danger">
                    <tr>
                        <th>Date</th>
                        <th>Leave Type</th>
                        <th>Day Type</th>
                        <th>Half Type</th>
                        <th>Canceled At</th>
                        <th>RO Remarks</th>
                    </tr>
                </thead>
                <tbody>
        `;

            cancelledDays.forEach(cancel => {
                const canceledDate = cancel.canceled_at ? new Date(cancel.canceled_at).toLocaleDateString() : '-';
                const halfType = cancel.half_type ? `${cancel.half_type}` : '-';

                html += `
            <tr class="table-danger">
                <td>${cancel.date}</td>
                <td><span class="badge bg-secondary">${cancel.leave_type || 'N/A'}</span></td>
                <td>${cancel.day_type}</td>
                <td>${halfType}</td>
                <td>${canceledDate}</td>
                <td>
                    <small class="text-muted">
                        ${cancel.remarks || 'No remarks provided'}
                    </small>
                </td>
            </tr>
        `;
            });

            html += `
                </tbody>
            </table>
        </div>
        `;
        }

        // ✅ Show summary if there are both active and canceled days
        if (days && days.length > 0 && cancelledDays && cancelledDays.length > 0) {
            html += `
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Summary:</strong> This leave has ${days.length} active day(s) and ${cancelledDays.length} canceled day(s).
        </div>
        `;
        }

        $('#viewLeaveModal .modal-body').html(html);
        $('#viewLeaveModal').modal('show');
    }


    function populateEditForm(data) {
        const leave = data.leave;
        const days = data.days;

        // Check if days array exists and has elements
        const firstDay = (days && days.length > 0) ? days[0] : null;

        $('#edit_leave_id').val(leave.id);
        $('#edit_reason').val(leave.reason || '');
        $('#edit_edit_reason').val('');
        $('#edit_address').val(leave.address || '');
        $('#edit_total_days').val(leave.total_days || '');

        if (leave.attachment) {
            $('#current_file_text').text('Current File: ' + leave.attachment);
        } else {
            $('#current_file_text').text('');
        }

        // Reset all fields first
        $('#edit_halfday_fields, #edit_fullday_fields, #edit_number_of_days_field').hide();

        // Check if we have day information and it's a half day
        if (firstDay && firstDay.day_type === 'half') {
            $('#edit_leave_type').val('halfday');
            $('#edit_halfday_fields').show();
            $('#edit_number_of_days_field').show();
            $('#edit_half_day_date').val(firstDay.leave_date || leave.start_date);
            $('#edit_time_period').val(firstDay.half_type || 'first_half');
        }
        // Check if total days is 0.5 (alternative way to detect half day)
        else if (parseFloat(leave.total_days) === 0.5) {
            $('#edit_leave_type').val('halfday');
            $('#edit_halfday_fields').show();
            $('#edit_number_of_days_field').show();
            $('#edit_half_day_date').val(leave.start_date);
            $('#edit_time_period').val('first_half'); // Default to first half
        }
        // Otherwise treat as full day
        else {
            $('#edit_leave_type').val('fullday');
            $('#edit_fullday_fields').show();
            $('#edit_number_of_days_field').show();
            $('#edit_start_date').val(leave.start_date || '');
            $('#edit_end_date').val(leave.end_date || '');
            updateEditDayCount();
        }

        $('#editLeaveModal').modal('show');
    }

    function updateEditDayCount() {
        const type = $('#edit_leave_type').val();

        if (type === 'fullday') {
            const startDate = $('#edit_start_date').val();
            const endDate = $('#edit_end_date').val();

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);

                if (start && end && !isNaN(start) && !isNaN(end)) {
                    // Calculate working days excluding weekends and holidays
                    let workingDays = 0;
                    let currentDate = new Date(start);

                    while (currentDate <= end) {
                        const day = currentDate.getDay();
                        const dateString = currentDate.toISOString().split('T')[0];

                        // Only count if it's not weekend (0=Sunday, 6=Saturday) and not holiday
                        if (day !== 0 && day !== 6 && !holidayDates.includes(dateString)) {
                            workingDays++;
                        }

                        currentDate.setDate(currentDate.getDate() + 1);
                    }

                    $('#edit_total_days').val(workingDays > 0 ? workingDays : 0);
                }
            }
        } else if (type === 'halfday') {
            $('#edit_total_days').val('0.5');
        }
    }


    function deleteLeave(id) {
        if (confirm("Are you sure you want to delete this leave request?")) {
            window.location.href = "<?= base_url('leave/delete/') ?>" + id;
        }
    }

    function reapplyLeave(id) {
        // You can implement reapply functionality here
        alert('Reapply functionality to be implemented');
    }

    // RO Cancel Action Functions
    function openRoCancelActionModal(cancelId) {
        $('#ro_cancel_id').val(cancelId);
        $('#ro_cancel_remark').val('');

        $.ajax({
            url: '<?= base_url('leave/get_cancellation_details/') ?>' + cancelId,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data) {
                    const data = res.data;

                    let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Employee:</strong> ${data.requester_name}<br>
                            <strong>Leave Period:</strong> ${data.start_date} to ${data.end_date}<br>
                            <strong>Total Days:</strong> ${data.total_days}
                        </div>
                        <div class="col-md-6">
                            <strong>Cancellation Type:</strong> ${data.cancellation_type}<br>
                            <strong>Requested On:</strong> ${new Date(data.requested_at).toLocaleDateString()}<br>
                            <strong>Leave Reason:</strong> ${data.leave_reason || 'N/A'}
                        </div>
                    </div>
                `;

                    if (data.remarks) {
                        html += `
                        <div class="mt-2">
                            <strong>Cancellation Remarks:</strong><br>
                            <em>"${data.remarks}"</em>
                        </div>
                    `;
                    }

                    $('#ro_cancel_leave_info').html(html);
                } else {
                    $('#ro_cancel_leave_info').html('<div class="alert alert-danger">Failed to load cancellation details.</div>');
                }
            },
            error: function () {
                $('#ro_cancel_leave_info').html('<div class="alert alert-danger">Error loading cancellation details.</div>');
            }
        });

        $('#roCancelActionModal').modal('show');
    }
</script>
