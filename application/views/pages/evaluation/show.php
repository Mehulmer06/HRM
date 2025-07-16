<?php $this->load->view('includes/header'); ?>
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

<?php $this->load->view('includes/footer'); ?>
