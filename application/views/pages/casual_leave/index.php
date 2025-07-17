<?php $this->load->view('includes/header');
include('./application/views/pages/message.php');
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-plus-circle"></i>
                CL Add Module
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <a href="<?= base_url('leave') ?>">Leave Management</a> /
                <span class="text-muted">CL Add Module</span>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('leave') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Leave Management
            </a>
        </div>
    </div>
</div>

<!-- CL Grant Form Section -->
<div class="form-card">
    <div class="form-section-title">
        <i class="fas fa-calendar-plus"></i>
        Grant Casual Leave (CL)
    </div>

    <form id="clGrantForm" method="POST" action="<?= base_url('casual-leave/save') ?>">
        <input type="hidden" id="edit_id" name="id" value="">

        <div class="row form-row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="user_id">Select Employee</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">Select Employee</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user->id ?>"><?= $user->name ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="cl_month">CL Month</label>
                    <input type="month" class="form-control" id="cl_month" name="cl_month"
                           value="<?= date('Y-m') ?>" required>
                    <small class="form-text text-muted">Select the month for which CL is being granted</small>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group" style="margin-top: 32px;">
                    <button type="submit" class="btn-primary me-2">
                        <i class="fas fa-check me-1"></i>
                        Grant CL
                    </button>
                    <button type="reset" class="btn-secondary">
                        <i class="fas fa-undo me-1"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- CL Grants Table Section -->
<div class="out-of-office-section">
    <h2 class="section-title">
        <i class="fas fa-list-alt"></i>
        CL Grants History
    </h2>

    <div class="table-responsive">
        <table class="table-modern table" id="clGrantsTable">
            <thead>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>CL Month</th>
                <th>Used Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($grants)): ?>
                <?php foreach ($grants as $grant): ?>
                    <tr>
                        <td><?= $grant->id ?></td>
                        <td><?= $grant->name ?></td>
                        <td><?= date('F Y', strtotime($grant->cl_month)) ?></td>
                        <td>
                            <?php if ($grant->is_used == 'y'): ?>
                                <span class="status-badge status-used">Used</span>
                            <?php else: ?>
                                <span class="status-badge status-inactive">Not Used</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y h:i A', strtotime($grant->granted_at)) ?></td>
                        <td>
                            <button class="action-btn btn-edit" title="Edit"
                                    onclick="editGrant(<?= $grant->id ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!--                            <a href="-->
                            <?php //= base_url('casual-leave/delete/' . $grant->id) ?><!--"-->
                            <!--                               class="action-btn btn-toggle" title="Delete"-->
                            <!--                               onclick="return confirm('Are you sure you want to delete this grant?')">-->
                            <!--                                <i class="fas fa-trash"></i>-->
                            <!--                            </a>-->
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>
    $(document).ready(function () {
        $('#user_id').select2();

        $('#clGrantsTable').DataTable({
            responsive: true,
            processing: true,
            pageLength: 10,
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                },
                emptyTable: "No CL grants found"
            },
        });
    });

    function editGrant(id) {
        $.ajax({
            url: '<?= base_url('casual-leave/get/') ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const data = response.data;

                    $('#edit_id').val(data.id);
                    $('#user_id').val(data.user_id);
                    $('#cl_month').val(data.cl_month.substring(0, 7));
                } else {
                    alert('Error loading grant data');
                }
            },
            error: function () {
                alert('Error loading grant data');
            }
        });
    }
</script>