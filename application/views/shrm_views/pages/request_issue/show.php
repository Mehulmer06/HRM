<?php $this->load->view('shrm_views/includes/header'); ?>
    <style>
        .form-value {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 0;
            border: 1px solid #e9ecef;
            min-height: 40px;
            display: flex;
            align-items: center;
        }

        .comment-item {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .comment-author {
            font-weight: bold;
            color: #007bff;
        }

        .comment-date {
            color: #6c757d;
            font-size: 0.9em;
        }

        .comment-text {
            color: #333;
            line-height: 1.6;
        }


    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="page-title">Request Issue Details</h1>
                <nav class="breadcrumb-nav">
                    <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                    <a href="<?= base_url('request-issue') ?>">Request</a> /
                    <span class="text-muted">Details</span>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('request-issue') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Request Details -->
    <div class="card shadow-sm rounded p-4 mb-4">
        <h5 class="mb-4 border-bottom pb-2">Request Details</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <tbody>
                <tr>
                    <th style="width: 30%;">Title</th>
                    <td><?= htmlspecialchars($evaluation->title ?? '') ?></td>
                </tr>
                <tr>
                    <th>Submitted By</th>
                    <td><?= htmlspecialchars($evaluation->user_name ?? '') ?></td>
                </tr>
                <tr>
                    <th>Submitted To</th>
                    <td><?= ucfirst(htmlspecialchars($evaluation->submitted_to_name ?? '')) ?></td>
                </tr>
                <tr>
                    <th>Request Date</th>
                    <td><?= htmlspecialchars($evaluation->request_date ?? '') ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php if (isset($evaluation->status)): ?>
                            <?php if ($evaluation->status == 'in_progress'): ?>
                                <span class="badge bg-success">In Progress</span>
                            <?php elseif ($evaluation->status == 'close'): ?>
                                <span class="badge bg-secondary">Closed</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>
                        <div class="description-content">
                            <?php
                            // If description contains HTML, display it as HTML
                            // Otherwise, convert line breaks to <br> tags
                            $description = $evaluation->description ?? '';
                            if ($description) {
                                // Check if the description contains HTML tags
                                if (strip_tags($description) != $description) {
                                    // Contains HTML - sanitize and display
                                    echo htmlspecialchars_decode($description);
                                } else {
                                    // Plain text - convert line breaks
                                    echo nl2br(htmlspecialchars($description));
                                }
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <?php if (!empty($evaluation->document)): ?>
                    <tr>
                        <th>Document</th>
                        <td>
                            <a href="<?= base_url('uploads/request_issue/' . $evaluation->document) ?>"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>
                                Download Document
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="card shadow-sm rounded p-4">
        <h5 class="mb-4 border-bottom pb-2">
            <i class="fas fa-comments me-2"></i>
            Comments (<?= count($comments) ?>)
        </h5>

        <?php if (!empty($comments)): ?>
            <div class="comments-container">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <span class="comment-author"><?= htmlspecialchars($comment['user_name'] ?? 'Unknown User') ?></span>
                            <span class="comment-date"><?= htmlspecialchars($comment['created_at'] ?? '') ?></span>
                        </div>
                        <div class="comment-text">
                            <?php
                            $commentText = $comment['comment'] ?? '';
                            if ($commentText) {
                                // Check if the comment contains HTML tags
                                if (strip_tags($commentText) != $commentText) {
                                    // Contains HTML - sanitize and display
                                    echo htmlspecialchars_decode($commentText);
                                } else {
                                    // Plain text - convert line breaks
                                    echo nl2br(htmlspecialchars($commentText));
                                }
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <p>No comments yet.</p>
            </div>
        <?php endif; ?>
    </div>

<?php $this->load->view('shrm_views/includes/footer'); ?>