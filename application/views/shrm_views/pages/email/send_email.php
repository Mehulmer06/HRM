<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IHRMS Login Credentials</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .logo {
            display: inline-block;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            line-height: 60px;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .email-body {
            padding: 40px 30px;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .welcome-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 0;
        }

        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .intro-text {
            margin-bottom: 30px;
            font-size: 16px;
            color: #555;
        }

        .credentials-box {
            background: linear-gradient(135deg, #e8f4fd, #f0f8ff);
            border: 2px solid #3498db;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
        }

        .credentials-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(52, 152, 219, 0.2);
        }

        .credential-item:last-child {
            border-bottom: none;
        }

        .credential-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            flex: 1;
        }

        .credential-value {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            flex: 2;
            text-align: center;
            word-break: break-all;
        }

        .access-button {
            display: block;
            width: 200px;
            margin: 25px auto;
            padding: 15px 25px;
            background: linear-gradient(135deg, #3498db, #5dade2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .access-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .security-title {
            color: #856404;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .security-list {
            color: #856404;
            margin-left: 20px;
            font-size: 14px;
        }

        .security-list li {
            margin-bottom: 5px;
        }

        .getting-started {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .getting-started-title {
            color: #0c5460;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .getting-started-list {
            color: #0c5460;
            margin-left: 20px;
            font-size: 14px;
        }

        .getting-started-list li {
            margin-bottom: 5px;
        }

        .support-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .support-text {
            margin-bottom: 15px;
            color: #555;
        }

        .support-contact {
            background: #3498db;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }

        .email-footer {
            background: #f8f9fa;
            padding: 25px 30px;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }

        .footer-signature {
            margin-bottom: 15px;
        }

        .footer-name {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .footer-title {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 3px;
        }

        .footer-company {
            font-size: 14px;
            color: #6c757d;
        }

        .footer-divider {
            height: 1px;
            background: #dee2e6;
            margin: 15px 0;
        }

        .footer-disclaimer {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.4;
        }

        .employee-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .employee-info-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .employee-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .employee-detail:last-child {
            border-bottom: none;
        }

        .employee-label {
            font-weight: 600;
            color: #555;
        }

        .employee-value {
            color: #2c3e50;
            font-weight: 500;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 0;
            }

            .email-body {
                padding: 25px 20px;
            }

            .credential-item {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }

            .credential-value {
                width: 100%;
            }

            .employee-detail {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- Email Header -->
    <div class="email-header">
        <div class="logo">🏢</div>
        <h1 class="company-name">IHRMS</h1>
        <p class="company-tagline">Human Resource Management System</p>
    </div>

    <!-- Email Body -->
    <div class="email-body">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2 class="welcome-title">Welcome to the Team!</h2>
            <p class="welcome-message">Your IHRMS account has been created successfully</p>
        </div>

        <!-- Greeting -->
        <div class="greeting">
            Dear <strong><?= ucfirst($name)?></strong>,
        </div>

        <!-- Introduction -->
        <div class="intro-text">
            We are pleased to welcome you to HRMS! Your account has been created in our Human Resource
            Management System (IHRMS), and we're providing you with your login credentials to get started.
        </div>

        <!-- Employee Information -->
        <div class="employee-info">
            <div class="employee-info-title">
                👤 Your Profile Information
            </div>

            <div class="employee-detail">
                <span class="employee-label">Reporting Manager:</span>
                <span class="employee-value"><?=$reportingOfficerName .' - '.$reportingOfficerDesignation ?></span>
            </div>
        </div>

        <!-- Login Credentials -->
        <div class="credentials-box">
            <div class="credentials-title">
                🔐 Your Login Credentials
            </div>

            <div class="credential-item">
                <span class="credential-label">System URL:</span>
                <span class="credential-value">http://localhost/hrm_demo/shrm/login</span>
            </div>

            <div class="credential-item">
                <span class="credential-label">Email:</span>
                <span class="credential-value"><?= $email?></span>
            </div>

            <div class="credential-item">
                <span class="credential-label">Temporary Password:</span>
                <span class="credential-value"><?= $password ?></span>
            </div>
        </div>

        <!-- Access Button -->
        <a href="http://localhost/hrm_demo/shrm/login" class="access-button">
            🚀 Access IHRMS System
        </a>

        <!-- Security Notice -->
        <div class="security-notice">
            <div class="security-title">
                ⚠️ Important Security Information
            </div>
            <ul class="security-list">
                <li>You will be required to change your password upon first login</li>
                <li>Please keep your credentials secure and do not share them with anyone</li>

            </ul>
        </div>


        <!-- System Features Info -->


        <!-- Email Footer -->
        <div class="email-footer">
            <div class="footer-signature">
                <div class="footer-name">HR Department</div>
                <div class="footer-title">Human Resource Management</div>
                <div class="footer-company">Your Company Name</div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-disclaimer">
                This email contains confidential information intended only for the recipient named above.
                If you have received this email in error, please notify the sender immediately and delete this email.
                Please do not forward or share your login credentials with anyone.
            </div>
        </div>
    </div>
</body>
</html>
