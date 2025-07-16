
<?php $this->load->view('includes/header'); ?>

<!-- Page Header -->
<div class="page-header">
	<div class="d-flex justify-content-between align-items-start">
		<div>
			<h1 class="page-title"><i class="fas fa-clipboard-list"></i> Request/Issue/Note to Admin</h1>
			<nav class="breadcrumb-nav">
				<a href="<?= base_url('dashboard') ?>">Dashboard</a> /
				<span class="text-muted">Request/Issue/Note</span>
			</nav>
		</div>
		<div class="d-flex gap-2">
			<a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary">
				<i class="fas fa-arrow-left me-2"></i>
				Back to List
			</a>
		</div>
	</div>
</div>

<!-- Request Form Card -->
<div class="form-card">
	<div class="form-section-title">
		<i class="fas fa-plus-circle"></i> Submit New Request/Issue/Note
	</div>
	<?php if($this->session->flashdata('success')): ?>
		<div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
	<?php endif; ?>

	<?php if($this->session->flashdata('error')): ?>
		<div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
	<?php endif; ?>

	<?= form_open_multipart('request-issue/store', ['id' => 'requestForm']) ?>
	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="title">Title *</label>
			<input type="text" class="form-control <?= form_error('title') ? 'is-invalid' : '' ?>" name="title" value="<?= set_value('title') ?>">
			<?= form_error('title', '<div class="invalid-feedback">', '</div>') ?>
		</div>

		<div class="col-md-6 mb-3">
			<label for="submitted_to">Submitted To *</label>
			<select class="form-select <?= form_error('submitted_to') ? 'is-invalid' : '' ?>" name="submitted_to">
				<option value="">Select Submitted </option>
				<option value="admin" <?= set_select('submitted_to', 'admin') ?>>Admin</option>
				<option value="hr" <?= set_select('submitted_to', 'hr') ?>>HR</option>
				<option value="it" <?= set_select('submitted_to', 'it') ?>>IT</option>
				<option value="finance" <?= set_select('submitted_to', 'finance') ?>>Finance</option>
				<option value="management" <?= set_select('submitted_to', 'management') ?>>Management</option>
			</select>
			<?= form_error('submitted_to', '<div class="invalid-feedback">', '</div>') ?>
		</div>
	</div>

	<div class="mb-3">
		<label for="description">Description *</label>
		<textarea name="description" id="description" class="form-control <?= form_error('description') ? 'is-invalid' : '' ?>"><?= set_value('description') ?></textarea>
		<?= form_error('description', '<div class="invalid-feedback">', '</div>') ?>
	</div>

	<div class="mb-3">
		<label for="document">Upload Document</label>
		<input type="file" class="form-control <?= form_error('document') ? 'is-invalid' : '' ?>" name="document">
		<?= form_error('document', '<div class="invalid-feedback">', '</div>') ?>
	</div>

	<div class="text-end">
		<button type="reset" class="btn btn-secondary">Reset</button>
		<button type="submit" class="btn btn-primary">Submit</button>
	</div>
	<?= form_close() ?>
</div>

