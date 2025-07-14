<?php $this->load->view('includes/header'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title"><i class="fas fa-user-shield"></i> Extra Day Requests</h1>
            <nav class="breadcrumb-nav">
                <a href="#">Dashboard</a> /
                <span class="text-muted">Extra Day Requests</span>
            </nav>
        </div>
    </div>
</div>

<!-- Extra Day Request Tabs -->
<div class="staff-tabs">
    <ul class="nav nav-tabs" id="extraDayRequestTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                Pending (<?php echo count($pending); ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                Processed (<?php echo count($approved); ?>)
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
                            <th style="width: 20%;">Employee Name</th>
                            <th style="width: 15%;">Extra Day Date</th>
                            <th style="width: 35%;">Reason</th>
                            <th style="width: 15%;">Created At</th>
                            <th style="width: 15%;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pending)): ?>
                            <?php foreach ($pending as $request): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/3498db/ffffff?text=<?php echo strtoupper(substr($request->employee_name, 0, 2)); ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?php echo $request->employee_name; ?>">
                                            <div>
                                                <div class="staff-name"><?php echo $request->employee_name; ?></div>
                                                <small class="text-muted"><?php echo $request->employee_id; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo date('Y-m-d', strtotime($request->work_date)); ?></span>
                                    </td>
                                    <td>
                                        <div class="reason-text">
                                            <?php echo nl2br(htmlspecialchars($request->reason)); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('Y-m-d', strtotime($request->created_at)); ?><br>
                                            <?php echo date('h:i A', strtotime($request->created_at)); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning take-action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#actionModal"
                                                data-employee="<?php echo $request->employee_name; ?>"
                                                data-day="<?php echo date('Y-m-d', strtotime($request->work_date)); ?>"
                                                data-reason="<?php echo htmlspecialchars($request->reason); ?>"
                                                data-id="<?php echo $request->id; ?>">
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

        <!-- Approved Tab -->
        <div class="tab-pane fade" id="approved" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 20%;">Employee Name</th>
                            <th style="width: 15%;">Extra Day Date</th>
                            <th style="width: 30%;">Reason</th>
                            <th style="width: 15%;">Created At</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($approved)): ?>
                            <?php foreach ($approved as $request): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/35x35/2ecc71/ffffff?text=<?php echo strtoupper(substr($request->employee_name, 0, 2)); ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?php echo $request->employee_name; ?>">
                                            <div>
                                                <div class="staff-name"><?php echo $request->employee_name; ?></div>
                                                <small class="text-muted"><?php echo $request->employee_id; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo date('Y-m-d', strtotime($request->work_date)); ?></span>
                                    </td>
                                    <td>
                                        <div class="reason-text">
                                            <?php echo nl2br(htmlspecialchars($request->reason)); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('Y-m-d', strtotime($request->created_at)); ?><br>
                                            <?php echo date('h:i A', strtotime($request->created_at)); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($request->status == 'approved'): ?>
                                            <span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span>
                                        <?php else: ?>
                                            <span class="status-badge" style="background: #f8d7da; color: #721c24;">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-details-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewModal"
                                                data-employee="<?php echo $request->employee_name; ?>"
                                                data-day="<?php echo date('Y-m-d', strtotime($request->work_date)); ?>"
                                                data-reason="<?php echo htmlspecialchars($request->reason); ?>"
                                                data-status="<?php echo ucfirst($request->status); ?>"
                                                data-remarks="<?php echo htmlspecialchars($request->ro_remark); ?>">
                                            <i class="fas fa-eye me-1"></i> View
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

