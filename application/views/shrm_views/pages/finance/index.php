<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php'); ?>
<style>
    .is-invalid {
        border-color: #dc3545;
    }
</style>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-indian-rupee-sign"></i>
                Finance Management
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> / <span class="text-muted">Finance</span>
            </nav>
        </div>
    </div>
</div>

<?php
// Get session data
$role = $this->session->userdata('role');
$category = $this->session->userdata('category');
$userId = $this->session->userdata('user_id');

// Apply conditional filtering
if (
    (
        ($role === 'e' && $category === 'e') ||
        ($role === 'admin') ||
        ($role === 'employee' && $category === 'admin')
    )
) {
    ?>
    <!-- Finance Tabs Section -->
    <div class="staff-tabs">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="financeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="salary-slip-tab" data-bs-toggle="tab" data-bs-target="#salary-slip"
                        type="button" role="tab" aria-controls="salary-slip" aria-selected="true">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Salary Slip
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="other-document-tab" data-bs-toggle="tab" data-bs-target="#other-document"
                        type="button" role="tab" aria-controls="other-document" aria-selected="false">
                    <i class="fas fa-file-alt me-2"></i>
                    Other Document
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="financeTabContent">
            <!-- Salary Slip Tab -->
            <div class="tab-pane fade show active" id="salary-slip" role="tabpanel" aria-labelledby="salary-slip-tab">
                <div class="form-section-title">
                    <i class="fas fa-upload"></i>
                    Upload Salary Slip
                </div>

                <form action="<?= base_url('shrm/finance/store') ?>" method="post" enctype="multipart/form-data"
                      id="salarySlipForm">
                    <?php
                    $csrf = array(
                        'name' => $this->security->get_csrf_token_name(),
                        'hash' => $this->security->get_csrf_hash()
                    );
                    ?>
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
                    <input type="hidden" name="document_type" value="salary_slip"/>

                    <div class="row form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="username">Username</label>
                                <select class="form-select <?= form_error('username') ? 'is-invalid' : '' ?>"
                                        name="username"
                                        id="username">
                                    <option value="">Select employee</option>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $employee): ?>
                                            <option value="<?= $employee->id ?>"><?= $employee->name ?>
                                                - <?= $employee->designation ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?= form_error('username', '<div class="invalid-feedback">', '</div>') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="month">Month</label>
                                <?php
                                $yearShort = date('y');
                                $months = [
                                    '01' => 'January',
                                    '02' => 'February',
                                    '03' => 'March',
                                    '04' => 'April',
                                    '05' => 'May',
                                    '06' => 'June',
                                    '07' => 'July',
                                    '08' => 'August',
                                    '09' => 'September',
                                    '10' => 'October',
                                    '11' => 'November',
                                    '12' => 'December'
                                ];
                                ?>
                                <select class="form-select <?= form_error('month') ? 'is-invalid' : '' ?>" name="month"
                                        id="month">
                                    <option value="">Select Month</option>
                                    <?php foreach ($months as $key => $month): ?>
                                        <option value="<?= $key . '' . $yearShort ?>"><?= $month . "'" . $yearShort ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('month', '<div class="invalid-feedback">', '</div>') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="document">Upload Salary Slip</label>
                                <input type="file" name="document" id="document"
                                       class="form-control <?= form_error('document') ? 'is-invalid' : '' ?>"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">PDF, JPG, PNG files only (Max 5MB)</small>
                                <?= form_error('document', '<div class="invalid-feedback">', '</div>') ?>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-1"></i> Upload Salary Slip
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Other Document Tab -->
            <div class="tab-pane fade" id="other-document" role="tabpanel" aria-labelledby="other-document-tab">
                <div class="form-section-title">
                    <i class="fas fa-upload"></i>
                    Upload Other Document
                </div>

                <form action="<?= base_url('shrm/finance/store_other_document') ?>" method="post"
                      enctype="multipart/form-data"
                      id="otherDocumentForm">
                    <?php
                    $csrf = array(
                        'name' => $this->security->get_csrf_token_name(),
                        'hash' => $this->security->get_csrf_hash()
                    );
                    ?>
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
                    <input type="hidden" name="document_type" value="other_document"/>

                    <div class="row form-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="other_username">Username</label>
                                <select class="form-select <?= form_error('other_username') ? 'is-invalid' : '' ?>"
                                        name="other_username"
                                        id="other_username">
                                    <option value="">Select employee</option>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $employee): ?>
                                            <option value="<?= $employee->id ?>"><?= $employee->name ?>
                                                - <?= $employee->designation ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?= form_error('other_username', '<div class="invalid-feedback">', '</div>') ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="document_title">Document Title</label>
                                <input type="text" name="document_title" id="document_title"
                                       class="form-control <?= form_error('document_title') ? 'is-invalid' : '' ?>"
                                       placeholder="Enter document title">
                                <?= form_error('document_title', '<div class="invalid-feedback">', '</div>') ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="document_date">Document Date</label>
                                <input type="date" name="document_date" id="document_date"
                                       class="form-control <?= form_error('document_date') ? 'is-invalid' : '' ?>">
                                <?= form_error('document_date', '<div class="invalid-feedback">', '</div>') ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="other_document">Upload Document</label>
                                <input type="file" name="other_document" id="other_document"
                                       class="form-control <?= form_error('other_document') ? 'is-invalid' : '' ?>"
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="form-text text-muted">PDF, JPG, PNG, DOC, DOCX files only (Max
                                    5MB)</small>
                                <?= form_error('other_document', '<div class="invalid-feedback">', '</div>') ?>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-1"></i> Upload Document
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<!-- Document History Tabs Section -->
<div class="staff-tabs mt-4">
    <!-- Navigation Tabs for Listing -->
    <ul class="nav nav-tabs" id="documentListTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="salary-slip-list-tab" data-bs-toggle="tab"
                    data-bs-target="#salary-slip-list"
                    type="button" role="tab" aria-controls="salary-slip-list" aria-selected="true">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                Salary Slips History
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="other-document-list-tab" data-bs-toggle="tab"
                    data-bs-target="#other-document-list"
                    type="button" role="tab" aria-controls="other-document-list" aria-selected="false">
                <i class="fas fa-file-alt me-2"></i>
                Other Documents History
            </button>
        </li>
    </ul>

    <!-- Tab Content for Listing -->
    <div class="tab-content" id="documentListTabContent">
        <!-- Salary Slips History Tab -->
        <div class="tab-pane fade show active" id="salary-slip-list" role="tabpanel"
             aria-labelledby="salary-slip-list-tab">
            <div class="table-container">
                <table id="finance" class="table-modern table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Username</th>
                        <th>Month(Year)</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($finances) && !empty($finances)): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($finances as $finance): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($finance['username']); ?></td>
                                <td>
                                    <?php
                                    $monthYear = $finance['month_year'];
                                    $formattedMonthYear = substr($monthYear, 0, 2) . '/' . '20' . substr($monthYear, 2, 2);
                                    echo htmlspecialchars($formattedMonthYear);
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('upload/salary_slip/' . urlencode($finance['salary_slip'])); ?>"
                                       download
                                       class="action-btn btn-edit"
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Other Documents History Tab -->
        <div class="tab-pane fade" id="other-document-list" role="tabpanel" aria-labelledby="other-document-list-tab">
            <div class="table-container">
                <table id="otherDocuments" class="table-modern table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Username</th>
                        <th>Document Title</th>
                        <th>Upload Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($other_documents) && !empty($other_documents)): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($other_documents as $document): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($document['username']); ?></td>
                                <td><?= htmlspecialchars($document['document_title']); ?></td>
                                <td><?= date('d/m/Y', strtotime($document['upload_date'])); ?></td>
                                <td>
                                    <a href="<?= base_url('upload/other_documents/' . urlencode($document['document_file'])); ?>"
                                       download
                                       class="action-btn btn-edit"
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="action-btn btn-view"
                                            onclick="viewDocument('<?= htmlspecialchars($document['document_title']); ?>', '<?= date('d/m/Y', strtotime($document['upload_date'])); ?>')"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
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


