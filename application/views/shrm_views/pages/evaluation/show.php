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
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #2c5aa0;
    }
    
    .comment-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .comment-author {
        font-weight: 600;
        color: #2c5aa0;
        margin-right: 10px;
    }
    
    .comment-date {
        font-size: 12px;
        color: #6c757d;
    }
    
    .comment-content {
        color: #2c3e50;
        line-height: 1.5;
        margin: 0;
    }
    
    .no-comments {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 8px;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">Work Progress Details</h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <a href="<?= base_url('work-progress') ?>">Work Progress</a> /
                <span class="text-muted">Details</span>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <?php
            $statusColors = [
                'pending' => ['#fff3cd', '#856404'],
                'completed' => ['#d4edda', '#155724'],
                'on_hold' => ['#ffeaa7', '#2d3436'],
                'in_progress' => ['#cce7ff', '#004085']
            ];
            $badge = $statusColors[$evaluation->status] ?? ['#eee', '#000'];
            ?>
            <span class="status-badge" style="background: <?= $badge[0] ?>; color: <?= $badge[1] ?>;">
                <?= ucfirst($evaluation->status) ?>
            </span>
            <a href="<?= base_url('work-progress') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Evaluation Details -->
<div class="form-card">
    <div class="form-section-title">Work Progress Information</div>

    <div class="row mb-4">
        <div class="col-md-12">
            <label class="form-label">Title</label>
            <p class="form-value"><?= htmlspecialchars($evaluation->title) ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <label class="form-label">Description</label>
            <div class="description-box">
                <?= $evaluation->description ?>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Users - Card Layout -->
<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-section-title mb-0">
            <i class="fas fa-users me-2"></i>
            Assigned Users
        </div>
        <span class="module-badge"><?= count($assigned_users) ?> Users</span>
    </div>

    <div class="row g-4">
        <?php foreach ($assigned_users as $user): ?>
            <?php
            $initials = strtoupper(implode('', array_map(fn($n) => $n[0], explode(' ', $user->name))));
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="module-card">
                    <div class="module-header">
                        <div class="module-info">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 50px; height: 50px; font-weight: 600;">
                                    <?= $initials ?>
                                </div>
                                <div>
                                    <h3 class="mb-1"><?= htmlspecialchars($user->name) ?></h3>
                                    <small class="text-muted">Email: <?= $user->email ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Comments Section -->
<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-section-title mb-0">
            <i class="fas fa-comments me-2"></i>
            Comments
        </div>
        <span class="module-badge"><?= count($evaluationComments) ?> Comments</span>
    </div>

    <div class="comments-container">
        <?php if (!empty($evaluationComments)): ?>
            <?php foreach ($evaluationComments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <div class="d-flex align-items-center">
                            <span class="comment-author"><?= htmlspecialchars($comment['user_name']) ?></span>
                            <span class="comment-date">
                                <?= date('M d, Y - h:i A', strtotime($comment['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <div class="comment-content">
                        <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-comments">
                <i class="fas fa-comment-slash mb-3" style="font-size: 2rem;"></i>
                <p>No comments available for this work progress item.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>