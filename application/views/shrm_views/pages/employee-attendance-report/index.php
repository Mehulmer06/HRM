<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-chart-line"></i>
                Employee Attendance Report
            </h1>
            <nav class="breadcrumb-nav">
                <a href="<?= base_url('shrm/dashboard') ?>">Dashboard</a> / <span
                        class="text-muted">Attendance Report</span>
            </nav>
        </div>
    </div>
</div>

<!-- Employee Selection -->
<div class="form-card">
    <div class="form-section-title">
        <i class="fas fa-user-check"></i>
        Select Employee & Date Range
    </div>

    <div class="row">
        <div class="col-md-4">
            <label class="form-label">Employee</label>
            <select class="form-select" id="employeeSelect">
                <option value="">Select Employee</option>
                <?php if (isset($users) && !empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user->id; ?>">
                            <?php echo $user->employee_id . ' - ' . $user->name . ' - ' . $user->designation; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" class="form-control" id="fromDate">
        </div>
        <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" class="form-control" id="toDate">
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>
                Generate
            </button>
        </div>
    </div>
</div>

<!-- Employee Info Card -->
<div class="detail-card" id="employeeInfo">
    <div class="detail-header">
        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face"
             alt="Employee Photo" class="detail-photo">
        <div class="detail-info">
            <h1>Pratik Patel</h1>
            <p>Software Developer - IT Department</p>
            <p><i class="fas fa-id-card me-2"></i>Employee ID: <span>EMP001</span></p>
        </div>
    </div>
</div>

<!-- Attendance Summary -->
<div class="form-card" id="summaryCard">
    <div class="form-section-title">
        <i class="fas fa-chart-pie"></i>
        Attendance Summary
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">22</div>
                <div class="stat-label">Total Working Days</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number" style="color: #28a745;">18</div>
                <div class="stat-label">Present Days</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number" style="color: #dc3545;">2</div>
                <div class="stat-label">Absent Days</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number" style="color: #ffc107;">2</div>
                <div class="stat-label">Leave Days</div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Attendance Data Tabs -->
<div class="staff-tabs" id="attendanceData">
    <ul class="nav nav-tabs" id="attendanceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button"
                    role="tab">
                <i class="fas fa-calendar-day me-2"></i>Daily Attendance
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="leaves-tab" data-bs-toggle="tab" data-bs-target="#leaves" type="button"
                    role="tab">
                <i class="fas fa-plane me-2"></i>Leave Records
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="extra-tab" data-bs-toggle="tab" data-bs-target="#extra" type="button"
                    role="tab">
                <i class="fas fa-clock me-2"></i>Extra Working Days
            </button>
        </li>
    </ul>

    <div class="tab-content" id="attendanceTabContent">
        <!-- Daily Attendance Tab -->
        <div class="tab-pane fade show active" id="daily" role="tabpanel">
            <div class="table-container">
                <table class="table table-striped" id="dailyTable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Working Hours</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>2024-12-01</td>
                        <td>Monday</td>
                        <td>09:15 AM</td>
                        <td>06:30 PM</td>
                        <td>8.25 hrs</td>
                        <td><span class="status-badge status-active">Present</span></td>
                        <td>On Time</td>
                    </tr>
                    <tr>
                        <td>2024-12-02</td>
                        <td>Tuesday</td>
                        <td>09:30 AM</td>
                        <td>06:15 PM</td>
                        <td>7.75 hrs</td>
                        <td><span class="status-badge status-active">Present</span></td>
                        <td>Late 15 min</td>
                    </tr>
                    <tr>
                        <td>2024-12-03</td>
                        <td>Wednesday</td>
                        <td>-</td>
                        <td>-</td>
                        <td>0 hrs</td>
                        <td><span class="status-badge status-inactive">Absent</span></td>
                        <td>No Show</td>
                    </tr>
                    <tr>
                        <td>2024-12-04</td>
                        <td>Thursday</td>
                        <td>09:00 AM</td>
                        <td>06:45 PM</td>
                        <td>8.75 hrs</td>
                        <td><span class="status-badge status-active">Present</span></td>
                        <td>Overtime</td>
                    </tr>
                    <tr>
                        <td>2024-12-05</td>
                        <td>Friday</td>
                        <td>-</td>
                        <td>-</td>
                        <td>0 hrs</td>
                        <td><span class="status-badge" style="background: #fff3cd; color: #856404;">Leave</span></td>
                        <td>Sick Leave</td>
                    </tr>
                    <tr>
                        <td>2024-12-06</td>
                        <td>Saturday</td>
                        <td>10:00 AM</td>
                        <td>02:00 PM</td>
                        <td>4 hrs</td>
                        <td><span class="status-badge" style="background: #e2e3f1; color: #383d41;">Extra Day</span>
                        </td>
                        <td>Weekend Work</td>
                    </tr>
                    <tr>
                        <td>2024-12-09</td>
                        <td>Monday</td>
                        <td>09:10 AM</td>
                        <td>06:20 PM</td>
                        <td>8.17 hrs</td>
                        <td><span class="status-badge status-active">Present</span></td>
                        <td>On Time</td>
                    </tr>
                    <tr>
                        <td>2024-12-10</td>
                        <td>Tuesday</td>
                        <td>08:45 AM</td>
                        <td>06:30 PM</td>
                        <td>8.75 hrs</td>
                        <td><span class="status-badge status-active">Present</span></td>
                        <td>Early In</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Leave Records Tab -->
        <div class="tab-pane fade" id="leaves" role="tabpanel">
            <div class="table-container">
                <table class="table table-striped" id="leavesTable">
                    <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Sick Leave</td>
                        <td>2024-12-05</td>
                        <td>2024-12-05</td>
                        <td>1</td>
                        <td><span class="status-badge status-active">Approved</span></td>
                        <td>2024-12-04</td>
                        <td>Fever and cold</td>
                    </tr>
                    <tr>
                        <td>Casual Leave</td>
                        <td>2024-11-28</td>
                        <td>2024-11-29</td>
                        <td>2</td>
                        <td><span class="status-badge status-active">Approved</span></td>
                        <td>2024-11-25</td>
                        <td>Personal work</td>
                    </tr>
                    <tr>
                        <td>Earned Leave</td>
                        <td>2024-11-15</td>
                        <td>2024-11-17</td>
                        <td>3</td>
                        <td><span class="status-badge status-active">Approved</span></td>
                        <td>2024-11-10</td>
                        <td>Family function</td>
                    </tr>
                    <tr>
                        <td>Medical Leave</td>
                        <td>2024-10-22</td>
                        <td>2024-10-23</td>
                        <td>2</td>
                        <td><span class="status-badge status-active">Approved</span></td>
                        <td>2024-10-21</td>
                        <td>Medical checkup</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Extra Working Days Tab -->
        <div class="tab-pane fade" id="extra" role="tabpanel">
            <div class="table-container">
                <table class="table table-striped" id="extraTable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Working Hours</th>
                        <th>Reason</th>
                        <th>Approved By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>2024-12-06</td>
                        <td>Saturday</td>
                        <td>10:00 AM</td>
                        <td>02:00 PM</td>
                        <td>4 hrs</td>
                        <td>Project deadline</td>
                        <td>John Manager</td>
                    </tr>
                    <tr>
                        <td>2024-11-30</td>
                        <td>Saturday</td>
                        <td>09:00 AM</td>
                        <td>01:00 PM</td>
                        <td>4 hrs</td>
                        <td>Client meeting</td>
                        <td>John Manager</td>
                    </tr>
                    <tr>
                        <td>2024-11-24</td>
                        <td>Sunday</td>
                        <td>10:00 AM</td>
                        <td>03:00 PM</td>
                        <td>5 hrs</td>
                        <td>System maintenance</td>
                        <td>Tech Lead</td>
                    </tr>
                    <tr>
                        <td>2024-11-17</td>
                        <td>Sunday</td>
                        <td>09:30 AM</td>
                        <td>01:30 PM</td>
                        <td>4 hrs</td>
                        <td>Emergency bug fix</td>
                        <td>Project Manager</td>
                    </tr>
                    <tr>
                        <td>2024-10-26</td>
                        <td>Saturday</td>
                        <td>08:00 AM</td>
                        <td>12:00 PM</td>
                        <td>4 hrs</td>
                        <td>Server migration</td>
                        <td>Tech Lead</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    $(document).ready(function () {
        $('#employeeSelect').select2();

        // Set current month's start and end dates using jQuery
        function setCurrentMonthDates() {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth();

            // First day: YYYY-MM-01
            const firstDay = `${year}-${String(month + 1).padStart(2, '0')}-01`;

            // Last day: Get last date of current month
            const lastDate = new Date(year, month + 1, 0).getDate();
            const lastDay = `${year}-${String(month + 1).padStart(2, '0')}-${String(lastDate).padStart(2, '0')}`;

            $('#fromDate').val(firstDay);
            $('#toDate').val(lastDay);
        }

        // Set the dates when page loads
        setCurrentMonthDates();

        // Initialize DataTables for all tables
        $('#dailyTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']], // Sort by date descending
            columnDefs: [
                {orderable: false, targets: [5]} // Disable sorting on Status column due to HTML content
            ],
            language: {
                search: "Search attendance:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ attendance records",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        $('#leavesTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[1, 'desc']], // Sort by From Date descending
            columnDefs: [
                {orderable: false, targets: [4]} // Disable sorting on Status column due to HTML content
            ],
            language: {
                search: "Search leaves:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ leave records",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        $('#extraTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']], // Sort by date descending
            language: {
                search: "Search extra days:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ extra working day records",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    });
</script>