<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php');
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-users"></i>
                Project Staff Management
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('shrm/dashboard') ?>">Dashboard</a> / <span class="text-muted">Project Staff</span>
            </nav>
        </div>
        <?php if ($this->session->userdata('role') == 'e' || $this->session->userdata('category') == 'admin') : ?>
        <a href="<?= base_url('project-staff/create') ?>" class="create-btn ">
            <i class="fas fa-plus"></i>
            Add New Staff
        </a>
        <?php endif  ?>
    </div>
</div>

<?php
// Separate active and inactive users
$active_users = [];
$inactive_users = [];

if (isset($users) && is_array($users)) {
    foreach ($users as $user) {
        if ($user->status === 'Y') {
            $active_users[] = $user;
        } else {
            $inactive_users[] = $user;
        }
    }
}

$total_active = count($active_users);
$total_inactive = count($inactive_users);
?>

<!-- Staff Tabs -->
<div class="staff-tabs">
    <ul class="nav nav-tabs" id="staffTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button"
                    role="tab">
                <i class="fas fa-user-check me-2"></i>
                Active Staff (<?php echo $total_active; ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive" type="button"
                    role="tab">
                <i class="fas fa-user-times me-2"></i>
                Inactive Staff (<?php echo $total_inactive; ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="staffTabContent">
        <!-- Active Staff Tab -->
        <div class="tab-pane fade show active" id="active" role="tabpanel">

            <!-- Active Staff Table -->
            <div class="table-container">
                <table id="activeStaffTable" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Staff</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($active_users)): ?>
                        <?php foreach ($active_users as $index => $user): ?>
                            <tr>
                                <td><?php echo($index + 1); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        // Generate initials from name
                                        $initials = '';
                                        if (!empty($user->name)) {
                                            $name_parts = explode(' ', trim($user->name));
                                            $initials = strtoupper(substr($name_parts[0], 0, 1));
                                            if (isset($name_parts[1])) {
                                                $initials .= strtoupper(substr($name_parts[1], 0, 1));
                                            }
                                        }
                                        // Generate random color for avatar
                                        $colors = ['3498db', 'e74c3c', '2ecc71', '9b59b6', 'f39c12', '1abc9c'];
                                        $color = $colors[($user->id - 1) % count($colors)];

                                        $avatar_url = "https://placehold.co/40x40/{$color}/ffffff?text={$initials}";
                                        ?>
                                        <img src="<?php echo $avatar_url; ?>" class="staff-photo me-3"
                                             alt="<?php echo htmlspecialchars($user->name); ?>">
                                        <div>
                                            <p class="staff-name"><a class="text-decoration-none" href="<?= base_url('project-staff/show/' . base_convert($user->id * 15395, 10, 36)) ?>
"><?php echo htmlspecialchars($user->name); ?>
                                                </a></p>
                                            <p class="staff-department">
                                                <?php echo !empty($user->designation) ? ucfirst(htmlspecialchars($user->designation)) : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo !empty($user->phone) ? htmlspecialchars($user->phone) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($user->email); ?></td>
                                <td><?php echo !empty($user->department) ? ucwords(htmlspecialchars($user->department)) : 'Not Assigned'; ?>
                                </td>
                                <td><span class="status-badge status-active">Active</span></td>
                                <td>

                                    <a href="<?= base_url('project-staff/edit/' . base_convert($user->id * 15395, 10, 36)) ?>" class="btn btn-sm btn-edit" data-action="edit"
                                            data-id="<?php echo $user->id; ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
<!--                                    <button class="action-btn btn-toggle" data-id="--><?php //echo $user->id; ?><!--"-->
<!--                                            data-status="deactivate"-->
<!--                                            data-name="--><?php //echo htmlspecialchars($user->name); ?><!--"-->
<!--                                            title="Deactivate">-->
<!--                                        <i class="fas fa-user-times"></i>-->
<!--                                    </button>-->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inactive Staff Tab -->
        <div class="tab-pane fade" id="inactive" role="tabpanel">


            <!-- Inactive Staff Table -->
            <div class="table-container">
                <table id="inactiveStaffTable" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Staff</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($inactive_users)): ?>
                        <?php foreach ($inactive_users as $index => $user): ?>
                            <tr>
                                <td><?php echo($index + 1); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        // Generate initials from name
                                        $initials = '';
                                        if (!empty($user->name)) {
                                            $name_parts = explode(' ', trim($user->name));
                                            $initials = strtoupper(substr($name_parts[0], 0, 1));
                                            if (isset($name_parts[1])) {
                                                $initials .= strtoupper(substr($name_parts[1], 0, 1));
                                            }
                                        }

                                        $avatar_url = !empty($user->photo) ? $user->photo :
                                            "https://placehold.co/40x40/95a5a6/ffffff?text={$initials}";
                                        ?>
                                        <img src="<?php echo $avatar_url; ?>" class="staff-photo me-3"
                                             alt="<?php echo htmlspecialchars($user->name); ?>">
                                        <div>
                                            <p class="staff-name"><?php echo htmlspecialchars($user->name); ?></p>
                                            <p class="staff-department">
                                                <?php echo !empty($user->role) ? 'Former ' . htmlspecialchars($user->role) : 'Former Staff Member'; ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo !empty($user->phone) ? htmlspecialchars($user->phone) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($user->email); ?></td>
                                <td><?php echo !empty($user->department) ? htmlspecialchars($user->department) : 'Not Assigned'; ?>
                                </td>
                                <td><span class="status-badge status-inactive">Inactive</span></td>
                                <td>
                                    <button class="action-btn btn-view" data-action="view"
                                            data-id="<?php echo $user->id; ?>" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn btn-edit" data-action="edit"
                                            data-id="<?php echo $user->id; ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn btn-toggle activate" data-id="<?php echo $user->id; ?>"
                                            data-status="activate"
                                            data-name="<?php echo htmlspecialchars($user->name); ?>"
                                            title="Activate">
                                        <i class="fas fa-user-check"></i>
                                    </button>
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

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6 id="toggleMessage"></h6>
                <p>Staff: <strong id="staffName"></strong></p>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="confirmToggle">Confirm</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('shrm_views/includes/footer'); ?>
