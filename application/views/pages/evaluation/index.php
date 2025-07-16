<?php $this->load->view('includes/header');
include('./application/views/pages/message.php');?>

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
        <a href="<?= base_url('work-progress/create') ?>" class="create-btn">
            <i class="fas fa-plus"></i> Add New Work
        </a>
    </div>
</div>

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
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $eval): ?>
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
                                ?>
                                <tr>
                                    <td><?= $eval->id ?></td>
                                    <td>
                                        <div>
                                            <?php
                                            $names = array_map('trim', explode(',', $eval->assigned_users));
                                            foreach ($names as $i => $name):
                                                $initial = strtoupper($name[0]);
                                                $color = dechex(crc32($name) & 0xffffff);
                                                ?>
                                                <div class="d-flex align-items-center mb-1">
                                                    <img src="https://placehold.co/30x30/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                         class="rounded-circle me-2"
                                                         style="width: 30px; height: 30px;"
                                                         alt="<?= htmlspecialchars($name) ?>">
                                                    <span class="staff-name"><?= htmlspecialchars($name) ?><?= $i < count($names) - 1 ? ',' : '' ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($eval->title) ?></td>
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
                                                       href="<?= base_url('work-progress/view/' . $eval->id) ?>"><i
                                                                class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('work-progress/edit/' . $eval->id) ?>"><i
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
                                                       href="<?= base_url('work-progress/comments/' . $eval->id) ?>"><i
                                                                class="fas fa-comments me-2"></i> Comments</a></li>
                                                <li>
                                                    <a href="javascript:void(0);"
                                                       class="dropdown-item btn-modal-comment"
                                                       data-id="<?= $eval->id ?>">
                                                        <i class="fas fa-comment-alt me-2"></i> Modal Comment
                                                    </a>
                                                </li>
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
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $eval): ?>
                            <?php if ($eval->status === 'completed'): ?>
                                <tr>
                                    <td><?= $eval->id ?></td>
                                    <td>
                                        <div>
                                            <?php
                                            $names = array_map('trim', explode(',', $eval->assigned_users));
                                            foreach ($names as $i => $name):
                                                $initial = strtoupper($name[0]);
                                                $color = dechex(crc32($name) & 0xffffff);
                                                ?>
                                                <div class="d-flex align-items-center mb-1">
                                                    <img src="https://placehold.co/30x30/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                         class="rounded-circle me-2"
                                                         style="width: 30px; height: 30px;"
                                                         alt="<?= htmlspecialchars($name) ?>">
                                                    <span class="staff-name"><?= htmlspecialchars($name) ?><?= $i < count($names) - 1 ? ',' : '' ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($eval->title) ?></td>
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
                                                       href="<?= base_url('work-progress/view/' . $eval->id) ?>"><i
                                                                class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('work-progress/edit/' . $eval->id) ?>"><i
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
                                                       href="<?= base_url('work-progress/comments/' . $eval->id) ?>"><i
                                                                class="fas fa-comments me-2"></i> Comments</a></li>
                                                <li>
                                                    <a href="javascript:void(0);"
                                                       class="dropdown-item btn-modal-comment"
                                                       data-id="<?= $eval->id ?>">
                                                        <i class="fas fa-comment-alt me-2"></i> Modal Comment
                                                    </a>
                                                </li>

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
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $eval): ?>
                            <?php if ($eval->status === 'on_hold'): ?>
                                <tr>
                                    <td><?= $eval->id ?></td>
                                    <td>
                                        <div>
                                            <?php
                                            $names = array_map('trim', explode(',', $eval->assigned_users));
                                            foreach ($names as $i => $name):
                                                $initial = strtoupper($name[0]);
                                                $color = dechex(crc32($name) & 0xffffff);
                                                ?>
                                                <div class="d-flex align-items-center mb-1">
                                                    <img src="https://placehold.co/30x30/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                         class="rounded-circle me-2"
                                                         style="width: 30px; height: 30px;"
                                                         alt="<?= htmlspecialchars($name) ?>">
                                                    <span class="staff-name"><?= htmlspecialchars($name) ?><?= $i < count($names) - 1 ? ',' : '' ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($eval->title) ?></td>
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
                                                       href="<?= base_url('work-progress/view/' . $eval->id) ?>"><i
                                                                class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('work-progress/edit/' . $eval->id) ?>"><i
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
                                                       href="<?= base_url('work-progress/comments/' . $eval->id) ?>"><i
                                                                class="fas fa-comments me-2"></i> Comments</a></li>
                                                <li>
                                                    <a href="javascript:void(0);"
                                                       class="dropdown-item btn-modal-comment"
                                                       data-id="<?= $eval->id ?>">
                                                        <i class="fas fa-comment-alt me-2"></i> Modal Comment
                                                    </a>
                                                </li>

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
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $eval): ?>
                            <?php
                            $statusColors = [
                                'pending' => ['#fff3cd', '#856404'],
                                'completed' => ['#d4edda', '#155724'],
                                'on_hold' => ['#ffeaa7', '#2d3436'],
                                'in_progress' => ['#cce7ff', '#004085'],
                                'overdue' => ['#f8d7da', '#721c24']
                            ];
                            $badge = $statusColors[$eval->status] ?? ['#eee', '#000'];
                            ?>
                            <tr>
                                <td><?= $eval->id ?></td>
                                <td>
                                    <div>
                                        <?php
                                        $names = array_map('trim', explode(',', $eval->assigned_users));
                                        foreach ($names as $i => $name):
                                            $initial = strtoupper($name[0]);
                                            $color = dechex(crc32($name) & 0xffffff);
                                            ?>
                                            <div class="d-flex align-items-center mb-1">
                                                <img src="https://placehold.co/30x30/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                     class="rounded-circle me-2"
                                                     style="width: 30px; height: 30px;"
                                                     alt="<?= htmlspecialchars($name) ?>">
                                                <span class="staff-name"><?= htmlspecialchars($name) ?><?= $i < count($names) - 1 ? ',' : '' ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($eval->title) ?></td>
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
                                                   href="<?= base_url('work-progress/view/' . $eval->id) ?>"><i
                                                            class="fas fa-eye me-2"></i> View</a></li>
                                            <li><a class="dropdown-item"
                                                   href="<?= base_url('work-progress/edit/' . $eval->id) ?>"><i
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
                                                   href="<?= base_url('work-progress/comments/' . $eval->id) ?>"><i
                                                            class="fas fa-comments me-2"></i> Comments</a></li>
                                            <li>
                                                <a href="javascript:void(0);"
                                                   class="dropdown-item btn-modal-comment"
                                                   data-id="<?= $eval->id ?>">
                                                    <i class="fas fa-comment-alt me-2"></i> Modal Comment
                                                </a>
                                            </li>
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


