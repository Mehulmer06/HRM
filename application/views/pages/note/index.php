<?php $this->load->view('includes/header'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title"><i class="fas fa-sticky-note"></i> Note Management</h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
                <span class="text-muted">Note Management</span>
            </nav>
        </div>
        <a href="<?= base_url('note/create') ?>" class="create-btn">
            <i class="fas fa-plus"></i> Add New Note
        </a>
    </div>
</div>

<!-- Note Tabs -->
<div class="staff-tabs">
    <ul class="nav nav-tabs" id="noteTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#open" type="button" role="tab">
                Open
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#closed" type="button" role="tab">
                Closed
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#deleted" type="button" role="tab">
                Deleted
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#draft" type="button" role="tab">
                Draft
            </button>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Open Tab -->
        <div class="tab-pane fade show active" id="open" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="openNotesTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Actions</th>
                            <th style="width: 20%;">Name/Date</th>
                            <th style="width: 25%;">Title</th>
                            <th style="width: 12%;">RO Status</th>
                            <th style="width: 12%;">Admin Status</th>
                            <th style="width: 11%;">Vishwambi Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($open_notes)): ?>
                            <?php foreach ($open_notes as $note): ?>
                                <tr>
                                    <td><?= $note->id ?></td>
                                    <td class="text-nowrap">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('note/view/' . $note->id) ?>">
                                                        <i class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item btn-note-discussion"
                                                       href="javascript:void(0);" data-id="<?= $note->id ?>">
                                                        <i class="fas fa-comments me-2"></i> Discussion</a></li>
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('note/edit/' . $note->id) ?>">
                                                        <i class="fas fa-edit me-2"></i> Edit</a></li>
                                                <li><a href="javascript:void(0);"
                                                       class="dropdown-item text-danger close-note-btn"
                                                       data-note-id="<?= $note->id ?>">
                                                        <i class="fas fa-times-circle me-2"></i> Close</a></li>
                                                <li><a href="javascript:void(0)"
                                                       class="dropdown-item text-danger btn-delete-note"
                                                       data-id="<?= $note->id ?>">
                                                        <i class="fas fa-trash-alt me-2"></i> Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $initial = strtoupper(substr($note->user_name, 0, 1));
                                            $color = dechex(crc32($note->user_name) & 0xffffff);
                                            ?>
                                            <img src="https://placehold.co/35x35/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $note->user_name ?>">
                                            <div>
                                                <div class="staff-name"><?= htmlspecialchars($note->user_name) ?></div>
                                                <small class="text-muted"><?= date('Y-m-d', strtotime($note->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="note-title"><?= htmlspecialchars($note->title) ?></div>
                                    </td>

                                    <!-- RO STATUS -->
                                    <td>
                                        <?php if ($note->is_approved_ro === '1'): ?>
                                            <span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span>
                                        <?php elseif ($note->is_approved_ro === '0'): ?>
                                            <span class="status-badge" style="background: #f8d7da; color: #721c24;">Rejected</span>
                                        <?php else: ?>
                                            <span class="status-badge" style="background: #fff3cd; color: #856404;">Pending</span>
                                        <?php endif; ?>

                                        <?php if (
                                            is_null($note->is_approved_ro) &&
                                            in_array($note->submitted_to, ['ro', 'ro_admin', 'ro_admin_vishwambi', 'ro_vishwambi'])
                                        ): ?>
                                            <br>
                                            <a href="javascript:void(0);" class="btn-note-action text-success"
                                               data-id="<?= $note->id ?>" data-role="ro">
                                                <i class="fas fa-check-circle me-1"></i> Take Action
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                    <!-- ADMIN STATUS -->
                                    <td>
                                        <?php if (in_array($note->submitted_to, ['ro', 'ro_vishwambi'])): ?>
                                            <span class="text-muted">-</span>
                                        <?php elseif ($note->is_approved_admin === '1'): ?>
                                            <span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span>
                                        <?php elseif ($note->is_approved_admin === '0'): ?>
                                            <span class="status-badge" style="background: #f8d7da; color: #721c24;">Rejected</span>
                                        <?php elseif ($note->is_approved_ro === '0'): ?>
                                            <span class="text-muted">N/A</span>
                                        <?php elseif ($note->is_approved_ro === '1'): ?>
                                            <span class="status-badge" style="background: #cce7ff; color: #004085;">Pending</span>
                                            <?php if (
                                                is_null($note->is_approved_admin) &&
                                                in_array($note->submitted_to, ['ro_admin', 'ro_admin_vishwambi'])
                                            ): ?>
                                                <br>
                                                <a href="javascript:void(0);" class="btn-note-action text-primary"
                                                   data-id="<?= $note->id ?>" data-role="admin">
                                                    <i class="fas fa-user-shield me-1"></i> Take Action
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="status-badge" style="background: #eee; color: #999;">Waiting for RO</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- VISHWAMBI STATUS -->
                                    <td>
                                        <?php
                                        $showVishNA = in_array($note->submitted_to, ['ro', 'ro_admin']);
                                        $needAdminApproval = in_array($note->submitted_to, ['ro_admin_vishwambi']);
                                        ?>

                                        <?php if ($showVishNA): ?>
                                            <span class="text-muted">-</span>
                                        <?php elseif ($note->is_approved_vishwambi === '1'): ?>
                                            <span class="status-badge" style="background: #d4edda; color: #155724;">Approved</span>
                                        <?php elseif ($note->is_approved_vishwambi === '0'): ?>
                                            <span class="status-badge" style="background: #f8d7da; color: #721c24;">Rejected</span>
                                        <?php elseif (
                                            $note->is_approved_ro === '0' ||
                                            ($needAdminApproval && $note->is_approved_admin === '0')
                                        ): ?>
                                            <span class="text-muted">N/A</span>
                                        <?php elseif (
                                            $note->is_approved_ro === '1' &&
                                            (!$needAdminApproval || $note->is_approved_admin === '1')
                                        ): ?>
                                            <span class="status-badge" style="background: #fff3cd; color: #856404;">Pending</span>
                                            <?php if (
                                                is_null($note->is_approved_vishwambi) &&
                                                in_array($note->submitted_to, ['ro_admin_vishwambi', 'ro_vishwambi'])
                                            ): ?>
                                                <br>
                                                <a href="javascript:void(0);" class="btn-note-action text-warning"
                                                   data-id="<?= $note->id ?>" data-role="vishwambi">
                                                    <i class="fas fa-user-tie me-1"></i> Take Action
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="status-badge" style="background: #eee; color: #999;">Waiting for Previous</span>
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

        <!-- Closed Tab -->
        <div class="tab-pane fade" id="closed" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="closedNotesTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Actions</th>
                            <th style="width: 18%;">Name/Date</th>
                            <th style="width: 22%;">Title</th>
                            <th style="width: 10%;">RO Status</th>
                            <th style="width: 10%;">Admin Status</th>
                            <th style="width: 10%;">Vishwambi Status</th>
                            <th style="width: 10%;">Final Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($closed_notes)): ?>
                            <?php foreach ($closed_notes as $note): ?>
                                <tr>
                                    <td><?= $note->id ?></td>
                                    <td class="text-nowrap">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                       href="<?= base_url('note/view/' . $note->id) ?>">
                                                        <i class="fas fa-eye me-2"></i> View</a></li>

                                                <li>
                                                    <a class="dropdown-item btn-note-discussion"
                                                       href="javascript:void(0);" data-id="<?= $note->id ?>">
                                                        <i class="fas fa-comments me-2"></i> Discussion
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $initial = strtoupper(substr($note->user_name, 0, 1));
                                            $color = dechex(crc32($note->user_name) & 0xffffff);
                                            ?>
                                            <img src="https://placehold.co/35x35/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $note->user_name ?>">
                                            <div>
                                                <div class="staff-name"><?= htmlspecialchars($note->user_name) ?></div>
                                                <small class="text-muted"><?= date('Y-m-d', strtotime($note->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="note-title"><?= htmlspecialchars($note->title) ?></div>
                                        <small class="text-muted">Closed: <?= date('Y-m-d', strtotime($note->updated_at)) ?></small>
                                    </td>
                                    <td><span class="status-badge"
                                        >N/A</span></td>
                                    <td><span class="status-badge"
                                        >N/A</span></td>
                                    <td><span class="status-badge"
                                        >N/A</span></td>
                                    <td>
                                <span class="status-badge" style="background: #d4edda; color: #155724;">
                                    Closed<?= !empty($note->closed_by_name) ? ' by ' . htmlspecialchars($note->closed_by_name) : '' ?>
                                </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Deleted Tab -->
        <div class="tab-pane fade" id="deleted" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="deletedNotesTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Actions</th>
                            <th style="width: 18%;">Name/Date</th>
                            <th style="width: 22%;">Title</th>
                            <th style="width: 10%;">RO Status</th>
                            <th style="width: 10%;">Admin Status</th>
                            <th style="width: 10%;">Vishwambi Status</th>
                            <th style="width: 10%;">Delete Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($deleted_notes)): ?>
                            <?php foreach ($deleted_notes as $note): ?>
                                <tr class="table-danger">
                                    <td><?= $note->id ?></td>
                                    <td class="text-nowrap">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-danger dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#">
                                                        <i class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item" href="#">
                                                        <i class="fas fa-undo me-2"></i> Restore</a></li>
                                                <li><a class="dropdown-item text-danger" href="#">
                                                        <i class="fas fa-trash-alt me-2"></i> Permanent Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $initial = strtoupper(substr($note->user_name, 0, 1));
                                            $color = dechex(crc32($note->user_name) & 0xffffff);
                                            ?>
                                            <img src="https://placehold.co/35x35/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $note->user_name ?>">
                                            <div>
                                                <div class="staff-name text-muted"><?= htmlspecialchars($note->user_name) ?></div>
                                                <small class="text-danger">Deleted: <?= date('Y-m-d', strtotime($note->deleted_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="note-title text-muted"><?= htmlspecialchars($note->title) ?></div>
                                        <small class="text-danger">Reason: Soft Deleted</small>
                                    </td>
                                    <td><span class="status-badge"
                                              style="background: #f8d7da; color: #721c24;">Deleted</span></td>
                                    <td><span class="status-badge"
                                              style="background: #f8d7da; color: #721c24;">Deleted</span></td>
                                    <td><span class="status-badge"
                                              style="background: #f8d7da; color: #721c24;">Deleted</span></td>
                                    <td><span class="status-badge"
                                              style="background: #f8d7da; color: #721c24;">Deleted</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Draft Tab -->
        <div class="tab-pane fade" id="draft" role="tabpanel">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="draftNotesTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Actions</th>
                            <th style="width: 18%;">Name/Date</th>
                            <th style="width: 22%;">Title</th>
                            <th style="width: 10%;">RO Status</th>
                            <th style="width: 10%;">Admin Status</th>
                            <th style="width: 10%;">Vishwambi Status</th>
                            <th style="width: 10%;">Draft Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($draft_notes)): ?>
                            <?php foreach ($draft_notes as $note): ?>
                                <tr class="table-secondary">
                                    <td><?= $note->id ?></td>
                                    <td class="text-nowrap">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cogs"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#">
                                                        <i class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item" href="#">
                                                        <i class="fas fa-edit me-2"></i> Edit</a></li>
                                                <li><a class="dropdown-item" href="#">
                                                        <i class="fas fa-paper-plane me-2"></i> Publish</a></li>
                                                <li><a class="dropdown-item" href="#">
                                                        <i class="fas fa-trash me-2"></i> Delete Draft</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $initial = strtoupper(substr($note->user_name, 0, 1));
                                            $color = dechex(crc32($note->user_name) & 0xffffff);
                                            ?>
                                            <img src="https://placehold.co/35x35/<?= $color ?>/ffffff?text=<?= $initial ?>"
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px;"
                                                 alt="<?= $note->user_name ?>">
                                            <div>
                                                <div class="staff-name text-muted"><?= htmlspecialchars($note->user_name) ?></div>
                                                <small class="text-muted">Created: <?= date('Y-m-d', strtotime($note->created_at)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="note-title text-muted">
                                            Draft: <?= htmlspecialchars($note->title) ?></div>
                                    </td>
                                    <td><span class="status-badge"
                                              style="background: #e2e3e5; color: #6c757d;">Draft</span></td>
                                    <td><span class="status-badge"
                                              style="background: #e2e3e5; color: #6c757d;">Draft</span></td>
                                    <td><span class="status-badge"
                                              style="background: #e2e3e5; color: #6c757d;">Draft</span></td>
                                    <td><span class="status-badge"
                                              style="background: #e2e3e5; color: #6c757d;">Draft</span></td>
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


<!-- Close Note Modal -->
<div class="modal fade" id="closeNoteModal" tabindex="-1" aria-labelledby="closeNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="closeNoteForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closeNoteModalLabel">Confirm Note Closure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="closeNoteId">
                    <div class="mb-3">
                        <label for="noteCloseRemarks" class="form-label">Remarks (optional)</label>
                        <textarea class="form-control" id="noteCloseRemarks" rows="3"
                                  placeholder="Enter any closing remarks"></textarea>
                    </div>
                    <p>Are you sure you want to close this note?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmCloseNote">Yes, Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteNoteModal" tabindex="-1" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteNoteModalLabel"><i class="fas fa-trash-alt me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this note?
            </div>
            <div class="modal-footer">
                <input type="hidden" id="deleteNoteId">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteNoteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Discussion Modal -->
<div class="modal fade" id="modalNoteDiscussion" tabindex="-1" aria-labelledby="modalNoteDiscussionLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNoteDiscussionLabel">Note Discussion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Discussion Form -->
                <form id="noteDiscussionForm" enctype="multipart/form-data">
                    <input type="hidden" name="note_id" id="discussionNoteId">

                    <div class="mb-3">
                        <label for="discussionRemarks" class="form-label">Remarks *</label>
                        <textarea name="remarks" id="discussionRemarks" class="form-control" rows="4" required
                                  placeholder="Enter your remarks here."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="discussionFile" class="form-label">Attachment (optional)</label>
                        <input type="file" name="document" id="discussionFile" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> Submit
                    </button>
                </form>

                <hr>

                <!-- Discussions List -->
                <div class="form-section-title mt-4">
                    <i class="fas fa-comments"></i> All Discussions (<span id="discussionCount">0</span>)
                </div>
                <div id="noteDiscussionList" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Take Action Modal -->
<div class="modal fade" id="takeActionModal" tabindex="-1" aria-labelledby="takeActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="takeActionForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="takeActionModalLabel">Take Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="note_id" id="actionNoteId">
                    <input type="hidden" name="action_type" id="actionType">

                    <div class="mb-3">
                        <label for="actionRemarks" class="form-label">Remarks *</label>
                        <textarea class="form-control" id="actionRemarks" name="remarks" rows="4" required
                                  placeholder="Enter your remarks..."></textarea>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger btn-action-submit" data-action="reject">
                        <i class="fas fa-times"></i> Reject
                    </button>
                    <button type="button" class="btn btn-success btn-action-submit" data-action="approve">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>

<script>
    $(document).ready(function () {
        // Initialize DataTables for all tables
        $('.table').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']], // Order by ID descending
            columnDefs: [
                {targets: 1, orderable: false}, // Actions column not sortable
            ],
            language: {
                search: "Search Notes:",
                lengthMenu: "Show _MENU_ notes per page",
                info: "Showing _START_ to _END_ of _TOTAL_ notes",
            }
        });

        // Handle tab switching and refresh tables
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            setTimeout(function () {
                $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
            }, 100);
        });
    });

    $(document).on('click', '.close-note-btn', function () {
        const noteId = $(this).data('note-id');
        $('#closeNoteId').val(noteId);
        $('#noteCloseRemarks').val('');
        $('#closeNoteModal').modal('show');
    });

    $('#confirmCloseNote').click(function () {
        const noteId = $('#closeNoteId').val();
        const remarks = $('#noteCloseRemarks').val();

        $.ajax({
            url: '<?= base_url('note/close') ?>',
            method: 'POST',
            data: {
                note_id: noteId,
                note_close_remarks: remarks
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#closeNoteModal').modal('hide');
                    alert('Note closed successfully.');
                    location.reload();
                } else {
                    alert('Failed to close the note.');
                }
            },
            error: function () {
                alert('Something went wrong.');
            }
        });
    });


    let noteIdToDelete = null;

    // Trigger modal on delete button click
    $(document).on('click', '.btn-delete-note', function () {
        noteIdToDelete = $(this).data('id');
        $('#deleteNoteId').val(noteIdToDelete);
        const modal = new bootstrap.Modal(document.getElementById('deleteNoteModal'));
        modal.show();
    });

    // Confirm deletion from modal
    $('#confirmDeleteNoteBtn').click(function () {
        const id = $('#deleteNoteId').val();

        $.ajax({
            url: '<?= base_url('note/delete') ?>',
            type: 'POST',
            data: {id},
            dataType: 'json',
            success: function (response) {
                $('#deleteNoteModal').modal('hide');
                alert(response.message);
                if (response.success) {
                    location.reload();
                }
            },
            error: function () {
                $('#deleteNoteModal').modal('hide');
                alert('Error deleting the note.');
            }
        });
    });


