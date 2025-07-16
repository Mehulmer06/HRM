<?php $this->load->view('includes/header'); ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title"><i class="fas fa-comments"></i> Work Progress Comments</h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <a href="<?= base_url('work-progress') ?>">Work Progress</a> /
                <span class="text-muted">Comments</span>
            </nav>
        </div>
        <a href="<?= base_url('work-progress') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Evaluation Information -->
<div class="form-card">
    <div class="form-section-title">
        <i class="fas fa-info-circle"></i>
        Work Progress Information
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="info-item">
                <div class="info-label">Title</div>
                <div class="info-value"><?= htmlspecialchars($evaluation->title) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-item">
                <div class="info-label">Created Date</div>
                <div class="info-value"><?= date('M j, Y', strtotime($evaluation->created_at)) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-item">
                <div class="info-label">Assigned Users</div>
                <div class="info-value">
                    <?php if (!empty($assigned_users)): ?>
                        <?php foreach ($assigned_users as $user): ?>
                            <div class="d-flex align-items-center mb-1">
                                <?php
                                $initial = strtoupper($user->name[0]);
                                $color = dechex(crc32($user->name) & 0xffffff);
                                ?>
                                <img src="https://placehold.co/25x25/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                     class="rounded-circle me-2"
                                     style="width: 25px; height: 25px;"
                                     alt="<?= htmlspecialchars($user->name) ?>">
                                <span><?= htmlspecialchars($user->name) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-muted">No users assigned</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comments and Input Section -->
<div class="row">
    <!-- Comments Display - Left Side -->
    <div class="col-lg-6">
        <div class="form-card">
            <div class="form-section-title">
                <i class="fas fa-comments"></i>
                All Comments (<span id="commentCount"><?= count($comments) ?></span>)
            </div>

            <div id="commentsContainer" style="max-height: 500px; overflow-y: auto;">
                <?php if (empty($comments)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No comments yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item mb-3 p-3 border rounded">
                            <div class="d-flex gap-3">
                                <div>
                                    <?php
                                    $initial = strtoupper($comment->user_name[0]);
                                    $color = dechex(crc32($comment->user_name) & 0xffffff);
                                    ?>
                                    <div style="width: 40px; height: 40px; background: #<?= $color ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        <?= $initial ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <strong><?= htmlspecialchars($comment->user_name) ?></strong>
                                    <small class="text-muted ms-2"><?= date('M j, Y g:i A', strtotime($comment->created_at)) ?></small>
                                    <div class="mt-2"><?= nl2br(htmlspecialchars($comment->comment)) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Comment Form - Right Side -->
    <div class="col-lg-6">
        <div class="form-card">
            <div class="form-section-title">
                <i class="fas fa-plus-circle"></i>
                Add New Comment
            </div>

            <form id="commentForm">
                <input type="hidden" name="evaluation_id" value="<?= $evaluation->id ?>">

                <div class="form-group mb-3">
                    <label for="comment" class="form-label">Your Comment *</label>
                    <textarea name="comment" id="comment" class="form-control" rows="6"
                              placeholder="Enter your comment here..." required></textarea>
                    <small class="text-muted">Minimum 10 characters required</small>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-paper-plane"></i> Submit Comment
                </button>
                <button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="clearForm()">
                    <i class="fas fa-times"></i> Clear
                </button>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<style>
    .comment-item {
        background: #f8f9fa;
    }
    .form-control:focus {
        border-color: #2c5aa0;
        box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
    }
    .form-label {
        color: #2c5aa0;
        font-weight: 600;
    }
</style>

<script>
    // Clear comment form
    function clearForm() {
        $('#comment').val('');
    }

    // Load all comments via AJAX
    function loadComments() {
        $.ajax({
            url: '<?= base_url("work-progress/get_comments") ?>',
            type: 'GET',
            data: {evaluation_id: <?= $evaluation->id ?>},
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

                $('#commentsContainer').html(html);
                $('#commentCount').text(count);
                $('#commentsContainer').scrollTop($('#commentsContainer')[0].scrollHeight);
            }
        });
    }

    // On DOM ready
    $(function () {
        // Load initial comments
        loadComments();

        // jQuery validation
        $('#commentForm').on('submit', function (e) {
            e.preventDefault();
            const comment = $('#comment').val().trim();

            if (comment.length < 10) {
                alert('Comment must be at least 10 characters long.');
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
                        clearForm();
                        loadComments();
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
    });
</script>
