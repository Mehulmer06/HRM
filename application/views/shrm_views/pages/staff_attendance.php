<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php');
?>
<style>
    /* Minimal custom styles - mostly calendar-specific */
    .calendar-table {
        font-size: 11px;
    }

    .weekday-header {
        background: #2c5aa0;
        color: white;
        font-weight: 600;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .month-cell {
        background: #e6fffa;
        color: #234e52;
        font-weight: 600;
        min-width: 110px;
        max-width: 110px;
        font-size: 12px;
    }

    .day-cell {
        width: 26px;
        height: 26px;
        font-size: 10px;
        font-weight: 500;
        cursor: pointer;
    }

    .day-present { background: #c6f6d5; color: #22543d; }
    .day-cl { background: #fed7d7; color: #c53030; }
    .day-hd { background: #feebc8; color: #c05621; }
    .day-lwp { background: #e9d8fd; color: #553c9a; }
    .day-weekend { background: #f5f5f5; color: #666; }
    .day-today { background: #fed7d7; color: #c53030; border: 2px solid #f56565; }
    .day-future { background: #f7fafc; color: #a0aec0; opacity: 0.6; }

    @media (max-width: 1200px) {
        .day-cell { width: 22px; height: 22px; font-size: 9px; }
        .month-cell { min-width: 90px; max-width: 90px; font-size: 11px; }
    }

    @media (max-width: 768px) {
        .day-cell { width: 18px; height: 18px; font-size: 8px; }
        .month-cell { min-width: 70px; max-width: 70px; font-size: 10px; }
        .calendar-table { font-size: 9px; }
    }
</style>
<!-- Page Header - using existing styles -->
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="fas fa-calendar-alt"></i>
                Staff Attendance Calendar
            </h1>
            <nav class="breadcrumb-nav">
                <a href="dashboard.html">Dashboard</a> /
                <span class="text-muted">Attendance</span>
            </nav>
        </div>
        <div class="d-flex gap-3 align-items-center flex-wrap">
            <div class="year-selector">
                <label class="form-label mb-0">Year:</label>
                <select class="form-select" id="yearSelect">
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025" selected>2025</option>
                    <option value="2026">2026</option>
                </select>
            </div>
            <button class="btn btn-primary" onclick="printCalendar()">
                <i class="fas fa-print me-2"></i>Print
            </button>
        </div>
    </div>
</div>

<!-- Employee Selection -->
<div class="form-card">
    <div class="row align-items-end g-3">
        <div class="col-md-8">
            <label class="form-label">Select Employee:</label>
            <select class="form-select" id="employeeSelect">
                <option value="170509">170509_F - John Anderson</option>
                <option value="170510">170510_F - Sarah Wilson</option>
                <option value="170511">170511_M - Michael Brown</option>
                <option value="170512">170512_F - Emily Davis</option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-success w-100" onclick="loadAttendance()">
                <i class="fas fa-search me-2"></i>Load Attendance
            </button>
        </div>
    </div>
</div>

<!-- Attendance Calendar -->
<div class="card shadow">
    <!-- Calendar Header -->
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h5 class="mb-0">Employee: <span id="currentEmployee" class="text-primary">170509_F</span></h5>
                <small class="text-muted">Year: <span id="selectedYear">2025</span></small>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 text-center rounded border" style="background: #fed7d7; color: #c53030;">
                    <div class="small text-uppercase">Days on Leave</div>
                    <div class="h4 mb-0" id="totalLeave">0</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 text-center rounded border" style="background: #c6f6d5; color: #22543d;">
                    <div class="small text-uppercase">Working Days</div>
                    <div class="h4 mb-0" id="workingDays">249</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 text-center rounded border" style="background: #feebc8; color: #c05621;">
                    <div class="small text-uppercase">Leave Balance</div>
                    <div class="h4 mb-0" id="leaveBalance">#N/A</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Body -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 calendar-table">
                <thead>
                <tr>
                    <th class="weekday-header text-center p-2">Weekday<br>Month</th>
                    <th class="weekday-header text-center p-1">Sun</th>
                    <th class="weekday-header text-center p-1">Mon</th>
                    <th class="weekday-header text-center p-1">Tue</th>
                    <th class="weekday-header text-center p-1">Wed</th>
                    <th class="weekday-header text-center p-1">Thu</th>
                    <th class="weekday-header text-center p-1">Fri</th>
                    <th class="weekday-header text-center p-1">Sat</th>
                    <th class="weekday-header text-center p-1">Sun</th>
                    <th class="weekday-header text-center p-1">Mon</th>
                    <th class="weekday-header text-center p-1">Tue</th>
                    <th class="weekday-header text-center p-1">Wed</th>
                    <th class="weekday-header text-center p-1">Thu</th>
                    <th class="weekday-header text-center p-1">Fri</th>
                    <th class="weekday-header text-center p-1">Sat</th>
                    <th class="weekday-header text-center p-1">Sun</th>
                    <th class="weekday-header text-center p-1">Mon</th>
                    <th class="weekday-header text-center p-1">Tue</th>
                    <th class="weekday-header text-center p-1">Wed</th>
                    <th class="weekday-header text-center p-1">Thu</th>
                    <th class="weekday-header text-center p-1">Fri</th>
                    <th class="weekday-header text-center p-1">Sat</th>
                    <th class="weekday-header text-center p-1">Sun</th>
                    <th class="weekday-header text-center p-1">Mon</th>
                    <th class="weekday-header text-center p-1">Tue</th>
                    <th class="weekday-header text-center p-1">Wed</th>
                    <th class="weekday-header text-center p-1">Thu</th>
                    <th class="weekday-header text-center p-1">Fri</th>
                    <th class="weekday-header text-center p-1">Sat</th>
                    <th class="weekday-header text-center p-1">Sun</th>
                    <th class="weekday-header text-center p-1">Mon</th>
                </tr>
                </thead>
                <tbody id="calendarBody">
                <!-- Calendar will be generated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="form-card mt-5">
    <h5 class="form-section-title">
        <i class="fas fa-info-circle"></i>
        Legend
    </h5>
    <div class="row g-2">
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                <div class="day-present rounded border" style="width: 16px; height: 16px; background: #c6f6d5;"></div>
                <small>Present/Working Day</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                <div class="day-cl rounded border" style="width: 16px; height: 16px; background: #fed7d7;"></div>
                <small>Casual Leave (CL)</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                <div class="day-hd rounded border" style="width: 16px; height: 16px; background: #feebc8;"></div>
                <small>Holiday (HD)</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                <div class="day-lwp rounded border" style="width: 16px; height: 16px; background: #e9d8fd;"></div>
                <small>Leave Without Pay (LWP)</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                <div class="day-weekend rounded border" style="width: 16px; height: 16px; background: #f5f5f5;"></div>
                <small>Weekend</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                <div class="day-today rounded border" style="width: 16px; height: 16px; background: #fed7d7; border: 2px solid #f56565 !important;"></div>
                <small>Today</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                <div class="day-future rounded border" style="width: 16px; height: 16px; background: #f7fafc; opacity: 0.6;"></div>
                <small>Future Date</small>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>

<script>
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    document.addEventListener('DOMContentLoaded', function() {
        generateCalendar();

        // Year change handler
        document.getElementById('yearSelect').addEventListener('change', function() {
            document.getElementById('selectedYear').textContent = this.value;
            generateCalendar();
        });
    });

    function generateCalendar() {
        const year = parseInt(document.getElementById('yearSelect').value);
        const calendarBody = document.getElementById('calendarBody');
        const today = new Date();

        // Clear existing calendar
        calendarBody.innerHTML = '';

        let totalLeave = 0;
        let workingDays = 0;

        // Generate each month as a row
        monthNames.forEach((monthName, monthIndex) => {
            const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();

            // Create month row
            const monthRow = document.createElement('tr');

            // Month name cell with leave counters
            const monthCell = document.createElement('td');
            monthCell.className = 'month-cell text-center align-top p-2 border';

            // Calculate leave counts for this month
            const monthCL = Math.floor(Math.random() * 3);
            const monthHD = Math.floor(Math.random() * 4);
            const monthLWP = Math.floor(Math.random() * 1);

            monthCell.innerHTML = `
                    <div class="fw-bold mb-2">${monthName}</div>
                    <div class="small text-start">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-danger">CL:</span>
                            <span class="fw-semibold">${monthCL}</span>
                        </div>
                        <div class="border-top pt-1">
                            <div class="d-flex justify-content-between">
                                <span class="text-success">Extra:</span>
                                <span class="fw-semibold">0</span>
                            </div>
                        </div>
                    </div>
                `;
            monthRow.appendChild(monthCell);

            // Generate 31 day cells
            for (let dayNum = 1; dayNum <= 31; dayNum++) {
                const dayCell = document.createElement('td');
                dayCell.className = 'day-cell text-center align-middle p-1 border';

                if (dayNum <= daysInMonth) {
                    dayCell.textContent = dayNum;

                    const currentDate = new Date(year, monthIndex, dayNum);
                    const dayOfWeek = currentDate.getDay();

                    // Check if it's today
                    if (currentDate.toDateString() === today.toDateString()) {
                        dayCell.classList.add('day-today');
                        dayCell.title = `Today - ${currentDate.toLocaleDateString()}`;
                    }
                    // Check if it's a future date
                    else if (currentDate > today) {
                        dayCell.classList.add('day-future');
                        dayCell.title = `Future Date - ${currentDate.toLocaleDateString()}`;
                    }
                    // Check if it's weekend
                    else if (dayOfWeek === 0 || dayOfWeek === 6) {
                        dayCell.classList.add('day-weekend');
                        dayCell.title = `Weekend - ${currentDate.toLocaleDateString()}`;
                    }
                    // Simulate attendance data
                    else {
                        const random = Math.random();
                        if (random < 0.05) {
                            dayCell.classList.add('day-cl');
                            dayCell.title = `Casual Leave - ${currentDate.toLocaleDateString()}`;
                            totalLeave++;
                        } else if (random < 0.08) {
                            dayCell.classList.add('day-hd');
                            dayCell.title = `Holiday - ${currentDate.toLocaleDateString()}`;
                        } else if (random < 0.09) {
                            dayCell.classList.add('day-lwp');
                            dayCell.title = `Leave Without Pay - ${currentDate.toLocaleDateString()}`;
                            totalLeave++;
                        } else {
                            dayCell.classList.add('day-present');
                            dayCell.title = `Present - ${currentDate.toLocaleDateString()}`;
                            workingDays++;
                        }
                    }

                    // Add click event
                    dayCell.addEventListener('click', function() {
                        if (!this.classList.contains('day-future')) {
                            showDayDetails(currentDate, this.className);
                        }
                    });

                } else {
                    // Empty cell
                    dayCell.style.background = 'transparent';
                    dayCell.style.border = 'none';
                    dayCell.style.cursor = 'default';
                }

                monthRow.appendChild(dayCell);
            }

            calendarBody.appendChild(monthRow);
        });

        // Update statistics
        document.getElementById('totalLeave').textContent = totalLeave;
        document.getElementById('workingDays').textContent = workingDays;

        // Calculate leave balance
        const totalAllowedLeave = 21;
        const leaveBalance = totalAllowedLeave - totalLeave;
        document.getElementById('leaveBalance').textContent = leaveBalance >= 0 ? leaveBalance : 0;
    }

    function showDayDetails(date, dayClass) {
        const dayTypes = {
            'day-present': 'Present',
            'day-cl': 'Casual Leave',
            'day-hd': 'Holiday',
            'day-lwp': 'Leave Without Pay',
            'day-weekend': 'Weekend',
            'day-today': 'Today'
        };

        const dayType = Object.keys(dayTypes).find(key => dayClass.includes(key));
        const dayName = dayTypes[dayType] || 'Unknown';

        // Simple notification using Bootstrap alert
        showNotification(`${date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })} - Status: ${dayName}`, 'info');
    }

    function loadAttendance() {
        const employeeSelect = document.getElementById('employeeSelect');
        const selectedEmployee = employeeSelect.value + '_' + (Math.random() > 0.5 ? 'F' : 'M');

        // Update employee info
        document.getElementById('currentEmployee').textContent = selectedEmployee;

        // Regenerate calendar
        generateCalendar();

        // Show success notification
        showNotification('Attendance data loaded successfully!', 'success');
    }

    function printCalendar() {
        window.print();
    }

    // Bootstrap-based notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
</script>