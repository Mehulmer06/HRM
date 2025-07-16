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

<!-- Evaluation Details -->
<div class="card shadow-sm rounded p-4 mb-4">
	<h5 class="mb-4 border-bottom pb-2">Request Details</h5>
	<div class="table-responsive">
		<table class="table table-bordered table-striped align-middle">
			<tbody>
			<tr>
				<th style="width: 30%;">Title</th>
				<td><?= htmlspecialchars($evaluation->title) ?></td>
			</tr>
			<tr>
				<th>Submitted To</th>
				<td><?= htmlspecialchars($evaluation->submitted_to) ?></td>
			</tr>
			<tr>
				<th>Request Date</th>
				<td><?= htmlspecialchars($evaluation->request_date) ?></td>
			</tr>
			<tr>
				<th>Description</th>
				<td><?= nl2br(htmlspecialchars($evaluation->description)) ?></td>
			</tr>
			<tr>
				<th>Employee Name</th>
				<td><?= htmlspecialchars($evaluation->name) ?></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>





<?php $this->load->view('includes/footer'); ?>