<script>
    // Initialize DataTables
    $(document).ready(function () {
        // Active Staff Table
		$('#activeStaffTable').DataTable({
			processing: true,
			paging: true,
			searching: true,
			ordering: true,
			info: true,
			autoWidth: false,
			responsive: true,
			lengthChange: true,

			lengthMenu: [
				[10, 25, 50, 100, -1],
				[10, 25, 50, 100, "All"]
			],

			dom:
				'<"row align-items-center mb-3"' +
				'<"col-md-6 d-flex gap-2 align-items-center"lB>' +
				'<"col-md-6 text-md-end mt-2 mt-md-0"f>' +
				'>' +
				'<"row"<"col-12"tr>>' +
				'<"row align-items-center mt-3"' +
				'<"col-md-6"i>' +
				'<"col-md-6 d-flex justify-content-end"p>' +  <!-- ✅ Fixed this line -->
				'>',

			buttons: [
				// {
				// 	extend: 'copyHtml5',
				// 	text: '<i class="fas fa-copy" style="color:#fff;"></i>',
				// 	titleAttr: 'Copy',
				// 	className: '',
				// 	init: function(api, node, config) {
				// 		$(node).css({
				// 			'background-color': '#6c757d',
				// 			'border': 'none',
				// 			'padding': '8px 12px',
				// 			'border-radius': '6px',
				// 			'margin-right': '6px',
				// 			'font-size': '16px',
				// 			'color': '#fff',
				// 			'box-shadow': '0 2px 6px rgba(0,0,0,0.1)',
				// 			'transition': 'background 0.3s'
				// 		}).hover(function () {
				// 			$(this).css('background-color', '#5a6268');
				// 		}, function () {
				// 			$(this).css('background-color', '#6c757d');
				// 		});
				// 	}
				// },
				// {
				// 	extend: 'excelHtml5',
				// 	text: '<i class="fas fa-file-excel" style="color:#fff;"></i>',
				// 	titleAttr: 'Export to Excel',
				// 	className: '',
				// 	init: function(api, node, config) {
				// 		$(node).css({
				// 			'background-color': '#28a745',
				// 			'border': 'none',
				// 			'padding': '8px 12px',
				// 			'border-radius': '6px',
				// 			'margin-right': '6px',
				// 			'font-size': '16px',
				// 			'color': '#fff',
				// 			'box-shadow': '0 2px 6px rgba(0,0,0,0.1)',
				// 			'transition': 'background 0.3s'
				// 		}).hover(function () {
				// 			$(this).css('background-color', '#218838');
				// 		}, function () {
				// 			$(this).css('background-color', '#28a745');
				// 		});
				// 	}
				// },
				// {
				// 	extend: 'pdfHtml5',
				// 	text: '<i class="fas fa-file-pdf" style="color:#fff;"></i>',
				// 	titleAttr: 'Export to PDF',
				// 	className: '',
				// 	init: function(api, node, config) {
				// 		$(node).css({
				// 			'background-color': '#dc3545',
				// 			'border': 'none',
				// 			'padding': '8px 12px',
				// 			'border-radius': '6px',
				// 			'margin-right': '6px',
				// 			'font-size': '16px',
				// 			'color': '#fff',
				// 			'box-shadow': '0 2px 6px rgba(0,0,0,0.1)',
				// 			'transition': 'background 0.3s'
				// 		}).hover(function () {
				// 			$(this).css('background-color', '#c82333');
				// 		}, function () {
				// 			$(this).css('background-color', '#dc3545');
				// 		});
				// 	}
				// },
				// {
				// 	extend: 'print',
				// 	text: '<i class="fas fa-print" style="color:#fff;"></i>',
				// 	titleAttr: 'Print',
				// 	className: '',
				// 	init: function(api, node, config) {
				// 		$(node).css({
				// 			'background-color': '#007bff',
				// 			'border': 'none',
				// 			'padding': '8px 12px',
				// 			'border-radius': '6px',
				// 			'margin-right': '6px',
				// 			'font-size': '16px',
				// 			'color': '#fff',
				// 			'box-shadow': '0 2px 6px rgba(0,0,0,0.1)',
				// 			'transition': 'background 0.3s'
				// 		}).hover(function () {
				// 			$(this).css('background-color', '#0069d9');
				// 		}, function () {
				// 			$(this).css('background-color', '#007bff');
				// 		});
				// 	}
				// }
			],

			language: {
				search: "Search Staff:",
				lengthMenu: "Show _MENU_ staff per page",
				info: "Showing _START_ to _END_ of _TOTAL_ staff members",
				emptyTable: "No active staff members found",
				zeroRecords: "No matching staff members found"
			}
		});





		// Inactive Staff Table
        $('#inactiveStaffTable').DataTable({
			processing: true,
			paging: true,
			searching: true,
			ordering: true,
			info: true,
			autoWidth: false,
			responsive: true,
			lengthChange: true,

			lengthMenu: [
				[10, 25, 50, 100, -1],
				[10, 25, 50, 100, "All"]
			],

			dom:
				'<"row align-items-center mb-3"' +
				'<"col-md-6 d-flex gap-2 align-items-center"lB>' +
				'<"col-md-6 text-md-end mt-2 mt-md-0"f>' +
				'>' +
				'<"row"<"col-12"tr>>' +
				'<"row align-items-center mt-3"' +
				'<"col-md-6"i>' +
				'<"col-md-6 d-flex justify-content-end"p>' +  <!-- ✅ Fixed this line -->
				'>',

			buttons: [
				// {
				// 	extend: 'copyHtml5',
				// 	text: '<i class="fas fa-copy" style="color:#fff;"></i>',
				// 	titleAttr: 'Copy',
				// 	className: '',
				// 	init: function(api, node, config) {
				// 		$(node).css({
				// 			'background-color': '#6c757d',
				// 			'border': 'none',
				// 			'padding': '8px 12px',
				// 			'border-radius': '6px',
				// 			'margin-right': '6px',
				// 			'font-size': '16px',
				// 			'color': '#fff',
				// 			'box-shadow': '0 2px 6px rgba(0,0,0,0.1)',
				// 			'transition': 'background 0.3s'
				// 		}).hover(function () {
				// 			$(this).css('background-color', '#5a6268');
				// 		}, function () {
				// 			$(this).css('background-color', '#6c757d');
				// 		});
				// 	}
				// },
				// {
				// 	extend: 'excelHtml5',
				// 	text: '<i class="fas fa-file-excel" style="color:#fff;"></i>',
				// 	titleAttr: 'Export to Excel',
				// 	className: '',
				// 	init: function(api, node, config) {
				// 		$(node).css({
				// 			'background-color': '#28a745',
				// 			'border': 'none',
				// 			'padding': '8px 12px',
				// 			'border-radius': '6px',
				// 			'margin-right': '6px',
				// 			'font-size': '16px',
				// 			'color': '#fff',
				// 			'box-shadow': '0 2px 6px rgba(0,0,0,0.1)',
				// 			'transition': 'background 0.3s'
				// 		}).hover(function () {
				// 			$(this).css('background-color', '#218838');
				// 		}, function () {
				// 			$(this).css('background-color', '#28a745');
				// 		});
				// 	}
				// },
				// {
				// 	extend: 'pdfHtml5',
				// 	text: '<i class="fas fa-file-pdf" style="color:#fff;"></i>',
				// 	titleAttr: 'Export to PDF',
				// 	className: '',
				// 	init: function(api, node, config) {
				// 		$(node).css({
				// 			'background-color': '#dc3545',
				// 			'border': 'none',
				// 			'padding': '8px 12px',
				// 			'border-radius': '6px',
				// 			'margin-right': '6px',
				// 			'font-size': '16px',
				// 			'color': '#fff',
				// 			'box-shadow': '0 2px 6px rgba(0,0,0,0.1)',
				// 			'transition': 'background 0.3s'
				// 		}).hover(function () {
				// 			$(this).css('background-color', '#c82333');
				// 		}, function () {
				// 			$(this).css('background-color', '#dc3545');
				// 		});
				// 	}
				// },
				// {
				// 	extend: 'print',
				// 	text: '<i class="fas fa-print" style="color:#fff;"></i>',
				// 	titleAttr: 'Print',
				// 	className: '',
				// 	init: function(api, node, config) {
				// 		$(node).css({
				// 			'background-color': '#007bff',
				// 			'border': 'none',
				// 			'padding': '8px 12px',
				// 			'border-radius': '6px',
				// 			'margin-right': '6px',
				// 			'font-size': '16px',
				// 			'color': '#fff',
				// 			'box-shadow': '0 2px 6px rgba(0,0,0,0.1)',
				// 			'transition': 'background 0.3s'
				// 		}).hover(function () {
				// 			$(this).css('background-color', '#0069d9');
				// 		}, function () {
				// 			$(this).css('background-color', '#007bff');
				// 		});
				// 	}
				// }
			],

			language: {
				search: "Search Staff:",
				lengthMenu: "Show _MENU_ staff per page",
				info: "Showing _START_ to _END_ of _TOTAL_ staff members",
				emptyTable: "No active staff members found",
				zeroRecords: "No matching staff members found"
			}
        });

        // Tab switching with table refresh
        $('[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            setTimeout(() => {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
            }, 100);
        });
    });

    // Toggle functionality
    let toggleId, toggleStatus;

    $(document).on('click', '.action-btn', function (e) {
        e.preventDefault();

        const action = $(this).data('action');
        const id = $(this).data('id');

        switch (action) {
            case 'view':
                window.location.href = `<?php echo base_url('project-staff/show/'); ?>${id}`;
                break;
            case 'edit':
                window.location.href = `<?php echo base_url('project-staff/edit/'); ?>${id}`;
                break;
            case 'contracts':
                window.location.href = `<?php echo base_url('project-staff/contracts/'); ?>${id}`;
                break;
        }
    });

    $(document).on('click', '.btn-toggle', function () {
        toggleId = $(this).data('id');
        toggleStatus = $(this).data('status') === 'activate' ? 'Y' : 'N';
        const name = $(this).data('name');
        const action = $(this).data('status') === 'activate' ? 'activate' : 'deactivate';

        $('#toggleMessage').text(`Are you sure you want to ${action} this staff member?`);
        $('#staffName').text(name);
        $('#toggleModal').modal('show');
    });

    $(document).on('click', '#confirmToggle', function () {
        $.ajax({
            url: '<?php echo base_url("project-staff/toggle-status"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                id: toggleId,
                status: toggleStatus
            },
            success: function (response) {
                $('#toggleModal').modal('hide');
                if (response.success) {
                    l̥
                    alert('Status updated successfully');
                    // Reload DataTables instead of page
                    location.reload();
                } else {
                    alert('Error updating status');
                }
            },
            error: function (xhr, status, error) {
                $('#toggleModal').modal('hide');
                alert('Error updating status');
                console.log(error);
            }
        });
    });
</script>
