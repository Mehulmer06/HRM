<?php $this->load->view('shrm_views/includes/header'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title"><i class="fas fa-eye"></i> View Note</h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <a href="<?= base_url('note') ?>">Note Management</a> /
                <span class="text-muted">View Note</span>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('note') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Notes
            </a>
            <a href="<?= base_url('note/edit/' . $note->id) ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Note
            </a>
        </div>

    </div>
</div>

<!-- Note Details Card -->
<div class="form-card">
    <div class="detail-header bg-primary text-white p-4 rounded-top">
        <div class="d-flex align-items-center gap-3">
            <?php
            $initial = strtoupper(substr($note->user_name ?? 'U', 0, 1));
            $color = dechex(crc32($note->user_name ?? 'user') & 0xffffff);
            ?>
            <img src="https://placehold.co/80x80/<?= $color ?>/ffffff?text=<?= $initial ?>"
                 class="rounded-circle border border-white border-3" alt="User">
            <div>
                <h1 class="h3 mb-1"><?= htmlspecialchars($note->title) ?></h1>
                <p class="mb-1 opacity-75"><i class="fas fa-user me-2"></i>Submitted
                    by: <?= htmlspecialchars($note->user_name ?? 'Unknown') ?></p>
                <p class="mb-0 opacity-75"><i
                            class="fas fa-calendar me-2"></i>Created: <?= date('F j, Y', strtotime($note->created_at)) ?>
                </p>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-<?= $note->status === 'open' ? 'warning' : 'success' ?> fs-6 px-3 py-2">
                <i class="fas fa-folder-<?= $note->status === 'open' ? 'open' : 'check' ?> me-1"></i>
                <?= ucfirst($note->status) ?>
            </span>
        </div>
    </div>

    <div class="p-4">
        <!-- Info Summary -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="info-item p-3 bg-light rounded border-start border-primary border-4">
                    <small class="text-muted text-uppercase fw-bold">Note ID</small>
                    <div class="fw-bold">#<?= $note->id ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-item p-3 bg-light rounded border-start border-primary border-4">
                    <small class="text-muted text-uppercase fw-bold">Submitted To</small>
                    <div class="fw-bold"><?= strtoupper(str_replace('_', ' → ', $note->submitted_to)) ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-item p-3 bg-light rounded border-start border-primary border-4">
                    <small class="text-muted text-uppercase fw-bold">Last Updated</small>
                    <div class="fw-bold"><?= date('F j, Y', strtotime($note->updated_at)) ?></div>
                </div>
            </div>
            <?php if ($note->status === 'closed' && !empty($note->note_close_remarks)): ?>
                <div class="col-md-3">
                    <div class="info-item p-3 bg-light rounded border-start border-primary border-4">
                        <small class="text-muted text-uppercase fw-bold">Closed Remarks</small>
                        <div class="fw-bold"><?= htmlspecialchars($note->note_close_remarks) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Description -->
        <div class="section-card mb-4">
            <div class="bg-light p-3 border-bottom">
                <h5 class="mb-0 text-primary"><i class="fas fa-file-alt me-2"></i>Description</h5>
            </div>
            <div class="p-3 lh-lg">
                <?= nl2br(htmlspecialchars($note->description)) ?>
            </div>
        </div>

        <!-- Attachments -->
        <?php if (!empty($note->attachments)): ?>
            <div class="section-card mb-4">
                <div class="bg-light p-3 border-bottom">
                    <h5 class="mb-0 text-primary"><i class="fas fa-paperclip me-2"></i>Attachments</h5>
                </div>
                <div class="p-3 row g-3">
                    <?php foreach ($note->attachments as $doc): ?>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-file fs-1 mb-3 text-info"></i>
                                <h6 class="fw-bold"><?= htmlspecialchars($doc->title) ?></h6>
                                <p class="text-muted small mb-2"></p>
                                <small class="text-muted d-block mb-3">Uploaded: <?= date('Y-m-d', strtotime($doc->created_at)) ?></small>
                                <div class="d-grid">
                                    <a href="<?= base_url($doc->document) ?>" target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>

<style>
    .section-card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    @media (max-width: 768px) {
        .detail-header {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
    }
</style>