<!-- Request/Issue/Note Tabs -->
<div class="staff-tabs">
	<ul class="nav nav-tabs" id="requestTabs" role="tablist">
		<li class="nav-item">
			<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#myRequests" type="button" role="tab">
				My Requests
			</button>
		</li>
		<li class="nav-item">
			<button class="nav-link" data-bs-toggle="tab" data-bs-target="#closedRequests" type="button" role="tab">
				Closed
			</button>
		</li>
	</ul>

	<div class="tab-content">
		<!-- My Requests Tab -->
		<div class="tab-pane fade show active" id="myRequests" role="tabpanel">
			<div class="table-container">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
						<tr>
							<th style="width: 5%;">ID</th>
							<th style="width: 15%;">Submitted To</th>
							<th style="width: 25%;">Title</th>
							<th>Description</th>
							<th style="width: 15%;">Added Date</th>
							<th style="width: 12%;">Status</th>
							<th style="width: 15%;">Actions</th>
						</tr>
						</thead>
						<tbody>
						<?php if (!empty($requestData)): ?>
							<?php foreach ($requestData as $row): ?>
								<tr>
									<td><?= $row->id ?? '' ?></td>
									<td>
										<div class="staff-name"><?= ucfirst($row->submitted_to ?? '') ?></div>
										<small class="text-muted"></small>
									</td>
									<td>
										<div class="fw-bold"><?= ucfirst($row->title ?? '') ?></div>
										<small class="text-muted"></small>
									</td>
									<td>
										<div class="fw-bold"><?= ucfirst($row->description ?? '') ?></div>
										<small class="text-muted"></small>
									</td>
									<td><?= date('Y-m-d', strtotime($row->request_date ?? '')) ?></td>
									<td>
										<?php if ($row->status == 'in_progress'): ?>
											<span class="status-badge" style="background: #d4edda; color: #155724;">in progress</span>
										<?php endif;?>
									</td>
									<td>
										<div class="dropdown">
											<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
												Actions
											</button>
											<ul class="dropdown-menu dropdown-menu-end">
												<li>
													<a class="dropdown-item" href="<?= base_url('request-issue/show/' . $row->id) ?>">
														<i class="fas fa-eye me-2"></i> View
													</a>
												</li>
												<li>
													<a href="#" class="dropdown-item"
													   data-bs-toggle="modal"
													   data-bs-target="#modalComment"
													   onclick="openCommentModal(<?= $row->id ?? '' ?>)">
														<i class="fas fa-edit me-2"></i> Comment
													</a>
												</li>
												<li><a href="javascript:void(0);"
													   class="dropdown-item btn-update-status"
													   data-id="<?= $row->id ?>"
													   data-status="close"><i class="fas fa-pause me-2"></i> Put on Close</a></li>
											</ul>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>

						<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- Closed Requests Tab -->
		<div class="tab-pane fade" id="closedRequests" role="tabpanel">
			<div class="table-container">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
						<tr>
							<th style="width: 5%;">ID</th>
							<th style="width: 15%;">Submitted To</th>
							<th style="width: 25%;">Title</th>
							<th>Description</th>
							<th style="width: 15%;">Added Date</th>
							<th style="width: 12%;">Status</th>
							<th style="width: 15%;">Actions</th>
						</tr>
						</thead>
						<tbody>
						<?php if (!empty($closeData)): ?>
							<?php foreach ($closeData as $row): ?>
								<tr>
									<td><?= $row->id ?? '' ?></td>
									<td>
										<div class="staff-name"><?= ucfirst($row->submitted_to ?? '') ?></div>
										<small class="text-muted"></small>
									</td>
									<td>
										<div class="fw-bold"><?= ucfirst($row->title ?? '') ?></div>
										<small class="text-muted"></small>
									</td>
									<td>
										<div class="fw-bold"><?= ucfirst($row->description ?? '') ?></div>
										<small class="text-muted"></small>
									</td>
									<td><?= date('Y-m-d', strtotime($row->request_date ?? '')) ?></td>
									<td>
										<?php if ($row->status == 'close'): ?>
											<span class="status-badge" style="background: #ee8d96; color: #0a0000;">close</span>
										<?php endif; ?>
									</td>
									<td>
										<div class="dropdown">
											<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
												Actions
											</button>
											<ul class="dropdown-menu dropdown-menu-end">
												<li>
													<a class="dropdown-item" href="<?= base_url('request-issue/show/' . $row->id) ?>">
														<i class="fas fa-eye me-2"></i> View
													</a>
												</li>
												<li>
													<a href="#" class="dropdown-item"
													   data-bs-toggle="modal"
													   data-bs-target="#modalComment"
													   onclick="openCommentModal(<?= $row->id ?>)">
														<i class="fas fa-edit me-2"></i> Comment
													</a>
												</li>
<!--												<li><a href="javascript:void(0);"-->
<!--													   class="dropdown-item btn-update-status"-->
<!--													   data-id="--><?php //= $row->id ?><!--"-->
<!--													   data-status="in_progress"><i class="fas fa-pause me-2"></i> Put on in progress</a></li>-->
											</ul>
										</div>
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
					<input type="hidden" name="request_id" id="commentEvalId">

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
<?php $this->load->view('includes/footer'); ?>

