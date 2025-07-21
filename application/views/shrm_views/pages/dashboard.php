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
                    <h3>Finance Management</h3>
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

<?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('role') == 'employee') : ?>
    <!-- Leave Tabs Section -->
    <div class="staff-tabs">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="page-title"><i class="fas fa-calendar-times"></i> Leave Dashboard</h1>
                    <nav class="breadcrumb-nav">
                        <a href="#">Dashboard</a> /
                        <span class="text-muted">Leave Management (<?= date('F j, Y') ?>)</span>
                    </nav>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs" id="leaveTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#today-out" type="button"
                        role="tab" aria-controls="today-out" aria-selected="true">
                    <i class="fas fa-user-times me-1"></i>Today's Out of Office
                    (<?php echo count($out_of_office ?? []); ?>)
                </button>
            </li>
            <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('e_mail') == 'abhishek@inflibnet.ac.in') : ?>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#upcoming-leave" type="button"
                            role="tab" aria-controls="upcoming-leave" aria-selected="false">
                        <i class="fas fa-calendar-plus me-1"></i>Upcoming Leave
                        (<?php echo count($upcoming_leave ?? []); ?>
                        )
                    </button>
                </li>
            <?php endif ?>
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
                                                    <small class="text-muted"><?php echo htmlspecialchars($employee['designation']); ?></small>
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
                                                <div class="leave-dates-container">
                                                    <?php
                                                    $totalDates = count($employee['dates']);
                                                    $showLimit = 3; // Show first 3 dates initially
                                                    ?>
                                                    <div class="leave-dates">
                                                        <?php foreach ($employee['dates'] as $index => $dateInfo): ?>
                                                            <span class="badge bg-primary me-1 mb-1 <?php echo $index >= $showLimit ? 'extra-date d-none' : ''; ?>"
                                                                  title="<?php echo htmlspecialchars($dateInfo['leave_details']); ?>">
                                                                <?php echo date('d M Y', strtotime($dateInfo['leave_date'])); ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <?php if ($totalDates > $showLimit): ?>
                                                        <div class="show-more-container mt-1">
                                                            <button class="btn btn-sm btn-outline-primary show-more-btn"
                                                                    onclick="toggleDates(this)">
                                                                <i class="fas fa-plus-circle me-1"></i>Show <?php echo($totalDates - $showLimit); ?>
                                                                more
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary show-less-btn d-none"
                                                                    onclick="toggleDates(this)">
                                                                <i class="fas fa-minus-circle me-1"></i>Show less
                                                            </button>
                                                        </div>
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

            <!-- Upcoming Leave Tab -->
            <div class="tab-pane fade" id="upcoming-leave" role="tabpanel">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="upcomingLeaveTable">
                            <thead>
                            <tr>
                                <th>Employee Details</th>
                                <th>Total Days</th>
                                <th>Nature</th>
                                <th>Start Date</th>
                                <th>Leave Dates</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($upcoming_leave)): ?>
                                <?php foreach ($upcoming_leave as $employee): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://placehold.co/35x35/28a745/ffffff?text=<?php echo strtoupper(substr($employee['name'], 0, 2)); ?>"
                                                     class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                     alt="<?php echo htmlspecialchars($employee['name']); ?>">
                                                <div>
                                                    <div class="staff-name"><?php echo htmlspecialchars($employee['name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($employee['designation']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="duration-badge upcoming"><?php echo $employee['total_days']; ?> day<?php echo $employee['total_days'] > 1 ? 's' : ''; ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($employee['nature']); ?></span>
                                        </td>
                                        <td>
                                            <span class="start-date"><?php echo date('d M Y', strtotime($employee['start_date'])); ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($employee['dates'])): ?>
                                                <div class="leave-dates-container">
                                                    <?php
                                                    $totalDates = count($employee['dates']);
                                                    $showLimit = 2; // Show first 2 dates initially for upcoming
                                                    ?>
                                                    <div class="leave-dates">
                                                        <?php foreach ($employee['dates'] as $index => $dateInfo): ?>
                                                            <span class="badge bg-success me-1 mb-1 <?php echo $index >= $showLimit ? 'extra-date d-none' : ''; ?>"
                                                                  title="<?php echo htmlspecialchars($dateInfo['leave_details']); ?>">
                                                                <?php echo date('d M Y', strtotime($dateInfo['leave_date'])); ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <?php if ($totalDates > $showLimit): ?>
                                                        <div class="show-more-container mt-1">
                                                            <button class="btn btn-sm btn-outline-success show-more-btn"
                                                                    onclick="toggleDates(this)">
                                                                <i class="fas fa-plus-circle me-1"></i>Show <?php echo($totalDates - $showLimit); ?>
                                                                more
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary show-less-btn d-none"
                                                                    onclick="toggleDates(this)">
                                                                <i class="fas fa-minus-circle me-1"></i>Show less
                                                            </button>
                                                        </div>
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
        // Initialize Today's Out of Office DataTable
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

        // Initialize Upcoming Leave DataTable
        $('#upcomingLeaveTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[3, 'asc']], // Order by Start Date column
            columnDefs: [
                {targets: -1, orderable: false},
            ],
            language: {
                search: "Search Upcoming Leaves:",
                lengthMenu: "Show _MENU_ leaves per page",
                info: "Showing _START_ to _END_ of _TOTAL_ upcoming leaves",
                emptyTable: "No upcoming leaves in the next 30 days"
            }
        });

        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Reinitialize tooltips when tab is switched
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    });

    // Function to toggle show more/less for leave dates
    function toggleDates(button) {
        const container = button.closest('.leave-dates-container');
        const extraDates = container.querySelectorAll('.extra-date');
        const showMoreBtn = container.querySelector('.show-more-btn');
        const showLessBtn = container.querySelector('.show-less-btn');

        if (button.classList.contains('show-more-btn')) {
            // Show extra dates
            extraDates.forEach(function (date) {
                date.classList.remove('d-none');
            });
            showMoreBtn.classList.add('d-none');
            showLessBtn.classList.remove('d-none');
        } else {
            // Hide extra dates
            extraDates.forEach(function (date) {
                date.classList.add('d-none');
            });
            showMoreBtn.classList.remove('d-none');
            showLessBtn.classList.add('d-none');
        }
    }
</script>
