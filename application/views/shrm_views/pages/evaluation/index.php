<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php'); ?>
<style>
    /* Compact Dashboard Styles */
    .compact-dashboard-tabs {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .compact-dashboard-tabs .nav-tabs {
        border-bottom: none;
        background: #f8f9fa;
        padding: 0;
    }

    .compact-dashboard-tabs .nav-tabs .nav-link {
        border: none;
        border-radius: 0;
        padding: 15px 25px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .compact-dashboard-tabs .nav-tabs .nav-link.active {
        background: white;
        color: #2c5aa0;
        border-bottom: 3px solid #2c5aa0;
    }

    .compact-dashboard-tabs .nav-tabs .nav-link:hover {
        color: #2c5aa0;
        background: rgba(44, 90, 160, 0.05);
    }

    .compact-tab-content {
        padding: 20px;
    }

    .compact-title {
        color: #2c5aa0;
        font-weight: 600;
        margin-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 8px;
    }

    .compact-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        min-height: 80px;
        justify-content: center;
    }

    .compact-item:hover {
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .compact-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .compact-icon {
        font-size: 14px;
    }

    .compact-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
        text-align: center;
    }

    .compact-count {
        font-size: 16px;
        font-weight: 700;
        color: #2c5aa0;
    }

    /* Combined Details Column Styling */
    .details-container {
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 12px;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .priority-badge {
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .activity-text {
        color: #495057;
        font-weight: 500;
    }

    .project-text {
        color: #6c757d;
        font-style: italic;
    }

    .detail-icon {
        font-size: 10px;
        width: 12px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .compact-dashboard-tabs .nav-tabs .nav-link {
            padding: 12px 15px;
            font-size: 13px;
        }

        .compact-tab-content {
            padding: 15px;
        }

        .compact-item {
            min-height: 70px;
            padding: 10px;
        }

        .compact-count {
            font-size: 14px;
        }

        .compact-label {
            font-size: 11px;
        }

        .details-container {
            font-size: 11px;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title"><i class="fas fa-chart-line"></i> Work Progress</h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <span class="text-muted">Work Progress</span>
            </nav>
        </div>
        <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('role') == 'e') : ?>
            <a href="<?= base_url('work-progress/create') ?>" class="create-btn">
                <i class="fas fa-plus"></i> Add New Work
            </a>
        <?php endif ?>
    </div>
</div>

<!-- Dashboard Stats Section for Employees -->
<?php if ($this->session->userdata('role') === 'employee' && isset($counts)): ?>
    <div class="stats-row mb-4">
        <!-- Summary Cards Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card"
                     style="display: flex; align-items: center; gap: 15px; border-left: 4px solid #3498db;">
                    <div class="page-title" style="margin: 0;">
                        <i class="fas fa-tasks" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?= $counts['total_count'] ?></div>
                        <div class="stat-label">Total Assignments</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card"
                     style="display: flex; align-items: center; gap: 15px; border-left: 4px solid #f39c12;">
                    <div class="page-title" style="margin: 0;">
                        <i class="fas fa-clock" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?= $counts['status_counts']['pending'] ?></div>
                        <div class="stat-label">Pending Tasks</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card"
                     style="display: flex; align-items: center; gap: 15px; border-left: 4px solid #27ae60;">
                    <div class="page-title" style="margin: 0;">
                        <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?= $counts['status_counts']['completed'] ?></div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card"
                     style="display: flex; align-items: center; gap: 15px; border-left: 4px solid #e74c3c;">
                    <div class="page-title" style="margin: 0;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?= $counts['category_counts']['urgent'] ?></div>
                        <div class="stat-label">Urgent Tasks</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Dashboard Tabs -->
        <div class="compact-dashboard-tabs">
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#categories">
                        <i class="fas fa-tags me-2"></i>Category Breakdown
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#activities">
                        <i class="fas fa-cog me-2"></i>Top Activities
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#projects">
                        <i class="fas fa-project-diagram me-2"></i>Projects Overview
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Category Breakdown Tab - Compact -->
                <div class="tab-pane fade" id="categories">
                    <div class="compact-tab-content">
                        <h6 class="compact-title">
                            <i class="fas fa-tags me-2"></i>Category Distribution
                        </h6>
                        <div class="row g-2">
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="compact-item">
                                    <span class="compact-dot" style="background: #3498db;"></span>
                                    <span class="compact-label">Routine</span>
                                    <span class="compact-count"><?= $counts['category_counts']['routine'] ?></span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="compact-item">
                                    <span class="compact-dot" style="background: #e74c3c;"></span>
                                    <span class="compact-label">Urgent</span>
                                    <span class="compact-count"><?= $counts['category_counts']['urgent'] ?></span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="compact-item">
                                    <span class="compact-dot" style="background: #9b59b6;"></span>
                                    <span class="compact-label">Add-on</span>
                                    <span class="compact-count"><?= $counts['category_counts']['addon'] ?></span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="compact-item">
                                    <span class="compact-dot" style="background: #27ae60;"></span>
                                    <span class="compact-label">Support</span>
                                    <span class="compact-count"><?= $counts['category_counts']['support'] ?></span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="compact-item">
                                    <span class="compact-dot" style="background: #95a5a6;"></span>
                                    <span class="compact-label">Other</span>
                                    <span class="compact-count"><?= $counts['category_counts']['other'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Activities Tab - Compact -->
                <div class="tab-pane fade" id="activities">
                    <div class="compact-tab-content">
                        <h6 class="compact-title">
                            <i class="fas fa-cog me-2"></i>Activity Distribution
                        </h6>
                        <?php if (!empty($counts['activity_counts'])): ?>
                            <div class="row g-2">
                                <?php foreach (array_slice($counts['activity_counts'], 0, 8) as $index => $activity): ?>
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="compact-item">
                                            <i class="fas fa-dot-circle compact-icon" style="color: #3498db;"></i>
                                            <span class="compact-label"><?= htmlspecialchars($activity['activity_name']) ?></span>
                                            <span class="compact-count"><?= $activity['count'] ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-info-circle"></i> No activities assigned
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Projects Overview Tab - Accordion -->
                <div class="tab-pane fade" id="projects">
                    <div class="compact-tab-content">
                        <h6 class="compact-title">
                            <i class="fas fa-project-diagram me-2"></i>Projects Overview
                        </h6>
                        <?php if (!empty($counts['project_details'])): ?>
                            <div class="accordion" id="projectsAccordion">
                                <?php foreach ($counts['project_details'] as $project_id => $project): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?= $project_id ?>">
                                            <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse<?= $project_id ?>"
                                                    aria-expanded="false"
                                                    aria-controls="collapse<?= $project_id ?>">
                                                <i class="fas fa-folder me-2"></i>
                                                <?= htmlspecialchars($project['project_name']) ?>
                                                <span class="badge bg-primary ms-auto"><?= $project['total_count'] ?> tasks</span>
                                            </button>
                                        </h2>
                                        <div id="collapse<?= $project_id ?>"
                                             class="accordion-collapse collapse"
                                             aria-labelledby="heading<?= $project_id ?>"
                                             data-bs-parent="#projectsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <!-- Categories -->
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold mb-3">
                                                            <i class="fas fa-tags me-2"></i>Categories
                                                        </h6>
                                                        <?php if (!empty($project['category_breakdown'])): ?>
                                                            <?php foreach ($project['category_breakdown'] as $category): ?>
                                                                <?php
                                                                $categoryColors = [
                                                                    'routine' => '#3498db',
                                                                    'urgent' => '#e74c3c',
                                                                    'addon' => '#9b59b6',
                                                                    'support' => '#27ae60',
                                                                    'other' => '#95a5a6'
                                                                ];
                                                                $color = $categoryColors[$category['category']] ?? '#95a5a6';
                                                                ?>
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <span>
                                                                        <span class="compact-dot me-2"
                                                                              style="background: <?= $color ?>; width: 8px; height: 8px; display: inline-block; border-radius: 50%;"></span>
                                                                        <?= ucfirst($category['category']) ?>
                                                                    </span>
                                                                    <span class="badge bg-light text-dark"><?= $category['count'] ?></span>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <p class="text-muted small">No categories</p>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Activities -->
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold mb-3">
                                                            <i class="fas fa-cog me-2"></i>Activities
                                                        </h6>
                                                        <?php if (!empty($project['activity_breakdown'])): ?>
                                                            <?php foreach ($project['activity_breakdown'] as $activity): ?>
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <span>
                                                                        <i class="fas fa-dot-circle me-2"
                                                                           style="color: #3498db; font-size: 8px;"></i>
                                                                        <?= htmlspecialchars($activity['activity_name'] ?: 'Unassigned') ?>
                                                                    </span>
                                                                    <span class="badge bg-light text-dark"><?= $activity['count'] ?></span>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <p class="text-muted small">No activities</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h6>No Projects Assigned</h6>
                                <p class="small">You don't have any projects assigned yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->session->userdata('role') == 'e' && $this->session->userdata('category') == 'e') : ?>
    <!-- Filter Section - Only show for role 'e' with category 'e' -->
    <div class="form-card">
        <div class="form-section-title">
            <i class="fas fa-filter"></i>
            Filter Work Progress
        </div>

        <form id="filterForm" method="GET">
            <!-- Hidden inputs for date range -->
            <input type="hidden" name="start_date" id="start_date"
                   value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
            <input type="hidden" name="end_date" id="end_date"
                   value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">

            <!-- Filter Fields Row -->
            <div class="row form-row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="form-group">
                        <label class="form-label">Date Range</label>
                        <div id="reportrange"
                             style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="filter_project">Project</label>
                        <select class="form-select" name="filter_project" id="filter_project">
                            <option value="">All Projects</option>
                            <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?= $project['id'] ?>" <?= (isset($_GET['filter_project']) && $_GET['filter_project'] == $project['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($project['project_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="filter_activity">Activity</label>
                        <select class="form-select" name="filter_activity" id="filter_activity">
                            <option value="">All Activities</option>
                            <?php if (!empty($activities)): ?>
                                <?php foreach ($activities as $activity): ?>
                                    <option value="<?= $activity['id'] ?>" <?= (isset($_GET['filter_activity']) && $_GET['filter_activity'] == $activity['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($activity['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="filter_priority">Priority</label>
                        <select class="form-select" name="filter_priority" id="filter_priority">
                            <option value="">All Priorities</option>
                            <option value="routine" <?= (isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'routine') ? 'selected' : '' ?>>
                                Routine
                            </option>
                            <option value="urgent" <?= (isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'urgent') ? 'selected' : '' ?>>
                                Urgent
                            </option>
                            <option value="addon" <?= (isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'addon') ? 'selected' : '' ?>>
                                Add-on
                            </option>
                            <option value="support" <?= (isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'support') ? 'selected' : '' ?>>
                                Support
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Row -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 justify-content-start align-items-center mt-3 pt-3"
                         style="border-top: 1px solid #eee;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fas fa-times me-1"></i> Clear All
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Evaluation Tabs -->
<div class="staff-tabs">
    <ul class="nav nav-tabs" id="evaluationTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending">Pending</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#completed">Completed</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hold">On Hold</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#all">All</button>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Pending Tab -->
        <div class="tab-pane fade show active" id="pending">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="pendingEvaluationTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Assign To</th>
                            <th>Title</th>
                            <th>Details</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $key => $eval): ?>
                            <?php if ($eval->status === 'pending'): ?>
                                <?php
                                $statusColors = [
                                    'pending' => ['#fff3cd', '#856404'],
                                    'completed' => ['#d4edda', '#155724'],
                                    'on_hold' => ['#ffeaa7', '#2d3436'],
                                    'in_progress' => ['#cce7ff', '#004085'],
                                    'overdue' => ['#f8d7da', '#721c24']
                                ];
                                $badge = $statusColors[$eval->status] ?? ['#eee', '#000'];

                                // Category colors
                                $categoryColors = [
                                    'routine' => ['#e3f2fd', '#1976d2'],
                                    'urgent' => ['#ffebee', '#d32f2f'],
                                    'addon' => ['#f3e5f5', '#7b1fa2'],
                                    'support' => ['#e8f5e8', '#388e3c']
                                ];
                                $categoryBadge = $categoryColors[$eval->category] ?? ['#f5f5f5', '#666'];
                                ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td>
                                        <div>
                                            <?php
                                            // Parse the assigned_users field to extract ID and name pairs
                                            $users = [];
                                            if (!empty($eval->assigned_users)) {
                                                $userEntries = explode('|', $eval->assigned_users);
                                                foreach ($userEntries as $entry) {
                                                    $parts = explode(':', $entry, 2); // Limit to 2 parts in case name contains ':'
                                                    if (count($parts) == 2) {
                                                        $users[] = [
                                                            'id' => trim($parts[0]),
                                                            'name' => trim($parts[1])
                                                        ];
                                                    }
                                                }
                                            }

                                            foreach ($users as $i => $user):
                                                $name = $user['name'];
                                                $userId = $user['id'];
                                                $initial = strtoupper($name[0]);
                                                $color = dechex(crc32($name) & 0xffffff);
                                                ?>
                                                <div class="d-flex align-items-center mb-1">
                                                    <img src="https://placehold.co/30x30/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                         class="rounded-circle me-2"
                                                         style="width: 30px; height: 30px;"
                                                         alt="<?= htmlspecialchars($name) ?>">
                                                    <?php if ($this->session->userdata('role') == 'e') : ?>
                                                        <span class="staff-name">
                <a class="text-decoration-none" href="<?= base_url('work-progress/report/' . base_convert($userId * 15394, 10, 36)) ?>"
                   target="_blank">
                    <?= htmlspecialchars($name) ?>
                </a><?= $i < count($users) - 1 ? ',' : '' ?>
            </span>
                                                    <?php else: ?>
                                                        <span class="staff-name"><?= htmlspecialchars($name) ?><?= $i < count($users) - 1 ? ',' : '' ?></span>
                                                    <?php endif ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($eval->title) ?></td>
                                    <td>
                                        <div class="details-container">
                                            <!-- Priority -->
                                            <div class="detail-item">
                                                <i class="fas fa-flag detail-icon"
                                                   style="color: <?= $categoryBadge[1] ?>;"></i>
                                                <span class="priority-badge"
                                                      style="background: <?= $categoryBadge[0] ?>; color: <?= $categoryBadge[1] ?>;">
                                                    <?= ucfirst($eval->category) ?>
                                                </span>
                                            </div>
                                            <!-- Activity -->
                                            <div class="detail-item">
                                                <i class="fas fa-cog detail-icon" style="color: #495057;"></i>
                                                <span class="activity-text">
                                                    <?= htmlspecialchars($eval->activity_name ?: 'No Activity') ?>
                                                </span>
                                            </div>
                                            <!-- Project -->
                                            <div class="detail-item">
                                                <i class="fas fa-project-diagram detail-icon"
                                                   style="color: #6c757d;"></i>
                                                <span class="project-text">
                                                    <?= htmlspecialchars($eval->project_name ?: 'No Project') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($eval->created_at)) ?></td>
                                    <td><span class="status-badge"
                                              style="background: <?= $badge[0] ?>; color: <?= $badge[1] ?>;"><?= ucfirst($eval->status) ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="<?= base_url('work-progress/view/' . base_convert($eval->id * 15394, 10, 36)) ?>">
                                                        <i class="fas fa-eye me-2"></i> View
                                                    </a>
                                                </li>
                                                <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('role') == 'e') : ?>
                                                    <li><a class="dropdown-item"
                                                           href="<?= base_url('work-progress/edit/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                    class="fas fa-edit me-2"></i> Edit</a></li>
                                                    <li><a href="javascript:void(0);"
                                                           class="dropdown-item btn-update-status"
                                                           data-id="<?= $eval->id ?>"
                                                           data-status="completed">
                                                            <i class="fas fa-check me-2"></i> Mark Completed</a></li>
                                                    <li><a href="javascript:void(0);"
                                                           class="dropdown-item btn-update-status"
                                                           data-id="<?= $eval->id ?>"
                                                           data-status="on_hold">
                                                            <i class="fas fa-pause me-2"></i> Put on Hold</a></li>
                                                <?php endif ?>
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('work-progress/comments/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                class="fas fa-comments me-2"></i> Comments</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Completed Tab -->
        <div class="tab-pane fade" id="completed">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="completedEvaluationTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Assign To</th>
                            <th>Title</th>
                            <th>Details</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $key => $eval): ?>
                            <?php if ($eval->status === 'completed'): ?>
                                <?php
                                $categoryColors = [
                                    'routine' => ['#e3f2fd', '#1976d2'],
                                    'urgent' => ['#ffebee', '#d32f2f'],
                                    'addon' => ['#f3e5f5', '#7b1fa2'],
                                    'support' => ['#e8f5e8', '#388e3c']
                                ];
                                $categoryBadge = $categoryColors[$eval->category] ?? ['#f5f5f5', '#666'];
                                ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td>
                                        <div>
                                            <?php
                                            // Parse the assigned_users field to extract ID and name pairs
                                            $users = [];
                                            if (!empty($eval->assigned_users)) {
                                                $userEntries = explode('|', $eval->assigned_users);
                                                foreach ($userEntries as $entry) {
                                                    $parts = explode(':', $entry, 2); // Limit to 2 parts in case name contains ':'
                                                    if (count($parts) == 2) {
                                                        $users[] = [
                                                            'id' => trim($parts[0]),
                                                            'name' => trim($parts[1])
                                                        ];
                                                    }
                                                }
                                            }

                                            foreach ($users as $i => $user):
                                                $name = $user['name'];
                                                $userId = $user['id'];
                                                $initial = strtoupper($name[0]);
                                                $color = dechex(crc32($name) & 0xffffff);
                                                ?>
                                                <div class="d-flex align-items-center mb-1">
                                                    <img src="https://placehold.co/30x30/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                         class="rounded-circle me-2"
                                                         style="width: 30px; height: 30px;"
                                                         alt="<?= htmlspecialchars($name) ?>">
                                                    <?php if ($this->session->userdata('role') == 'e') : ?>
                                                        <span class="staff-name">
                <a class="text-decoration-none" href="<?= base_url('work-progress/report/' . base_convert($userId * 15394, 10, 36)) ?>"
                   target="_blank">
                    <?= htmlspecialchars($name) ?>
                </a><?= $i < count($users) - 1 ? ',' : '' ?>
            </span>
                                                    <?php else: ?>
                                                        <span class="staff-name"><?= htmlspecialchars($name) ?><?= $i < count($users) - 1 ? ',' : '' ?></span>
                                                    <?php endif ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($eval->title) ?></td>
                                    <td>
                                        <div class="details-container">
                                            <!-- Priority -->
                                            <div class="detail-item">
                                                <i class="fas fa-flag detail-icon"
                                                   style="color: <?= $categoryBadge[1] ?>;"></i>
                                                <span class="priority-badge"
                                                      style="background: <?= $categoryBadge[0] ?>; color: <?= $categoryBadge[1] ?>;">
                                                    <?= ucfirst($eval->category) ?>
                                                </span>
                                            </div>
                                            <!-- Activity -->
                                            <div class="detail-item">
                                                <i class="fas fa-cog detail-icon" style="color: #495057;"></i>
                                                <span class="activity-text">
                                                    <?= htmlspecialchars($eval->activity_name ?: 'No Activity') ?>
                                                </span>
                                            </div>
                                            <!-- Project -->
                                            <div class="detail-item">
                                                <i class="fas fa-project-diagram detail-icon"
                                                   style="color: #6c757d;"></i>
                                                <span class="project-text">
                                                    <?= htmlspecialchars($eval->project_name ?: 'No Project') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($eval->created_at)) ?></td>
                                    <td><span class="status-badge"
                                              style="background: #d4edda; color: #155724;">Completed</span></td>
                                    <td class="text-nowrap">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('work-progress/view/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                class="fas fa-eye me-2"></i> View</a></li>
                                                <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('role') == 'e') : ?>
                                                    <li><a class="dropdown-item"
                                                           href="<?= base_url('work-progress/edit/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                    class="fas fa-edit me-2"></i> Edit</a></li>
                                                    <li><a href="javascript:void(0);"
                                                           class="dropdown-item btn-update-status"
                                                           data-id="<?= $eval->id ?>"
                                                           data-status="completed">
                                                            <i class="fas fa-check me-2"></i> Mark Completed</a></li>
                                                    <li><a href="javascript:void(0);"
                                                           class="dropdown-item btn-update-status"
                                                           data-id="<?= $eval->id ?>"
                                                           data-status="on_hold">
                                                            <i class="fas fa-pause me-2"></i> Put on Hold</a></li>
                                                    <li><a class="dropdown-item"
                                                           href="<?= base_url('work-progress/comments/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                    class="fas fa-comments me-2"></i> Comments</a></li>
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                           class="dropdown-item btn-modal-comment"
                                                           data-id="<?= $eval->id ?>">
                                                            <i class="fas fa-comment-alt me-2"></i> Modal Comment
                                                        </a>
                                                    </li>
                                                <?php endif ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- On Hold Tab -->
        <div class="tab-pane fade" id="hold">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="holdEvaluationTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Assign To</th>
                            <th>Title</th>
                            <th>Details</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $key => $eval): ?>
                            <?php if ($eval->status === 'on_hold'): ?>
                                <?php
                                $categoryColors = [
                                    'routine' => ['#e3f2fd', '#1976d2'],
                                    'urgent' => ['#ffebee', '#d32f2f'],
                                    'addon' => ['#f3e5f5', '#7b1fa2'],
                                    'support' => ['#e8f5e8', '#388e3c']
                                ];
                                $categoryBadge = $categoryColors[$eval->category] ?? ['#f5f5f5', '#666'];
                                ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td>
                                        <div>
                                            <?php
                                            // Parse the assigned_users field to extract ID and name pairs
                                            $users = [];
                                            if (!empty($eval->assigned_users)) {
                                                $userEntries = explode('|', $eval->assigned_users);
                                                foreach ($userEntries as $entry) {
                                                    $parts = explode(':', $entry, 2); // Limit to 2 parts in case name contains ':'
                                                    if (count($parts) == 2) {
                                                        $users[] = [
                                                            'id' => trim($parts[0]),
                                                            'name' => trim($parts[1])
                                                        ];
                                                    }
                                                }
                                            }

                                            foreach ($users as $i => $user):
                                                $name = $user['name'];
                                                $userId = $user['id'];
                                                $initial = strtoupper($name[0]);
                                                $color = dechex(crc32($name) & 0xffffff);
                                                ?>
                                                <div class="d-flex align-items-center mb-1">
                                                    <img src="https://placehold.co/30x30/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                         class="rounded-circle me-2"
                                                         style="width: 30px; height: 30px;"
                                                         alt="<?= htmlspecialchars($name) ?>">
                                                    <?php if ($this->session->userdata('role') == 'e') : ?>
                                                        <span class="staff-name">
                <a class="text-decoration-none" href="<?= base_url('work-progress/report/' . base_convert($userId * 15394, 10, 36)) ?>"
                   target="_blank">
                    <?= htmlspecialchars($name) ?>
                </a><?= $i < count($users) - 1 ? ',' : '' ?>
            </span>
                                                    <?php else: ?>
                                                        <span class="staff-name"><?= htmlspecialchars($name) ?><?= $i < count($users) - 1 ? ',' : '' ?></span>
                                                    <?php endif ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($eval->title) ?></td>
                                    <td>
                                        <div class="details-container">
                                            <!-- Priority -->
                                            <div class="detail-item">
                                                <i class="fas fa-flag detail-icon"
                                                   style="color: <?= $categoryBadge[1] ?>;"></i>
                                                <span class="priority-badge"
                                                      style="background: <?= $categoryBadge[0] ?>; color: <?= $categoryBadge[1] ?>;">
                                                    <?= ucfirst($eval->category) ?>
                                                </span>
                                            </div>
                                            <!-- Activity -->
                                            <div class="detail-item">
                                                <i class="fas fa-cog detail-icon" style="color: #495057;"></i>
                                                <span class="activity-text">
                                                    <?= htmlspecialchars($eval->activity_name ?: 'No Activity') ?>
                                                </span>
                                            </div>
                                            <!-- Project -->
                                            <div class="detail-item">
                                                <i class="fas fa-project-diagram detail-icon"
                                                   style="color: #6c757d;"></i>
                                                <span class="project-text">
                                                    <?= htmlspecialchars($eval->project_name ?: 'No Project') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($eval->created_at)) ?></td>
                                    <td><span class="status-badge"
                                              style="background: #ffeaa7; color: #2d3436;">On Hold</span></td>
                                    <td class="text-nowrap">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('work-progress/view/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                class="fas fa-eye me-2"></i> View</a></li>
                                                <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('role') == 'e') : ?>
                                                    <li><a class="dropdown-item"
                                                           href="<?= base_url('work-progress/edit/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                    class="fas fa-edit me-2"></i> Edit</a></li>
                                                    <li><a href="javascript:void(0);"
                                                           class="dropdown-item btn-update-status"
                                                           data-id="<?= $eval->id ?>"
                                                           data-status="pending">
                                                            <i class="fas fa-play me-2"></i> Remove from Hold</a></li>
                                                <?php endif ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- All Tab -->
        <div class="tab-pane fade" id="all">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="allEvaluationTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Assign To</th>
                            <th>Title</th>
                            <th>Details</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $key => $eval): ?>
                            <?php
                            $statusColors = [
                                'pending' => ['#fff3cd', '#856404'],
                                'completed' => ['#d4edda', '#155724'],
                                'on_hold' => ['#ffeaa7', '#2d3436'],
                                'in_progress' => ['#cce7ff', '#004085'],
                                'overdue' => ['#f8d7da', '#721c24']
                            ];
                            $badge = $statusColors[$eval->status] ?? ['#eee', '#000'];

                            $categoryColors = [
                                'routine' => ['#e3f2fd', '#1976d2'],
                                'urgent' => ['#ffebee', '#d32f2f'],
                                'addon' => ['#f3e5f5', '#7b1fa2'],
                                'support' => ['#e8f5e8', '#388e3c']
                            ];
                            $categoryBadge = $categoryColors[$eval->category] ?? ['#f5f5f5', '#666'];
                            ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td>
                                    <div>
                                        <?php
                                        // Parse the assigned_users field to extract ID and name pairs
                                        $users = [];
                                        if (!empty($eval->assigned_users)) {
                                            $userEntries = explode('|', $eval->assigned_users);
                                            foreach ($userEntries as $entry) {
                                                $parts = explode(':', $entry, 2); // Limit to 2 parts in case name contains ':'
                                                if (count($parts) == 2) {
                                                    $users[] = [
                                                        'id' => trim($parts[0]),
                                                        'name' => trim($parts[1])
                                                    ];
                                                }
                                            }
                                        }

                                        foreach ($users as $i => $user):
                                            $name = $user['name'];
                                            $userId = $user['id'];
                                            $initial = strtoupper($name[0]);
                                            $color = dechex(crc32($name) & 0xffffff);
                                            ?>
                                            <div class="d-flex align-items-center mb-1">
                                                <img src="https://placehold.co/30x30/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                     class="rounded-circle me-2"
                                                     style="width: 30px; height: 30px;"
                                                     alt="<?= htmlspecialchars($name) ?>">
                                                <?php if ($this->session->userdata('role') == 'e') : ?>
                                                    <span class="staff-name">
                <a class="text-decoration-none" href="<?= base_url('work-progress/report/' . base_convert($userId * 15394, 10, 36)) ?>"
                   target="_blank">
                    <?= htmlspecialchars($name) ?>
                </a><?= $i < count($users) - 1 ? ',' : '' ?>
            </span>
                                                <?php else: ?>
                                                    <span class="staff-name"><?= htmlspecialchars($name) ?><?= $i < count($users) - 1 ? ',' : '' ?></span>
                                                <?php endif ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($eval->title) ?></td>
                                <td>
                                    <div class="details-container">
                                        <!-- Priority -->
                                        <div class="detail-item">
                                            <i class="fas fa-flag detail-icon"
                                               style="color: <?= $categoryBadge[1] ?>;"></i>
                                            <span class="priority-badge"
                                                  style="background: <?= $categoryBadge[0] ?>; color: <?= $categoryBadge[1] ?>;">
                                                <?= ucfirst($eval->category) ?>
                                            </span>
                                        </div>
                                        <!-- Activity -->
                                        <div class="detail-item">
                                            <i class="fas fa-cog detail-icon" style="color: #495057;"></i>
                                            <span class="activity-text">
                                                <?= htmlspecialchars($eval->activity_name ?: 'No Activity') ?>
                                            </span>
                                        </div>
                                        <!-- Project -->
                                        <div class="detail-item">
                                            <i class="fas fa-project-diagram detail-icon" style="color: #6c757d;"></i>
                                            <span class="project-text">
                                                <?= htmlspecialchars($eval->project_name ?: 'No Project') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td><?= date('Y-m-d', strtotime($eval->created_at)) ?></td>
                                <td><span class="status-badge"
                                          style="background: <?= $badge[0] ?>; color: <?= $badge[1] ?>;"><?= ucfirst($eval->status) ?></span>
                                </td>
                                <td class="text-nowrap">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                            <i class="fas fa-cogs"></i> Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item"
                                                   href="<?= base_url('work-progress/view/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                            class="fas fa-eye me-2"></i> View</a></li>
                                            <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('role') == 'e') : ?>
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('work-progress/edit/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                class="fas fa-edit me-2"></i> Edit</a></li>
                                                <li><a href="javascript:void(0);"
                                                       class="dropdown-item btn-update-status"
                                                       data-id="<?= $eval->id ?>"
                                                       data-status="completed">
                                                        <i class="fas fa-check me-2"></i> Mark Completed</a></li>
                                                <?php if ($eval->status === 'on_hold'): ?>
                                                    <li><a href="javascript:void(0);"
                                                           class="dropdown-item btn-update-status"
                                                           data-id="<?= $eval->id ?>"
                                                           data-status="pending">
                                                            <i class="fas fa-play me-2"></i> Remove from Hold</a></li>
                                                <?php else: ?>
                                                    <li><a href="javascript:void(0);"
                                                           class="dropdown-item btn-update-status"
                                                           data-id="<?= $eval->id ?>"
                                                           data-status="on_hold">
                                                            <i class="fas fa-pause me-2"></i> Put on Hold</a></li>
                                                <?php endif ?>
                                            <?php endif ?>
                                            <?php if ($eval->status === 'pending'): ?>
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('work-progress/comments/' . base_convert($eval->id * 15394, 10, 36)) ?>"><i
                                                                class="fas fa-comments me-2"></i> Comments</a></li>
                                            <?php endif ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Confirm Modal -->
<div class="modal fade" id="confirmStatusModal" tabindex="-1" aria-labelledby="confirmStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmStatusLabel">Confirm Status Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to update this evaluation to <strong><span id="statusText"></span></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusBtn">Yes, Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Comment Modal (Same Flow as comment.php) -->
<div class="modal fade" id="modalComment" tabindex="-1" aria-labelledby="modalCommentLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Evaluation Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Comment Form -->
                <form id="commentForm">
                    <?php
                    $csrf = array(
                        'name' => $this->security->get_csrf_token_name(),
                        'hash' => $this->security->get_csrf_hash()
                    );
                    ?>
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
                    <input type="hidden" name="evaluation_id" id="commentEvalId">

                    <div class="form-group mb-3">
                        <label for="commentText" class="form-label">Your Comment *</label>
                        <textarea name="comment" id="commentText" class="form-control" rows="5"
                                  placeholder="Enter your comment here." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> Submit Comment
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="clearCommentForm()">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </form>

                <hr>

                <!-- Comments -->
                <div class="form-section-title mt-4">
                    <i class="fas fa-comments"></i>
                    All Comments (<span id="modalCommentCount">0</span>)
                </div>
                <div id="modalCommentsContainer" style="max-height: 400px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>
<script type="text/javascript">
    // Date Range Picker Script
    $(function () {
        var start = moment().subtract(29, 'days');
        var end = moment();

        // Check if dates are already set from URL parameters
        if ($('#start_date').val() && $('#end_date').val()) {
            start = moment($('#start_date').val());
            end = moment($('#end_date').val());
        }

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            // Update hidden date inputs
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
    });

    // Clear Filters Function
    function clearFilters() {
        // Reset all filter fields
        document.getElementById('filter_project').value = '';
        document.getElementById('filter_activity').value = '';
        document.getElementById('filter_priority').value = '';

        // Reset date range picker to default
        $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(29, 'days'));
        $('#reportrange').data('daterangepicker').setEndDate(moment());

        // Clear hidden date inputs
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';

        // Redirect to clear URL parameters
        window.location.href = window.location.pathname;
    }