<!-- Take Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalLabel">
                    <i class="fas fa-gavel me-2"></i> Take Action on Extra Day Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="actionForm">
                <div class="modal-body">
                    <input type="hidden" id="requestId">

                    <!-- Request Details -->
                    <div class="mb-4">
                        <h6 class="text-primary">Request Details:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Employee:</strong> <span id="modalEmployee"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Extra Day Date:</strong> <span id="modalDay"></span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>Reason:</strong>
                            <div id="modalReason" class="bg-light p-2 rounded mt-1"></div>
                        </div>
                    </div>

                    <!-- Reporting Officer Remarks -->
                    <div class="mb-3">
                        <label for="roRemarks" class="form-label">Reporting Officer Remarks *</label>
                        <textarea class="form-control" id="roRemarks" rows="4" required
                                  placeholder="Enter your remarks for this decision..."></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" id="rejectBtn">
                        <i class="fas fa-times me-2"></i> Reject
                    </button>
                    <button type="button" class="btn btn-success" id="approveBtn">
                        <i class="fas fa-check me-2"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="fas fa-eye me-2"></i> Extra Day Request Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Request Details -->
                <div class="mb-4">
                    <h6 class="text-primary">Request Information:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Employee:</strong> <span id="viewEmployee"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Extra Day Date:</strong> <span id="viewDay"></span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <strong>Status:</strong> <span id="viewStatus"></span>
                    </div>
                    <div class="mt-2">
                        <strong>Reason:</strong>
                        <div id="viewReason" class="bg-light p-2 rounded mt-1"></div>
                    </div>
                    <div class="mt-2">
                        <strong>Reporting Officer Remarks:</strong>
                        <div id="viewRemarks" class="bg-light p-2 rounded mt-1"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
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
            order: [[3, 'desc']], // Order by Created At descending
            columnDefs: [
                {targets: -1, orderable: false}, // Actions column not sortable
            ],
            language: {
                search: "Search Requests:",
                lengthMenu: "Show _MENU_ requests per page",
                info: "Showing _START_ to _END_ of _TOTAL_ requests",
            }
        });

        // Handle tab switching and refresh tables
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            setTimeout(function() {
                $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
            }, 100);
        });

        // Handle Take Action button click
        $('.take-action-btn').click(function() {
            const employee = $(this).data('employee');
            const day = $(this).data('day');
            const reason = $(this).data('reason');
            const id = $(this).data('id');

            $('#requestId').val(id);
            $('#modalEmployee').text(employee);
            $('#modalDay').text(day);
            $('#modalReason').text(reason);
            $('#roRemarks').val('');
        });

        // Handle View Details button click
        $('.view-details-btn').click(function() {
            const employee = $(this).data('employee');
            const day = $(this).data('day');
            const reason = $(this).data('reason');
            const status = $(this).data('status');
            const remarks = $(this).data('remarks');

            $('#viewEmployee').text(employee);
            $('#viewDay').text(day);
            $('#viewReason').text(reason);
            $('#viewStatus').html('<span class="status-badge ' + (status === 'Approved' ? 'style="background: #d4edda; color: #155724;"' : 'style="background: #f8d7da; color: #721c24;"') + '">' + status + '</span>');
            $('#viewRemarks').text(remarks);
        });

        // Common function for both actions
        function processRequest(action) {
            const requestId = $('#requestId').val();
            const remarks = $('#roRemarks').val().trim();

            if (!remarks) {
                alert('Please enter your remarks before taking action.');
                return;
            }

            $.ajax({
                url: '<?php echo base_url('ro-extra-day-approval/action_request'); ?>',
                type: 'POST',
                data: {
                    request_id: requestId,
                    remarks: remarks,
                    action: action
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        $('#actionModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while processing the request.');
                }
            });
        }

        // Handle Approve button
        $('#approveBtn').click(function() {
            processRequest('approved');
        });

        // Handle Reject button
        $('#rejectBtn').click(function() {
            processRequest('rejected');
        });
    });
</script>

<style>
    .reason-text {
        font-size: 13px;
        line-height: 1.4;
        color: #2c3e50;
    }

    .table td {
        vertical-align: top;
    }

    .take-action-btn {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .take-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
        color: white;
    }

    .modal-header {
        background: linear-gradient(135deg, #2c5aa0, #1e3d72);
        color: white;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .form-label {
        font-weight: 600;
        color: #2c5aa0;
    }

    #approveBtn {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        border: none;
        font-weight: 600;
    }

    #rejectBtn {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        border: none;
        font-weight: 600;
    }

    #approveBtn:hover, #rejectBtn:hover {
        transform: translateY(-1px);
    }
</style>