</script>


<script>
    $(document).ready(function () {

        // Open Note Discussion Modal and Load Discussions
        $(document).on('click', '.btn-note-discussion', function () {
            const noteId = $(this).data('id');
            $('#discussionNoteId').val(noteId); // set hidden input
            $('#modalNoteDiscussion').modal('show');
            loadNoteDiscussions(noteId);
        });

        // Submit Discussion Form
        $('#noteDiscussionForm').submit(function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const noteId = $('#discussionNoteId').val();

            $.ajax({
                url: '<?= base_url('note/add-discussion') ?>',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Reset form
                        form.reset();

                        // Refresh discussion list
                        loadNoteDiscussions(noteId);

                        // Optional: Show toast or alert
                        // alert('Discussion added successfully!');
                    } else {
                        alert(response.message || 'Failed to submit discussion.');
                    }
                },
                error: function () {
                    alert('Error submitting discussion.');
                }
            });
        });


        // Load Discussions for Given Note ID
        function loadNoteDiscussions(noteId) {
            $.ajax({
                url: '<?= base_url('note/get-discussions') ?>',
                method: 'POST',
                data: {note_id: noteId},
                dataType: 'json',
                success: function (res) {
                    if (res.success && Array.isArray(res.data)) {
                        $('#discussionCount').text(res.data.length);
                        renderNoteDiscussions(res.data);
                    } else {
                        $('#noteDiscussionList').html('<div class="text-danger">No discussion data found.</div>');
                    }
                },
                error: function () {
                    $('#noteDiscussionList').html('<div class="text-danger">Error loading discussions.</div>');
                }
            });
        }

        // Render Discussion List
        function renderNoteDiscussions(discussions) {
            const container = $('#noteDiscussionList');
            container.empty();

            if (discussions.length === 0) {
                container.html('<div class="text-muted">No discussions yet.</div>');
                return;
            }

            discussions.forEach(item => {
                const html = `
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between mb-1">
                        <strong>${item.user_name}</strong>
                        <small class="text-muted">${item.created_at}</small>
                    </div>
                    <div>${item.remarks}</div>
                    ${item.document ? `
                        <a href="${item.document}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="fas fa-download me-1"></i> Download
                        </a>` : ''
                }
                </div>
            `;
                container.append(html);
            });
        }

    });

    let currentActionRole = '';

    $(document).on('click', '.btn-note-action', function () {
        const noteId = $(this).data('id');
        currentActionRole = $(this).data('role');

        $('#actionNoteId').val(noteId);
        $('#actionRemarks').val('');
        $('#takeActionModal').modal('show');
    });

    $('#takeActionForm').submit(function (e) {
        e.preventDefault();
        const formData = {
            note_id: $('#actionNoteId').val(),
            remarks: $('#actionRemarks').val(),
            role: currentActionRole
        };

        $.ajax({
            url: '<?= base_url('note/take-action') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#takeActionModal').modal('hide');
                    alert('Action submitted successfully.');
                    location.reload();
                } else {
                    alert(res.message || 'Failed to submit action.');
                }
            },
            error: function () {
                alert('Error submitting action.');
            }
        });
    });


</script>

<script>
    $(document).ready(function () {
        let selectedAction = '';

        // Trigger modal open
        $(document).on('click', '.btn-note-action', function () {
            const noteId = $(this).data('id');
            currentActionRole = $(this).data('role');

            $('#actionNoteId').val(noteId);
            $('#actionRemarks').val('');
            selectedAction = '';
            $('#takeActionModal').modal('show');
        });

        // Handle Approve/Reject Button Clicks
        $(document).on('click', '.btn-action-submit', function () {
            selectedAction = $(this).data('action');

            const noteId = $('#actionNoteId').val();
            const remarks = $('#actionRemarks').val().trim();

            if (!remarks) {
                alert('Please enter remarks.');
                return;
            }

            const payload = {
                note_id: noteId,
                remarks: remarks,
                is_approved: selectedAction === 'approve' ? 1 : 0,
                role: currentActionRole
            };

            $.ajax({
                url: '<?= base_url('note/take-action') ?>',
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        $('#takeActionModal').modal('hide');
                        alert(res.message || 'Action submitted successfully.');
                        location.reload();
                    } else {
                        alert(res.message || 'Failed to take action.');
                    }
                },
                error: function () {
                    alert('Server error. Please try again.');
                }
            });
        });
    });

</script>
