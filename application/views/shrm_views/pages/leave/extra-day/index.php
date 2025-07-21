<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php');
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title"><i class="fas fa-calendar-plus"></i>Extra Day Request</h1>
            <nav class="breadcrumb-nav">
                <a href="#">Dashboard</a> /
                <a href="#">Leave Management</a> /
                <span class="text-muted">Extra Day Request</span>
            </nav>
        </div>
        <div>
            <a href="#" class="create-btn" data-bs-toggle="modal" data-bs-target="#dayRequestModal">
                <i class="fas fa-plus"></i> New Extra Day Request
            </a>
            <a href="<?= base_url('leave') ?>" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left"></i> Back to Leave Management
            </a>
        </div>
    </div>
</div>

<!-- Day Requests Table -->
<div class="staff-tabs">
    <div class="tab-content">
        <div class="table-container">
            <div class="table-responsive">
                <table id="dayRequestTable" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 15%;">Day</th>
                        <th style="width: 35%;">Reason</th>
                        <th style="width: 12%;">Used Status</th>
                        <th style="width: 15%;">Created At</th>
                        <th style="width: 12%;">RO Status</th>
                        <th style="width: 15%;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($requests) && !empty($requests)): ?>
                        <?php foreach ($requests as $row): ?>
                            <tr>
                                <td><?= str_pad($row->id, 3, '0', STR_PAD_LEFT); ?></td>
                                <td><span class="badge bg-primary"><?= $row->work_date; ?></span></td>
                                <td>
                                    <div class="reason-text"><?= htmlentities($row->reason); ?></div>
                                </td>
                                <td>
                                    <?php if (isset($row->is_used)): ?>
                                        <?php if ($row->is_used === 'y' || $row->is_used === 'yes' || $row->is_used === '1'): ?>
                                            <span class="status-badge status-used">Used</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">Not Used</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-badge status-unknown">Unknown</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= date('Y-m-d', strtotime($row->created_at)); ?></small>
                                </td>
                                <td>
                                    <?php
                                    $status = ucfirst($row->status);
                                    $colorMap = [
                                        'pending' => ['#fff3cd', '#856404'],
                                        'approved' => ['#d4edda', '#155724'],
                                        'rejected' => ['#f8d7da', '#721c24'],
                                    ];
                                    $bg = $colorMap[$row->status][0] ?? '#f8f9fa';
                                    $text = $colorMap[$row->status][1] ?? '#495057';
                                    ?>
                                    <span class="status-badge"
                                          style="background: <?= $bg ?>; color: <?= $text ?>;"><?= $status ?></span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-cogs"></i> Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item view-btn" href="#" data-id="<?= $row->id ?>"
                                                   data-bs-toggle="modal" data-bs-target="#viewDayRequestModal">
                                                    <i class="fas fa-eye me-2"></i> View
                                                </a>
                                            </li>
                                            <?php if ($row->status !== 'approved'): ?>
                                                <li>
                                                    <a class="dropdown-item edit-btn" href="#" data-id="<?= $row->id ?>"
                                                       data-bs-toggle="modal" data-bs-target="#editDayRequestModal">
                                                        <i class="fas fa-edit me-2"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger delete-btn" href="#"
                                                       data-id="<?= $row->id ?>" data-date="<?= $row->work_date ?>"
                                                       data-reason="<?= htmlentities($row->reason) ?>"
                                                       data-bs-toggle="modal" data-bs-target="#deleteDayRequestModal">
                                                        <i class="fas fa-trash me-2"></i> Delete
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
</div>

<!-- New Day Request Modal -->
<div class="modal fade" id="dayRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="dayRequestForm" method="post" action="<?= base_url('extra-day-requests/create') ?>">
            <?php
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i> New Day Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dayRequestDate" class="form-label">Day Date *</label>
                        <input type="date" class="form-control" name="work_date" id="dayRequestDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="dayRequestReason" class="form-label">Work *</label>
                        <textarea class="form-control" name="reason" id="dayRequestReason" rows="4" required
                                  placeholder="Please provide a detailed reason for your day request..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Day Request Modal -->
<div class="modal fade" id="viewDayRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i> View Day Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Date:</dt>
                    <dd class="col-sm-8" id="viewWorkDate"></dd>

                    <dt class="col-sm-4">Reason:</dt>
                    <dd class="col-sm-8" id="viewReason"></dd>

                    <dt class="col-sm-4">Status:</dt>
                    <dd class="col-sm-8" id="viewStatus"></dd>

                    <dt class="col-sm-4">Created At:</dt>
                    <dd class="col-sm-8" id="viewCreatedAt"></dd>

                    <dt class="col-sm-4">RO Remark:</dt>
                    <dd class="col-sm-8" id="viewRoRemark"></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Edit Day Request Modal -->