</script>
<script>
    $(document).ready(function () {
        $('.table').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                targets: 0,
                width: "5%"
            }, // ID
                {
                    targets: 1,
                    width: "20%"
                }, // Assign To
                {
                    targets: 2,
                    width: "25%"
                }, // Title
                {
                    targets: 3,
                    width: "20%"
                }, // Category
                {
                    targets: 4,
                    width: "12%"
                }, // Added Date
                {
                    targets: 5,
                    width: "10%"
                }, // Status
                {
                    targets: 6,
                    width: "18%",
                    orderable: false
                } // Actions
            ]
        });
    });

    let selectedEvalId = null;
    let selectedStatus = null;

    $(document).on('click', '.btn-update-status', function (e) {
        e.preventDefault();
        selectedEvalId = $(this).data('id');
        selectedStatus = $(this).data('status');

        $('#statusText').text(selectedStatus.replace('_', ' ').toUpperCase());
        const modal = new bootstrap.Modal(document.getElementById('confirmStatusModal'));
        modal.show();
    });

    $('#confirmStatusBtn').on('click', function () {
        $.ajax({
            url: '<?= base_url("work-progress/update_status") ?>',
            type: 'POST',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                id: selectedEvalId,
                status: selectedStatus
            },
            dataType: 'json',
            success: function (response) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmStatusModal'));
                modal.hide();
                if (response.success) {
                    alert('Status updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update status.');
                }
            },
            error: function () {
                alert('Error while updating status.');
            }
        });
    });

    function clearCommentForm() {
        $('#commentText').val('');
    }

    function loadModalComments(evaluationId) {
        $.ajax({
            url: '<?= base_url("work-progress/get_comments") ?>',
            type: 'GET',
            data: {
                evaluation_id: evaluationId
            },
            dataType: 'json',
            success: function (data) {
                let html = '';
                const count = data.comments.length;

                if (count === 0) {
                    html = '<div class="text-center py-5"><i class="fas fa-comment-slash fa-3x text-muted mb-3"></i><p class="text-muted">No comments yet.</p></div>';
                } else {
                    data.comments.forEach(function (comment) {
                        const initial = comment.user_name.charAt(0).toUpperCase();
                        const color = Math.abs(comment.user_name.split('').reduce((a, b) => ((a << 5) - a) + b.charCodeAt(0), 0)).toString(16).slice(0, 6);
                        const date = new Date(comment.created_at).toLocaleString();
                        html += `
                        <div class="comment-item mb-3 p-3 border rounded">
                            <div class="d-flex gap-3">
                                <div>
                                    <div style="width: 40px; height: 40px; background: #${color}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        ${initial}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>${comment.user_name}</strong>
                                    <small class="text-muted ms-2">${date}</small>
                                    <div class="mt-2">${comment.comment.replace(/\n/g, '<br>')}</div>
                                </div>
                            </div>
                        </div>`;
                    });
                }

                $('#modalCommentsContainer').html(html);
                $('#modalCommentCount').text(count);
            }
        });
    }

    $(document).on('click', '.btn-modal-comment', function () {
        const evalId = $(this).data('id');
        $('#commentEvalId').val(evalId);
        clearCommentForm();
        $('#modalCommentsContainer').html('<p class="text-muted">Loading comments...</p>');
        const modal = new bootstrap.Modal(document.getElementById('modalComment'));
        modal.show();
        loadModalComments(evalId);
    });

    $('#commentForm').on('submit', function (e) {
        e.preventDefault();
        const comment = $('#commentText').val().trim();
        if (comment.length < 3) {
            alert('Comment must be at least 3 characters long.');
            return;
        }

        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Posting...');

        $.ajax({
            url: '<?= base_url("work-progress/add_comment") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    btn.html('<i class="fas fa-check"></i> Posted!').removeClass('btn-primary').addClass('btn-success');
                    clearCommentForm();
                    loadModalComments($('#commentEvalId').val());
                    setTimeout(() => {
                        btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Comment').removeClass('btn-success').addClass('btn-primary');
                    }, 2000);
                } else {
                    alert(data.message || 'Failed to add comment.');
                    btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Comment');
                }
            },
            error: function () {
                alert('Network error. Please try again.');
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Comment');
            }
        });
    });
</script>