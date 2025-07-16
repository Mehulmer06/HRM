<?php $this->load->view('includes/header'); ?>

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

    <!-- Finance Form Section -->
    <div class="form-card">
        <div class="form-section-title">
            <i class="fas fa-upload"></i>
            Upload Salary Slip
        </div>

        <form id="salarySlipForm" enctype="multipart/form-data">
            <div class="row form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <select class="form-select" id="username" name="username" required>
                            <option value="">Select Username</option>
                            <option value="john.doe">john.doe</option>
                            <option value="jane.smith">jane.smith</option>
                            <option value="mike.johnson">mike.johnson</option>
                            <option value="sarah.wilson">sarah.wilson</option>
                            <option value="david.brown">david.brown</option>
                            <option value="lisa.davis">lisa.davis</option>
                            <option value="robert.taylor">robert.taylor</option>
                            <option value="emily.clark">emily.clark</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="month">Month</label>
                        <select class="form-select" id="month" name="month" required>
                            <option value="">Select Month</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="salarySlip">Upload Salary Slip</label>
                        <input type="file" id="salarySlip" name="salary_slip" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">PDF, JPG, PNG files only (Max 5MB)</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn-primary me-2">
                        <i class="fas fa-save me-1"></i>
                        Upload Salary Slip
                    </button>
                    <button type="reset" class="btn-secondary">
                        <i class="fas fa-undo me-1"></i>
                        Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Salary Slips Table Section -->
    <div class="out-of-office-section">
        <h2 class="section-title">
            <i class="fas fa-file-invoice-dollar"></i>
            Salary Slips History
        </h2>

        <div class="table-controls">
            <div class="show-entries">
                Show
                <select id="entriesPerPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                entries
            </div>

            <div class="search-box">
                Search: <input type="text" class="search-input" id="searchInput" placeholder="Search...">
            </div>
        </div>

        <div class="data-table">
            <table class="table-modern" id="salarySlipsTable">
                <thead>
                <tr>
                    <th>S.No <i class="fas fa-sort"></i></th>
                    <th>Username <i class="fas fa-sort"></i></th>
                    <th>Month <i class="fas fa-sort"></i></th>
                    <th>Upload Date <i class="fas fa-sort"></i></th>
                    <th>File <i class="fas fa-sort"></i></th>
                    <th>Actions <i class="fas fa-sort"></i></th>
                </tr>
                </thead>
                <tbody id="salarySlipsTableBody">
                <!-- Sample data - replace with dynamic data from database -->
                <tr>
                    <td>1</td>
                    <td>john.doe</td>
                    <td>July</td>
                    <td>16/07/2025</td>
                    <td>
                    <span class="file-badge">
                        <i class="fas fa-file-pdf"></i>
                        salary_slip_july_2025.pdf
                    </span>
                    </td>
                    <td>
                        <button class="action-btn btn-view" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn btn-edit" title="Download">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="action-btn btn-toggle" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>jane.smith</td>
                    <td>June</td>
                    <td>15/06/2025</td>
                    <td>
                    <span class="file-badge">
                        <i class="fas fa-file-image"></i>
                        salary_slip_june_2025.jpg
                    </span>
                    </td>
                    <td>
                        <button class="action-btn btn-view" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn btn-edit" title="Download">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="action-btn btn-toggle" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div id="tableInfo">Showing 1 to 2 of 2 entries</div>
            <div class="pagination">
                <button id="prevBtn" disabled>Previous</button>
                <button id="nextBtn" disabled>Next</button>
            </div>
        </div>
    </div>



<?php $this->load->view('includes/footer'); ?>