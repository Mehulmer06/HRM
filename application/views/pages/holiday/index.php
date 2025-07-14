<?php $this->load->view('includes/header'); ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-calendar-alt"></i>
                Holiday List
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('dashboard') ?>">Dashboard</a> / <span class="text-muted">Holiday List</span>
            </nav>
        </div>
        <div class="year-selector">
            <label for="yearSelect" class="form-label me-2">Select Year:</label>
            <select class="form-select" id="yearSelect" style="width: auto; display: inline-block;">
                <?php for ($y = 2022; $y <= 2026; $y++): ?>
                    <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
</div>

<!-- Holiday Lists Container -->
<div class="row">
    <!-- Restricted Holidays List -->
    <div class="col-lg-6">
        <div class="holiday-section">
            <div class="holiday-header">
                <h3><i class="fas fa-ban me-2"></i>Restricted Holidays List</h3>
            </div>

            <div class="simple-table-container">
                <table class="table table-striped table-hover" id="restricted-holidays">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Date (Day)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($restricted_holidays)): ?>
                        <?php foreach ($restricted_holidays as $index => $holiday): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $holiday->holiday_list_name ?></td>
                                <td><?= date('d/m/Y', strtotime($holiday->holiday_list_date)) ?>
                                    (<?= $holiday->holiday_list_day ?>)
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Public Holidays List -->
    <div class="col-lg-6">
        <div class="holiday-section">
            <div class="holiday-header">
                <h3><i class="fas fa-calendar-check me-2"></i>Public Holidays List</h3>
            </div>

            <div class="simple-table-container">
                <table class="table table-striped table-hover" id="public-holidays">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Date (Day)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($public_holidays)): ?>
                        <?php foreach ($public_holidays as $index => $holiday): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $holiday->holiday_list_name ?></td>
                                <td><?= date('d/m/Y', strtotime($holiday->holiday_list_date)) ?>
                                    (<?= $holiday->holiday_list_day ?>)
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

<!-- Year Redirect Script -->
<script>
    document.getElementById('yearSelect').addEventListener('change', function () {
        const selectedYear = this.value;
        const baseUrl = "<?= base_url('holiday') ?>";
        window.location.href = baseUrl + '/' + selectedYear;
    });
</script>


<?php $this->load->view('includes/footer'); ?>

<script>
    // Initialize DataTables
    $(document).ready(function () {
        $('#restricted-holidays').DataTable({
            info: true,
            paging: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false, // Disable auto width to allow manual control
            columnDefs: [
                {width: '10%', targets: 0}, // No
                {width: '50%', targets: 1}, // Name
                {width: '40%', targets: 2}  // Date (Day)
            ]
        });

        $('#public-holidays').DataTable({
            info: true,
            paging: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            columnDefs: [
                {width: '10%', targets: 0}, // No
                {width: '50%', targets: 1}, // Name
                {width: '40%', targets: 2}  // Date (Day)
            ]
        });
    });
</script>