<?php $this->load->view('includes/footer'); ?>

<script>
    $(document).ready(function () {
        $('.table').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[3, 'desc']],
            columnDefs: [
                {targets: 0, width: "5%"},   // ID
                {targets: 1, width: "25%"},  // Assign To
                {targets: 2, width: "30%"},  // Title
                {targets: 3, width: "15%"},  // Added Date
                {targets: 4, width: "10%"},  // Status
                {targets: 5, width: "15%", orderable: false} // Actions
            ]
        });

        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', () => {
                setTimeout(() => $.fn.dataTable.tables({visible: true, api: true}).columns.adjust(), 100);
            });
        });

        // Fix dropdown scroll issues
        $(document).on('show.bs.dropdown', '.dropdown', function () {
            // Temporarily prevent body scroll
            $('body').css('overflow-x', 'hidden');

            // Get dropdown position
            const dropdown = $(this).find('.dropdown-menu');
            const button = $(this).find('.dropdown-toggle');
            const buttonOffset = button.offset();
            const dropdownHeight = dropdown.outerHeight();
            const windowHeight = $(window).height();

            // Check if dropdown will extend beyond viewport
            if (buttonOffset.top + dropdownHeight > windowHeight) {
                dropdown.addClass('dropup');
            }
        });

        $(document).on('hide.bs.dropdown', '.dropdown', function () {
            // Restore body scroll
            $('body').css('overflow-x', 'auto');
            $(this).find('.dropdown-menu').removeClass('dropup');
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
            data: {id: selectedEvalId, status: selectedStatus},
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
            data: {evaluation_id: evaluationId},
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