<script>
	$(document).ready(function() {
		// Initialize DataTables for all tables
		$('.table').DataTable({
			responsive: true,
			pageLength: 10,
			// order: [[0, 'desc']], // Order by ID descending
			columnDefs: [
				{ targets: -1, orderable: false }, // Actions column not sortable
			],
			language: {
				search: "Search Requests:",
				lengthMenu: "Show _MENU_ requests per page",
				info: "Showing _START_ to _END_ of _TOTAL_ requests",
			}
		});
		$('#description').summernote({
			height: 250,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['color', ['color']],
				['para', ['ul', 'ol']],
				['table', ['table']],
				['insert', ['link']],
				['view', ['codeview', 'help']]
			],
			placeholder: 'Enter evaluation description...'
		});
		// Handle tab switching and refresh tables
		$('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
			setTimeout(function () {
				$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
			}, 100);
		});
		$('#description').summernote({
			placeholder: 'Write your request/issue/note here...',
			height: 150
		});

		// Initialize Summernote
		$('#description').summernote({
			placeholder: 'Write your request/issue/note here...',
			height: 150
		});

		// jQuery Validation
		$('#requestForm').validate({
			ignore: '',
			rules: {
				title: {
					required: true,
					minlength: 5,
					maxlength: 100
				},
				submitted_to: {
					required: true
				},
				description: {
					summernoteRequired: true,
					summernoteMinLength: 10
				}
			},
			messages: {
				title: {
					required: "Please enter a title",
					minlength: "At least 5 characters",
					maxlength: "Max 100 characters"
				},
				submitted_to: {
					required: "Please select a recipient"
				},
				description: {
					summernoteRequired: "Please enter a description",
					summernoteMinLength: "Description must be at least 10 characters long"
				}
			},
			errorClass: 'text-danger',
			errorElement: 'small',
			highlight: function (element) {
				$(element).addClass('is-invalid');
			},
			unhighlight: function (element) {
				$(element).removeClass('is-invalid');
			},
			errorPlacement: function (error, element) {
				if (element.attr("id") === "description") {
					error.insertAfter($('.note-editor'));
				} else {
					error.insertAfter(element);
				}
			},
			submitHandler: function (form) {
				const fileInput = $('#document')[0];
				if (fileInput.files.length > 0) {
					const size = fileInput.files[0].size / 1024 / 1024;
					if (size > 10) {
						alert("File must be 10MB or less");
						return false;
					}
				}
				form.submit();
			}
		});
		$.validator.addMethod("summernoteRequired", function (value, element) {
			var content = $(element).summernote('code');
			var textContent = $('<div>').html(content).text().trim();
			return textContent.length > 0;
		});

		$.validator.addMethod("summernoteMinLength", function (value, element, minLength) {
			var content = $(element).summernote('code');
			var textContent = $('<div>').html(content).text().trim();
			return textContent.length >= minLength;
		});

		$('#assign_users').on('change', function () {
			$(this).valid();
		});

		$('#description').on('summernote.change', function () {
			$(this).valid();
		});

		// Character counter for description
		$('#description').on('input', function() {
			const currentLength = $(this).val().length;
			const maxLength = 1000;
			const remaining = maxLength - currentLength;

			// Remove existing counter
			$(this).next('.char-counter').remove();

			// Add counter
			const counterClass = remaining < 100 ? 'text-warning' : 'text-muted';
			$(this).after(`<small class="char-counter ${counterClass}">${remaining} characters remaining</small>`);
		});
	});
	function openCommentModal(id) {
		$('#commentEvalId').val(id);
		$('#commentText').val('');
		$('#modalCommentsContainer').html('');
		$('#modalCommentCount').text('0');

		// Fetch comments
		$.get("<?= base_url('request-issue/') ?>" + id, function(response) {
			if (response.comments) {
				let html = '';
				response.comments.forEach(function(comment) {
					html += `<div class="mb-2 p-2 border rounded bg-light">
                            <strong>${comment.user_name}</strong><br>
                            ${comment.comment}<br>
                            <small class="text-muted">${comment.created_at}</small>
                         </div>`;
				});
				$('#modalCommentsContainer').html(html);
				$('#modalCommentCount').text(response.comments.length);
			}
		}, 'json');
	}


	$('#commentForm').submit(function(e) {
		e.preventDefault();
		$.ajax({
			url: "<?= base_url('request-issue/commentStore') ?>",
			method: 'POST',
			data: {
				evaluation_id: $('#commentEvalId').val(),
				comment: $('#commentText').val()
			},
			success: function(res) {
				alert('Comment submitted!');
				$('#modalComment').modal('hide');
			},
			error: function() {
				alert('Error submitting comment.');
			}
		});
	});

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
			url: '<?= base_url("request-issue/update_status") ?>',
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
</script>

