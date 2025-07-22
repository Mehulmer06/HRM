<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php');
?>
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


    <div class="stats-row mb-4">
        <!-- Header with Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Dashboard Overview</h4>
            <a href="<?= base_url('work-progress') ?>" class="btn btn-outline-secondary btn-sm" title="Go Back">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>

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
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#categories">
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
                <div class="tab-pane fade show active" id="categories">
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

<?php $this->load->view('shrm_views/includes/footer'); ?>