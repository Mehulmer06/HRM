<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php');
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
            <?php if ($this->session->userdata('role') == 'employee') : ?>
                <a href="#" class="create-btn" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                    <i class="fas fa-plus"></i> Apply Leave
                </a>

                <a href="<?= base_url('extra-day-requests') ?>" class="create-btn btn btn-warning ms-2">
                    <i class="fas fa-calendar-plus"></i> Extra Day Request
                </a>
            <?php endif ?>
            <?php if ($this->session->userdata('role') == 'admin' || $this->session->userdata('role') == 'e' || ($this->session->userdata('role') == 'employee' && $this->session->userdata('category') == 'admin')) : ?>
                <a href="<?= base_url('casual-leave') ?>" class="create-btn btn btn-success ms-2">
                    <i class="fas fa-plus-circle"></i> CL Add Module
                </a>
            <?php endif ?>
            <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('role') == 'e') : ?>
                <a href="<?= base_url('ro-extra-day-approval') ?>" class="create-btn btn btn-info ms-2">
                    <i class="fas fa-user-shield"></i> RO Extra Day Approval
                </a>
            <?php endif ?>
        </div>
    </div>
</div>

<?php if ($this->session->userdata('role') == 'employee') : ?>
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
<?php endif ?>

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
        <!--        <li class="nav-item">-->
        <!--            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">-->
        <!--                Cancelled (--><?php //= !empty($cancelled_leaves) ? count($cancelled_leaves) : 0 ?><!--)-->
        <!--            </button>-->
        <!--        </li>-->
        <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('role') == 'e') : ?>
            <li class="nav-item">
                <button class="nav-link text-warning" data-bs-toggle="tab" data-bs-target="#pending_cancellations"
                        type="button" role="tab">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Pending Cancellations (<?= !empty($pending_cancellations) ? count($pending_cancellations) : 0 ?>)
                </button>
            </li>
        <?php endif ?>
    </ul>

    <div class="tab-content">
        <!-- Pending Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="pendingTable">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 20%;">Name/Apply Date</th>
                            <th style="width: 15%;">Leave Period</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 8%;">CL Used</th>
                            <th style="width: 8%;">Extra Used</th>
                            <th style="width: 8%;">Paid Used</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pending_leaves)): ?>
                            <?php foreach ($pending_leaves as $leave): ?>
                                <tr>
                                    <td><?= $leave->id ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/3498db/ffffff?text=<?= substr($leave->user_name, 0, 1) ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $leave->user_name ?>">
                                            <div>
                                                <div class="staff-name"><?= $leave->user_name ?></div>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($leave->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong><?= date('M d, Y', strtotime($leave->start_date)) ?></strong>
                                            <?php if ($leave->start_date != $leave->end_date): ?>
                                                <br><small class="text-muted">to</small><br>
                                                <strong><?= date('M d, Y', strtotime($leave->end_date)) ?></strong>
                                            <?php endif; ?>
                                        </div>
                                    </td>
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
                                                <?php if (($leave->user_id == $this->session->userdata('user_id')) && $this->session->userdata('role') == 'employee'): ?>
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
                                                <?php if ($this->session->userdata('role') == 'e') : ?>
                                                    <li>
                                                        <a class="dropdown-item text-success" href="#"
                                                           onclick="openActionModal(<?= $leave->id ?>)">
                                                            <i class="fas fa-check-circle me-2"></i> Take Action
                                                        </a>
                                                    </li>
                                                <?php endif ?>
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
                            <th style="width: 20%;">Name/Apply Date</th>
                            <th style="width: 15%;">Leave Period</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 8%;">CL Used</th>
                            <th style="width: 8%;">Extra Used</th>
                            <th style="width: 8%;">Paid Used</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($approved_leaves)): ?>
                            <?php foreach ($approved_leaves as $leave): ?>
                                <tr>
                                    <td><?= $leave->id ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/2ecc71/ffffff?text=<?= substr($leave->user_name, 0, 1) ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $leave->user_name ?>">
                                            <div>
                                                <div class="staff-name"><?= $leave->user_name ?></div>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($leave->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong><?= date('M d, Y', strtotime($leave->start_date)) ?></strong>
                                            <?php if ($leave->start_date != $leave->end_date): ?>
                                                <br><small class="text-muted">to</small><br>
                                                <strong><?= date('M d, Y', strtotime($leave->end_date)) ?></strong>
                                            <?php endif; ?>
                                        </div>
                                    </td>
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
                                                <?php if (($leave->user_id == $this->session->userdata('user_id')) && $this->session->userdata('role') === 'employee'): ?>
                                                    <?php

                                                    // Check if this leave has any rejected cancellation requests
                                                    $has_rejected_cancellation = false;
                                                    if (isset($leave->cancellation_status) && $leave->cancellation_status == 'rejected') {
                                                        $has_rejected_cancellation = true;
                                                    }
                                                    $today = date('Y-m-d');
                                                    $start_date = $leave->start_date;
                                                    $end_date = $leave->end_date;

                                                    $can_request_cancel = ($today <= $end_date);

                                                    ?>
                                                    <?php if (!$has_rejected_cancellation && $can_request_cancel): ?>
                                                        <li>
                                                            <a class="dropdown-item text-warning" href="#"
                                                               onclick="openCancelModal(<?= $leave->id ?>)">
                                                                <i class="fas fa-minus-circle me-2"></i> Request
                                                                Cancellation
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
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
                            <th style="width: 20%;">Name/Apply Date</th>
                            <th style="width: 15%;">Leave Period</th>
                            <th style="width: 8%;">Total Days</th>
                            <th style="width: 8%;">CL Used</th>
                            <th style="width: 8%;">Extra Used</th>
                            <th style="width: 8%;">Paid Used</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($rejected_leaves)): ?>
                            <?php foreach ($rejected_leaves as $leave): ?>
                                <tr class="table-danger">
                                    <td><?= $leave->id ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/e67e22/ffffff?text=<?= substr($leave->user_name, 0, 1) ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $leave->user_name ?>">
                                            <div>
                                                <div class="staff-name"><?= $leave->user_name ?></div>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($leave->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong><?= date('M d, Y', strtotime($leave->start_date)) ?></strong>
                                            <?php if ($leave->start_date != $leave->end_date): ?>
                                                <br><small class="text-muted">to</small><br>
                                                <strong><?= date('M d, Y', strtotime($leave->end_date)) ?></strong>
                                            <?php endif; ?>
                                        </div>
                                    </td>
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
                            <th style="width: 13%;">Leave Period</th>
                            <th style="width: 7%;">Total Days</th>
                            <th style="width: 7%;">CL Used</th>
                            <th style="width: 7%;">Extra Used</th>
                            <th style="width: 7%;">Paid Used</th>
                            <th style="width: 10%;">Cancellation Type</th>
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
                                            <img src="https://placehold.co/35x35/f39c12/ffffff?text=<?= substr($leave->user_name, 0, 2) ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $leave->user_name ?>">
                                            <div>
                                                <div class="staff-name"><?= $leave->user_name ?></div>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($leave->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong><?= date('M d, Y', strtotime($leave->start_date)) ?></strong>
                                            <?php if ($leave->start_date != $leave->end_date): ?>
                                                <br><small class="text-muted">to</small><br>
                                                <strong><?= date('M d, Y', strtotime($leave->end_date)) ?></strong>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary"><?= $leave->total_days ?></span></td>
                                    <td><span class="badge bg-info"><?= $leave->cl_used ?></span></td>
                                    <td>
                                        <span class="badge bg-purple"><?= isset($leave->extra_used) ? $leave->extra_used : '0' ?></span>
                                    </td>
                                    <td><span class="badge bg-danger"><?= $leave->paid_used ?></span></td>
                                    <td>
                                <span class="badge <?= isset($leave->leave_request_day_id) && $leave->leave_request_day_id ? 'bg-info' : 'bg-warning' ?>">
                                    <?= isset($leave->cancellation_type) ? ucfirst($leave->cancellation_type) : 'Full' ?>
                                </span>
                                    </td>
                                    <td>
                                        <span class="status-badge bg-warning"><?= $leave->cancellation_status ?></span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
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

        <!-- NEW PENDING CANCELLATIONS TAB FOR RO -->
        <div class="tab-pane fade" id="pending_cancellations" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="pendingCancellationsTable">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Employee</th>
                            <th style="width: 12%;">Cancellation Type</th>
                            <th style="width: 12%;">Original Leave</th>
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
                                            <img src="https://placehold.co/30x30/f39c12/ffffff?text=<?= substr($cancellation->requester_name, 0, 1) ?>"
                                                 class="rounded-circle me-2" style="width: 30px; height: 30px;"
                                                 alt="<?= $cancellation->requester_name ?>">
                                            <div>
                                                <div class="fw-bold"
                                                     style="font-size: 0.9rem;"><?= $cancellation->requester_name ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                <span class="badge <?= strpos($cancellation->cancellation_type, 'Full') !== false ? 'bg-warning' : 'bg-info' ?>">
                                    <?= $cancellation->cancellation_type ?>
                                </span>
                                        <?php if (isset($cancellation->cancellation_count) && $cancellation->cancellation_count > 1): ?>
                                            <br><small class="text-muted"><?= $cancellation->cancellation_count ?>
                                                day(s)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong><?= date('M d', strtotime($cancellation->start_date)) ?></strong>
                                            <?php if ($cancellation->start_date != $cancellation->end_date): ?>
                                                <br><small class="text-muted">to</small><br>
                                                <strong><?= date('M d, Y', strtotime($cancellation->end_date)) ?></strong>
                                            <?php else: ?>
                                                <br><small
                                                        class="text-muted"><?= date('Y', strtotime($cancellation->start_date)) ?></small>
                                            <?php endif; ?>
                                        </div>
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
                                        <?php if (isset($cancellation->cancelled_dates) && !empty($cancellation->cancelled_dates)): ?>
                                            <br><small class="text-primary">
                                                <i class="fas fa-calendar-times me-1"></i>
                                                <?php
                                                $dates = explode(',', $cancellation->cancelled_dates);
                                                if (count($dates) > 2) {
                                                    echo date('M d', strtotime(trim($dates[0]))) . ', ' . date('M d', strtotime(trim($dates[1]))) . ' +' . (count($dates) - 2) . ' more';
                                                } else {
                                                    echo implode(', ', array_map(function ($date) {
                                                        return date('M d', strtotime(trim($date)));
                                                    }, $dates));
                                                }
                                                ?>
                                            </small>
                                        <?php endif; ?>
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
                <?php
                $csrf = array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                );
                ?>
                <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
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
                <?php
                $csrf = array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                );
                ?>
                <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
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
            <?php
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
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
            <?php
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
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
    <div class="modal-dialog modal-lg">
        <form method="post" action="<?= base_url('leave/cancel_action') ?>">
            <?php
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
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

