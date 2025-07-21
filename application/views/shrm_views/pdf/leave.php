<?php
// Helper function to format date
function formatDate($date)
{
    return date('jS F Y', strtotime($date));
}

// Helper function to get leave type breakdown
function getLeaveBreakdown($days)
{
    $breakdown = [];
    foreach ($days as $day) {
        if (!isset($breakdown[$day->leave_type])) {
            $breakdown[$day->leave_type] = 0;
        }
        $breakdown[$day->leave_type]++;
    }
    return $breakdown;
}

// Get leave breakdown
$leaveBreakdown = getLeaveBreakdown($days);
$breakdownText = [];
foreach ($leaveBreakdown as $type => $count) {
    $breakdownText[] = "{$count} day" . ($count > 1 ? 's' : '') . " ({$type})";
}

// Format dates
$startDate = formatDate($leave->start_date);
$endDate = formatDate($leave->end_date);
$applicationDate = formatDate($leave->created_at);
$approvalDate = !empty($leave->action_at) ? formatDate($leave->action_at) : '';

// Determine leave period text
$totalDays = (int)$leave->total_days;
if ($leave->start_date === $leave->end_date) {
    $leavePeriod = $startDate;
} else {
    $leavePeriod = $startDate . ' to ' . $endDate;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casual Leave Application Form - <?php echo htmlspecialchars($leave->name); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #000;
            background: white;
            padding: 20px;
        }

        .form-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .certification {
            font-size: 14px;
            margin-bottom: 12px;
        }

        .address {
            font-size: 12px;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .contact-info {
            font-size: 12px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }

        .form-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 25px 0;
            text-decoration: underline;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .date-section {
            display: table-cell;
            width: 40%;
            vertical-align: bottom;
        }

        .signature-section-right {
            display: table-cell;
            width: 60%;
            text-align: right;
            vertical-align: bottom;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            height: 35px;
            margin-left: auto;
            margin-bottom: 5px;
        }

        .approval-section {
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 20px;
        }

        .approval-field {
            margin-bottom: 40px;
        }

        .approval-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .approval-line {
            border-bottom: 1px solid #000;
            height: 35px;
        }

        /* PDF Generation Optimizations */
        @page {
            margin: 1in;
            size: A4;
        }

        @media print {
            body {
                padding: 0;
                font-size: 12px;
            }

            .form-container {
                max-width: none;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="form-container">
    <div class="header">
        <div class="company-name">VISWAMBI SECURITY AGENCY PVT. LTD.</div>
        <div class="certification">(An ISO 9001-2008 Certified Agency)</div>
        <div class="address">
            Address: 406, M V House, Opposite Hathising Wadi, Near Swami Narayan<br>
            Chowk, Shahibagh, Ahmedabad, Gujarat-380004
        </div>
        <div class="contact-info">
            <strong>Tele:</strong> (O) 9033050557, <strong>Fax:</strong> 079-25268947
            <strong>*Email:</strong> viswambi@hotmail.com
        </div>
    </div>

    <div class="form-title">CASUAL LEAVE APPLICATION FORM</div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #000;">
        <tr>
            <td style="border: 1px solid #000; padding: 8px 10px; width: 35%; vertical-align: top; font-weight: 500;">
                1. Name of the applicant:
            </td>
            <td style="border: 1px solid #000; padding: 8px 10px; vertical-align: top; font-weight: 600;">
                <?php echo htmlspecialchars($leave->name); ?>
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 8px 10px; width: 35%; vertical-align: top; font-weight: 500;">
                2. Designation:
            </td>
            <td style="border: 1px solid #000; padding: 8px 10px; vertical-align: top;">
                <?php echo !empty($contract->designation) ? htmlspecialchars($contract->designation) : '_______________________________'; ?>
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 8px 10px; width: 35%; vertical-align: top; font-weight: 500;">
                3. Project Name & Department:
            </td>
            <td style="border: 1px solid #000; padding: 8px 10px; vertical-align: top;">
                <?php echo !empty($contract->project_name) ? htmlspecialchars($contract->project_name) : '_______________________________'; ?>
                - <?php echo !empty($leave->department) ? htmlspecialchars($leave->department) : '_______________________________'; ?>
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 8px 10px; width: 35%; vertical-align: top; font-weight: 500;">
                4. No. of days of leave and Date/period:
            </td>
            <td style="border: 1px solid #000; padding: 8px 10px; vertical-align: top;">
                <strong><?php echo $totalDays; ?> day<?php echo $totalDays > 1 ? 's' : ''; ?></strong>
                (<?php echo $leavePeriod; ?>)

                <div style="margin-top: 6px; font-size: 11px; line-height: 1.3;">
                    <strong>Leave Details:</strong><br>
                    <div style="display: table; width: 100%; margin-top: 3px;">
                        <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 8px;">
                            <?php
                            $totalDaysCount = count($days);
                            $halfPoint = ceil($totalDaysCount / 2);
                            $leftSideDays = array_slice($days, 0, $halfPoint);
                            ?>
                            <?php foreach ($leftSideDays as $index => $day): ?>
                                <div style="margin-bottom: 2px; padding: 1px 0; font-size: 10px;">
                                    <span style="font-weight: 500; color: #2c5aa0;">
                                        <?php echo date('d/m/Y', strtotime($day->leave_date)); ?>
                                    </span>
                                    -
                                    <span style="font-weight: 500; color: <?php echo $day->leave_type == 'CL' ? '#27ae60' : ($day->leave_type == 'Paid' ? '#3498db' : '#e67e22'); ?>;">
                                        <?php echo $day->leave_type; ?>
                                    </span>
                                    <?php if (!empty($day->source_reference) && $day->leave_type == 'CL'): ?>
                                        (<?php echo date('M Y', strtotime($day->source_reference)); ?>)
                                    <?php endif; ?>
                                    <?php if ($day->day_type != 'full'): ?>
                                        - <em><?php echo ucfirst($day->day_type); ?></em>
                                    <?php endif; ?>
                                    <?php if (!empty($day->half_type) && $day->day_type == 'half'): ?>
                                        (<?php echo ucfirst($day->half_type); ?>)
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 8px;">
                            <?php
                            $rightSideDays = array_slice($days, $halfPoint);
                            ?>
                            <?php foreach ($rightSideDays as $index => $day): ?>
                                <div style="margin-bottom: 2px; padding: 1px 0; font-size: 10px;">
                                    <span style="font-weight: 500; color: #2c5aa0;">
                                        <?php echo date('d/m/Y', strtotime($day->leave_date)); ?>
                                    </span>
                                    -
                                    <span style="font-weight: 500; color: <?php echo $day->leave_type == 'CL' ? '#27ae60' : ($day->leave_type == 'Paid' ? '#3498db' : '#e67e22'); ?>;">
                                        <?php echo $day->leave_type; ?>
                                    </span>
                                    <?php if (!empty($day->source_reference) && $day->leave_type == 'CL'): ?>
                                        (<?php echo date('M Y', strtotime($day->source_reference)); ?>)
                                    <?php endif; ?>
                                    <?php if ($day->day_type != 'full'): ?>
                                        - <em><?php echo ucfirst($day->day_type); ?></em>
                                    <?php endif; ?>
                                    <?php if (!empty($day->half_type) && $day->day_type == 'half'): ?>
                                        (<?php echo ucfirst($day->half_type); ?>)
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($breakdownText)): ?>
                    <div style="margin-top: 4px; padding-top: 3px; border-top: 1px solid #eee; font-size: 10px; color: #666;">
                        <strong>Summary:</strong> <?php echo implode(', ', $breakdownText); ?>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 8px 10px; width: 35%; vertical-align: top; font-weight: 500;">
                5. Reason for leave:
            </td>
            <td style="border: 1px solid #000; padding: 8px 10px; vertical-align: top; line-height: 1.4; min-height: 60px;">
                <?php echo nl2br(htmlspecialchars($leave->reason)); ?>

            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 8px 10px; width: 35%; vertical-align: top; font-weight: 500;">
                6. Address during leave period:
            </td>
            <td style="border: 1px solid #000; padding: 8px 10px; vertical-align: top; line-height: 1.4; min-height: 60px;">
                <?php echo nl2br(htmlspecialchars($leave->address)); ?>
            </td>
        </tr>
        <?php if (!empty($leave->edit_reason)): ?>
            <tr>
                <td style="border: 1px solid #000; padding: 8px 10px; width: 35%; vertical-align: top; font-weight: 500;">
                    7. Edit Reason:
                </td>
                <td style="border: 1px solid #000; padding: 8px 10px; vertical-align: top; color: #e74c3c; line-height: 1.4;">
                    <?php echo nl2br(htmlspecialchars($leave->edit_reason)); ?>
                </td>
            </tr>
        <?php endif; ?>
    </table>

    <div style="margin-top: 40px;">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 30%; vertical-align: bottom;">
                <strong>Date:</strong> <?php echo $applicationDate; ?>
            </div>
            <div style="display: table-cell; width: 70%; text-align: right; vertical-align: bottom;">
                <div style="border-bottom: 1px solid #000; height: 30px; width: 250px; margin-left: auto; margin-bottom: 5px; position: relative;">
                    <?php if ( ! empty($leave->signature) ): ?>
                        <img
                                src="<?= base_url('uploads/signatures/' . $leave->signature) ?>"
                                alt="Signature"
                                style="max-height: 30px; position: absolute; bottom: 0; right: 0;"
                        />
                    <?php else: ?>
                        <div
                                style="position: absolute; bottom: 0; right: 0; font-size: 14px; line-height: 30px;"
                        >
                            <?= htmlspecialchars($leave->name, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <div style="position: absolute; bottom: -20px; right: 50px; font-size: 12px;">
                        Signature of applicant
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div style="margin-top: 80px;">
        <div style="margin-bottom: 60px;">
            <div style="font-weight: bold; margin-bottom: 10px;">Recommendation of Division Head</div>
            <?= htmlspecialchars($leave->action_remark) ?> By <?= htmlspecialchars($leave->reporting_officer_name) ?>
        </div>

        <div style="margin-bottom: 60px;">
            <div style="font-weight: bold; margin-bottom: 10px;">HR Department</div>
        </div>
    </div>

    <!-- Leave Details Summary (for reference) -->
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #ccc; font-size: 11px; color: #666;">
        <strong>Leave Details Summary:</strong><br>

        Status: <?php echo ucfirst($leave->status); ?> |
        Applied: <?php echo date('d/m/Y H:i', strtotime($leave->created_at)); ?>
        <?php if (!empty($leave->action_at)): ?>
            | Action Date: <?php echo date('d/m/Y H:i', strtotime($leave->action_at)); ?>
        <?php endif; ?>
        <br>
        Leave Types Used:
        <?php if ($leave->cl_used > 0): ?>CL: <?php echo $leave->cl_used; ?><?php endif; ?>
        <?php if ($leave->paid_used > 0): ?>Paid: <?php echo $leave->paid_used; ?><?php endif; ?>
        <?php if ($leave->extra_used > 0): ?>Extra: <?php echo $leave->extra_used; ?><?php endif; ?>
    </div>
</div>
</body>
</html>