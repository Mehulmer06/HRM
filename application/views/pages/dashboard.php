<?php $this->load->view('includes/header'); ?>


    <h1 class="welcome-title">Welcome to IHRMS Dashboard</h1>

    <!-- Modules Grid -->
    <div class="modules-grid">
        <!-- Holiday List -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-calendar-alt icon"></i>
                    <h3>Holiday List</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('holiday') ?>" class="module-btn primary">
                    Click Here
                </a>
            </div>
        </div>

        <!-- Leave Management -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-user-clock icon"></i>
                    <h3>Leave Management</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('leave') ?>" class="module-btn success">
                    Click Here
                </a>
            </div>
        </div>

        <!-- Request/Issue/Note to Admin -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-clipboard-list icon"></i>
                    <h3>Request/Issue/Note</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="request-admin.html" class="module-btn warning">
                    Click Here
                </a>
            </div>
        </div>

        <!-- Note Management -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-sticky-note icon"></i>
                    <h3>Note Management</h3>
                </div>
                <!--<span class="module-badge">NEW</span>-->
            </div>
            <div class="module-body">
                <a href="<?= base_url('note') ?>" class="module-btn info">
                    Click Here
                </a>
            </div>
        </div>

        <!-- Project Staffs -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-project-diagram icon"></i>
                    <h3>Project Staffs</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('project-staff') ?>" class="module-btn primary">
                    Click Here
                </a>
            </div>
        </div>

        <!-- Evaluation -->
        <div class="module-card">
            <div class="module-header">
                <div class="module-info">
                    <i class="fas fa-chart-line icon"></i>
                    <h3>Evaluation</h3>
                </div>
            </div>
            <div class="module-body">
                <a href="<?= base_url('evaluation') ?>" class="module-btn secondary">
                    Click Here
                </a>
            </div>
        </div>
    </div>

    <!-- Today's Out of Office Section -->
    <div class="out-of-office-section">
        <h2 class="section-title">
            <i class="fas fa-calendar-times"></i>
            Today's Out of Office
        </h2>

        <div class="table-controls">
            <div class="show-entries">
                Show
                <select>
                    <option value="50">50</option>
                    <option value="25">25</option>
                    <option value="10">10</option>
                </select>
                entries
            </div>

            <div class="search-box">
                Search: <input type="text" class="search-input" placeholder="Search...">
            </div>
        </div>

        <div class="data-table">
            <table class="table-modern">
                <thead>
                <tr>
                    <th>Payroll No <i class="fas fa-sort"></i></th>
                    <th>Name <i class="fas fa-sort"></i></th>
                    <th>Nature <i class="fas fa-sort"></i></th>
                    <th>From Date <i class="fas fa-sort"></i></th>
                    <th>To Date <i class="fas fa-sort"></i></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5" class="no-data-message">No data available in table</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div>Showing 0 to 0 of 0 entries</div>
            <div class="pagination">
                <button disabled>Previous</button>
                <button disabled>Next</button>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer'); ?>