<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    $(document).ready(function () {
        // Custom validator for file size (5MB max)
        $.validator.addMethod("filesize", function (value, element, param) {
            if (element.files.length === 0) return true; // allow if no file selected
            return element.files[0].size <= param;
        }, "File is too large.");

        // Salary Slip Form Validation (unchanged)
        $("#salarySlipForm").validate({
            rules: {
                username: {
                    required: true
                },
                month: {
                    required: true
                },
                document: {
                    required: true,
                    filesize: 5242880 // 5MB
                }
            },
            messages: {
                username: {
                    required: "Please select an employee."
                },
                month: {
                    required: "Please select a month."
                },
                document: {
                    required: "Please upload a salary slip.",
                    filesize: "File must be less than 5MB."
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger small d-block mt-1',
            highlight: function (element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                const $submitBtn = $('button[type="submit"]');
                $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Uploading...').prop('disabled', true);
                form.submit();
            }
        });

        // Other Document Form Validation
        $("#otherDocumentForm").validate({
            rules: {
                other_username: {
                    required: true
                },
                document_title: {
                    required: true,
                    minlength: 3
                },
                document_date: {
                    required: true
                },
                other_document: {
                    required: true,
                    filesize: 5242880 // 5MB
                }
            },
            messages: {
                other_username: {
                    required: "Please select an employee."
                },
                document_title: {
                    required: "Please enter document title.",
                    minlength: "Document title must be at least 3 characters long."
                },
                document_date: {
                    required: "Please select document date."
                },
                other_document: {
                    required: "Please upload a document.",
                    filesize: "File must be less than 5MB."
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger small d-block mt-1',
            highlight: function (element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                const $submitBtn = $(form).find('button[type="submit"]');
                $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Uploading...').prop('disabled', true);
                form.submit();
            }
        });

        // DataTables initialization
        $('#finance').DataTable({
            "processing": true,
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "pageLength": 10,
            "language": {
                "search": "Search salary slips:",
            },
        });

        $('#otherDocuments').DataTable({
            "processing": true,
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "pageLength": 10,
            "language": {
                "search": "Search documents:",
            },
        });
    });
</script>