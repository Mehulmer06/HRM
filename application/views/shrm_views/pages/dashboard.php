<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php');
?>

<h1 class="welcome-title">Welcome to IHRMS Dashboard</h1>

<!-- Modules Grid -->
<div class="modules-grid">
    <?php $role = $this->session->userdata('role'); ?>

    <?php if ($role == 'e' || $role == 'a') : ?>
        <!-- 1. Project Man Power Resource -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-project-diagram icon"></i>
                    <h3>Project Man-Power Resource</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('project-staff') ?>" class="module-btn primary">Click Here</a>
            </div>
        </div>

        <!-- 2. Work Progress/Assessment -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-chart-line icon"></i>
                    <h3>Work Progress/Assessment</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('work-progress') ?>" class="module-btn secondary">Click Here</a>
            </div>
        </div>

        <!-- 3. Note Management -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-sticky-note icon"></i>
                    <h3>Note Management</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('note') ?>" class="module-btn info">Click Here</a>
            </div>
        </div>

        <!-- 4. Leave Management -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-user-clock icon"></i>
                    <h3>Leave Management</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('leave') ?>" class="module-btn success">Click Here</a>
            </div>
        </div>

        <!-- 5. Request/Issue/Note -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-clipboard-list icon"></i>
                    <h3>Request/Issue</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('request-issue') ?>" class="module-btn warning">Click Here</a>
            </div>
        </div>

        <!-- 6. Finance -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-indian-rupee-sign icon"></i>
                    <h3>Salary Slip</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('shrm/finance') ?>" class="module-btn success">Click Here</a>
            </div>
        </div>

        <!-- 7. Activity -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-tasks icon"></i>
                    <h3>Activity</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('activity') ?>" class="module-btn primary">Click Here</a>
            </div>
        </div>

        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-folder-open icon"></i>
                    <h3>Project</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('project') ?>" class="module-btn primary">Click Here</a>
            </div>
        </div>

    <?php elseif ($role == 'employee') : ?>
        <!-- 2. Work Progress/Assessment -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-chart-line icon"></i>
                    <h3>Work Progress/Assessment</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('work-progress') ?>" class="module-btn secondary">Click Here</a>
            </div>
        </div>

        <!-- 3. Finance -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-indian-rupee-sign icon"></i>
                    <h3>Salary Slip</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('shrm/finance') ?>" class="module-btn success">Click Here</a>
            </div>
        </div>

        <!-- 4. Leave Management -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-user-clock icon"></i>
                    <h3>Leave Management</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('leave') ?>" class="module-btn success">Click Here</a>
            </div>
        </div>

        <!-- 5. Note Management -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-sticky-note icon"></i>
                    <h3>Note Management</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('note') ?>" class="module-btn info">Click Here</a>
            </div>
        </div>

        <!-- 6. Request/Issue/Note -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-clipboard-list icon"></i>
                    <h3>Request/Issue</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('request-issue') ?>" class="module-btn warning">Click Here</a>
            </div>
        </div>

        <!-- 1. Holiday List -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-calendar-alt icon"></i>
                    <h3>Holiday List</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('holiday') ?>" class="module-btn primary">Click Here</a>
            </div>
        </div>
    <?php elseif ($role == 'viswambi') : ?>
        <!-- 1. Project Man Power Resource -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-project-diagram icon"></i>
                    <h3>Project Man-Power Resource</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('project-staff') ?>" class="module-btn primary">Click Here</a>
            </div>
        </div>

        <!-- 2. Note Management -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-sticky-note icon"></i>
                    <h3>Note Management</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('note') ?>" class="module-btn info">Click Here</a>
            </div>
        </div>

        <!-- 3. Request/Issue -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-clipboard-list icon"></i>
                    <h3>Request/Issue</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('request-issue') ?>" class="module-btn warning">Click Here</a>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('e_mail') == 'abhishek@inflibnet.ac.in') : ?>
    <!-- Page Header for Out of Office Section -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="page-title"><i class="fas fa-calendar-times"></i> Today's Out of Office</h1>
                <nav class="breadcrumb-nav">
                    <a href="#">Dashboard</a> /
                    <span class="text-muted">Out of Office (<?= date('F j, Y') ?>)</span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Today's Out of Office Tab Section -->
    <div class="staff-tabs">
        <ul class="nav nav-tabs" id="outOfOfficeTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#today-out" type="button"
                        role="tab">
                    Today's Out of Office (<?php echo count($out_of_office ?? []); ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Today's Out of Office Tab -->
            <div class="tab-pane fade show active" id="today-out" role="tabpanel">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="outOfOfficeTable">
                            <thead>
                            <tr>
                                <th>Employee Details</th>
                                <th>Total Days</th>
                                <th>Nature</th>
                                <th>Leave Dates</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($out_of_office)): ?>
                                <?php foreach ($out_of_office as $employee): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://placehold.co/35x35/3498db/ffffff?text=<?php echo strtoupper(substr($employee['name'], 0, 2)); ?>"
                                                     class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                     alt="<?php echo htmlspecialchars($employee['name']); ?>">
                                                <div>
                                                    <div class="staff-name"><?php echo htmlspecialchars($employee['name']); ?></div>
                                                    <small class="text-muted"> <?php echo htmlspecialchars($employee['designation']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="duration-badge"><?php echo $employee['total_days']; ?> day<?php echo $employee['total_days'] > 1 ? 's' : ''; ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($employee['nature']); ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($employee['dates'])): ?>
                                                <div class="leave-dates">
                                                    <?php
                                                    $dateCount = 0;
                                                    $remainingDates = array();
                                                    foreach ($employee['dates'] as $dateInfo):
                                                        if ($dateCount >= 3) {
                                                            $remainingDates[] = date('d M Y', strtotime($dateInfo['leave_date'])) . ' (' . $dateInfo['leave_details'] . ')';
                                                        } else {
                                                            ?>
                                                            <span class="badge bg-primary me-1 mb-1"
                                                                  title="<?php echo htmlspecialchars($dateInfo['leave_details']); ?>">
                                                            <?php echo date('d M Y', strtotime($dateInfo['leave_date'])); ?>
                                                        </span>
                                                            <?php
                                                        }
                                                        $dateCount++;
                                                    endforeach;

                                                    if (!empty($remainingDates)):
                                                        $tooltipContent = implode(', ', $remainingDates);
                                                        ?>
                                                        <span class="badge bg-secondary cursor-pointer"
                                                              data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              data-bs-html="true"
                                                              title="<?php echo htmlspecialchars($tooltipContent); ?>">
                                                        +<?php echo count($remainingDates); ?> more
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">No dates available</span>
                                            <?php endif; ?>
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
<?php endif ?>
<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    $(document).ready(function () {
        $('#outOfOfficeTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [
                {targets: -1, orderable: false},
            ],
            language: {
                search: "Search Employees:",
                lengthMenu: "Show _MENU_ employees per page",
                info: "Showing _START_ to _END_ of _TOTAL_ employees",
                emptyTable: "No employees are out of office today"
            }
        });

        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>
    .leave-dates {
        max-width: 200px;
    }

    .leave-dates .badge {
        font-size: 0.75rem;
        cursor: help;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .duration-badge {
        background-color: #28a745;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
    }

    .no-data {
        padding: 2rem;
    }

    .staff-name {
        font-weight: 500;
        color: #333;
    }

    .module-card {
        transition: transform 0.2s ease;
    }

    .module-card:hover {
        transform: translateY(-2px);
    }
</style>