<?php $this->load->view('shrm_views/includes/footer'); ?>

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

    // ✅ UPDATED displayLeaveDetails function
    function displayLeaveDetails(data, cancelledDays = []) {
        const leave = data.leave;
        const days = data.days;

        let html = `
    <!-- Leave Header Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user-circle me-2"></i>
                    ${leave.name}
                </h5>
                <span class="badge ${leave.status === 'approved' ? 'bg-success' : leave.status === 'rejected' ? 'bg-danger' : leave.status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary'} fs-6">
                    ${leave.status.charAt(0).toUpperCase() + leave.status.slice(1)}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Basic Info Column -->
                <div class="col-md-6">
                    <div class="mb-3 pb-2 border-bottom">
                        <small class="text-muted d-block">Apply Date</small>
                        <div class="fw-bold">${leave.created_at}</div>
                    </div>
                    <div class="mb-3 pb-2 border-bottom">
                        <small class="text-muted d-block">Leave Period</small>
                        <div class="fw-bold">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>
                            ${leave.start_date}
                            ${leave.start_date !== leave.end_date ? 'to ' + leave.end_date : ''}
                        </div>
                    </div>
                    <div class="mb-3 pb-2 border-bottom">
                        <small class="text-muted d-block">Reason</small>
                        <div class="text-break">${leave.reason || 'N/A'}</div>
                    </div>
                    <div class="mb-3 pb-2 border-bottom">
                        <small class="text-muted d-block">Address During Leave</small>
                        <div class="text-break">${leave.address || 'N/A'}</div>
                    </div>
                    ${leave.attachment ? `
                    <div class="mb-3">
                        <small class="text-muted d-block">Attachment</small>
                        <div class="mt-1">
                            <a href="<?= base_url() ?>uploads/leave_attachments/${leave.attachment}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download File
                            </a>
                        </div>
                    </div>
                    ` : ''}
                </div>

                <!-- Statistics Column -->
                <div class="col-md-6">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded border">
                                <div class="fs-3 fw-bold text-primary">${leave.total_days}</div>
                                <small class="text-muted text-uppercase">Total Days</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded border">
                                <div class="fs-3 fw-bold text-info">${leave.cl_used}</div>
                                <small class="text-muted text-uppercase">CL Used</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded border">
                                <div class="fs-3 fw-bold text-warning">${leave.extra_used || 0}</div>
                                <small class="text-muted text-uppercase">Extra Used</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded border">
                                <div class="fs-3 fw-bold text-danger">${leave.paid_used}</div>
                                <small class="text-muted text-uppercase">Paid Used</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-3" id="leaveDaysTabs" role="tablist">
        ${days && days.length > 0 ? `
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="active-days-tab" data-bs-toggle="tab"
                    data-bs-target="#active-days" type="button" role="tab">
                <i class="fas fa-calendar-check me-2"></i>
                Active Days <span class="badge bg-success ms-1">${days.length}</span>
            </button>
        </li>
        ` : ''}
        ${cancelledDays && cancelledDays.length > 0 ? `
        <li class="nav-item" role="presentation">
            <button class="nav-link ${days && days.length > 0 ? '' : 'active'}"
                    id="cancelled-days-tab" data-bs-toggle="tab"
                    data-bs-target="#cancelled-days" type="button" role="tab">
                <i class="fas fa-ban me-2"></i>
                Cancelled Days <span class="badge bg-danger ms-1">${cancelledDays.length}</span>
            </button>
        </li>
        ` : ''}
    </ul>

    <div class="tab-content" id="leaveDaysTabContent">
    `;

        // ✅ Active Leave Days Tab
        if (days && days.length > 0) {
            html += `
        <div class="tab-pane fade show active" id="active-days" role="tabpanel">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        Active Leave Days (${days.length})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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

            days.forEach((day, index) => {
                html += `
                <tr>
                    <td class="fw-bold">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        ${day.leave_date}
                    </td>
                    <td>
                        <span class="badge ${day.leave_type === 'CL' ? 'bg-info' : day.leave_type === 'Extra' ? 'bg-warning' : 'bg-danger'}">
                            ${day.leave_type}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${day.day_type === 'full' ? 'bg-primary' : 'bg-info'}">
                            ${day.day_type === 'full' ? 'Full Day' : 'Half Day'}
                        </span>
                    </td>
                    <td>
                        ${day.half_type ? `
                            <span class="badge bg-secondary">
                                ${day.half_type === 'first_half' ? 'Morning' : 'Afternoon'}
                            </span>
                        ` : '<span class="text-muted">-</span>'}
                    </td>
                    <td class="text-muted">${day.source_reference || '-'}</td>
                </tr>
            `;
            });

            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        `;
        }

        // ✅ Cancelled Days Tab
        if (cancelledDays && cancelledDays.length > 0) {
            html += `
        <div class="tab-pane fade ${days && days.length > 0 ? '' : 'show active'}"
             id="cancelled-days" role="tabpanel">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-ban me-2"></i>
                        Cancelled Leave Days (${cancelledDays.length})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-danger">
                                <tr>
                                    <th>Date</th>
                                    <th>Leave Type</th>
                                    <th>Day Type</th>
                                    <th>Half Type</th>
                                    <th>Cancelled At</th>
                                    <th>RO Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

            cancelledDays.forEach(cancel => {
                const canceledDate = cancel.canceled_at ? new Date(cancel.canceled_at).toLocaleDateString() : '-';
                const halfType = cancel.half_type ? cancel.half_type : '-';

                html += `
                <tr>
                    <td class="fw-bold">
                        <i class="fas fa-calendar-times text-danger me-2"></i>
                        ${cancel.date}
                    </td>
                    <td>
                        <span class="badge bg-secondary">${cancel.leave_type || 'N/A'}</span>
                    </td>
                    <td>
                        <span class="badge ${cancel.day_type === 'full' ? 'bg-primary' : 'bg-info'}">
                            ${cancel.day_type === 'full' ? 'Full Day' : 'Half Day'}
                        </span>
                    </td>
                    <td>
                        ${halfType !== '-' ? `
                            <span class="badge bg-secondary">
                                ${halfType === 'first_half' ? 'Morning' : 'Afternoon'}
                            </span>
                        ` : '<span class="text-muted">-</span>'}
                    </td>
                    <td class="text-muted">${canceledDate}</td>
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
                </div>
            </div>
        </div>
        `;
        }

        html += `
    </div>
    `;

        // ✅ Summary Alert
        if (days && days.length > 0 && cancelledDays && cancelledDays.length > 0) {
            html += `
        <div class="alert alert-info mt-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fs-4 me-3"></i>
                <div>
                    <h6 class="mb-1">Leave Summary</h6>
                    <p class="mb-0">
                        This leave request has <strong>${days.length}</strong> active day(s)
                        and <strong>${cancelledDays.length}</strong> cancelled day(s).
                    </p>
                </div>
            </div>
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
    // ✅ UPDATED RO Cancel Action Functions
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

                    // Show cancelled dates if it's a partial cancellation
                    if (data.all_cancelled_dates && data.cancellation_type.includes('Partial')) {
                        html += `
                        <div class="mt-3">
                            <strong>Days to be Cancelled:</strong><br>
                            <div class="alert alert-warning">
                                ${data.all_cancelled_dates.split(',').map(date =>
                            `<span class="badge bg-warning text-dark me-1">${date.trim()}</span>`
                        ).join('')}
                            </div>
                        </div>
                    `;
                    }

                    if (data.remarks) {
                        html += `
                        <div class="mt-2">
                            <strong>Cancellation Remarks:</strong><br>
                            <em>"${data.remarks}"</em>
                        </div>
                    `;
                    }

                    // Show count if multiple days
                    if (data.total_cancellations > 1) {
                        html += `
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                This action will affect ${data.total_cancellations} day(s) cancellation request(s).
                            </small>
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