<div class="modal fade" id="editDayRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editDayRequestForm" method="post">
            <?php
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Day Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDayRequestDate" class="form-label">Day Date *</label>
                        <input type="date" class="form-control" name="work_date" id="editDayRequestDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDayRequestReason" class="form-label">Work *</label>
                        <textarea class="form-control" name="reason" id="editDayRequestReason" rows="4"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Update Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Day Request Modal -->
<div class="modal fade" id="deleteDayRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i> Delete Day Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to delete this day request?</p>
                <div class="alert alert-warning">
                    <strong>Date:</strong> <span id="deleteRequestDate"></span><br>
                    <strong>Reason:</strong> <span id="deleteRequestReason"></span>
                </div>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteRequestForm" method="post">
                    <?php
                    $csrf = array(
                        'name' => $this->security->get_csrf_token_name(),
                        'hash' => $this->security->get_csrf_hash()
                    );
                    ?>
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
                    <input type="hidden" id="deleteRequestId" name="id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>

<!-- JS -->
<script>
    $(document).ready(function () {
        const allowedHolidays = <?= $holiday_dates_js ?>;
        const today = new Date().toISOString().split('T')[0];
        $('#dayRequestDate').attr('max', today);
        $('#editDayRequestDate').attr('max', today); // also for edit modal

        $('#dayRequestDate').on('change', function () {
            const selectedDate = this.value;
            const day = new Date(selectedDate).getDay(); // 0 = Sunday, 6 = Saturday

            const isWeekend = (day === 0 || day === 6);
            const isHoliday = allowedHolidays.includes(selectedDate);

            if (!isWeekend && !isHoliday) {
                $(this).val('');
            }
        });
    });

    $(document).ready(function () {
        $('#dayRequestTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            columnDefs: [{targets: -1, orderable: false}],
            language: {
                search: "Search Day Requests:",
                lengthMenu: "Show _MENU_ requests per page",
                info: "Showing _START_ to _END_ of _TOTAL_ requests"
            }
        });

        // View Modal
        $(document).on('click', '.view-btn', function () {
            const id = $(this).data('id');
            $.ajax({
                url: '<?= base_url('extra-day-requests/get/') ?>' + id,
                method: 'GET',
                success: function (res) {
                    try {
                        const data = JSON.parse(res);
                        $('#viewWorkDate').text(data.work_date || '-');
                        $('#viewReason').text(data.reason || '-');
                        $('#viewStatus').text(data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : '-');
                        $('#viewCreatedAt').text(data.created_at || '-');
                        $('#viewRoRemark').text(data.ro_remark || '-');
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        // Edit
        $(document).on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            $.ajax({
                url: '<?= base_url('extra-day-requests/get/') ?>' + id,
                method: 'GET',
                success: function (res) {
                    try {
                        const data = JSON.parse(res);
                        $('#editDayRequestDate').val(data.work_date || '');
                        $('#editDayRequestReason').val(data.reason || '');
                        $('#editDayRequestForm').attr('action', '<?= base_url('extra-day-requests/update/') ?>' + data.id);
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        // Delete
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            const date = $(this).data('date');
            const reason = $(this).data('reason');

            $('#deleteRequestId').val(id);
            $('#deleteRequestDate').text(date || '-');
            $('#deleteRequestReason').text(reason || '-');
            $('#deleteRequestForm').attr('action', '<?= base_url('extra-day-requests/delete/') ?>');
        });

        // Form submissions
        $('#deleteRequestForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    $('#deleteDayRequestModal').modal('hide');
                    location.reload(); // Refresh page to show updated data
                },
                error: function (xhr, status, error) {
                    console.error('Error deleting request:', error);
                    alert('Error deleting request. Please try again.');
                }
            });
        });

        // Validation
        $("#dayRequestForm, #editDayRequestForm").validate({
            rules: {
                work_date: {required: true},
                reason: {required: true, minlength: 10}
            },
            messages: {
                work_date: "Please select a date.",
                reason: {
                    required: "Please enter a work.",
                    minlength: "Reason must be at least 10 characters long."
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });

        // Clear forms when modals are closed
        $('.modal').on('hidden.bs.modal', function () {
            $(this).find('form')[0]?.reset();
            $(this).find('.is-invalid').removeClass('is-invalid');
        });
    });
</script>
<!-- Styles -->
<style>
    .reason-text {
        font-size: 13px;
        line-height: 1.4;
        color: #2c3e50;
    }

    .table td {
        vertical-align: top;
    }

    .modal-lg {
        max-width: 800px;
    }

    .form-label {
        font-weight: 600;
        color: #2c5aa0;
    }

    .modal-header {
        background: linear-gradient(135deg, #e67e22, #d35400);
        color: white;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }
</style>
