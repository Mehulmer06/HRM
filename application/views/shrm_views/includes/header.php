<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IHRMS - Dashboard</title>
    <link href="<?= base_url('asset/shrm/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/shrm/font-awesome/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/shrm/select2/select2.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/shrm/css/style.css') ?>" rel="stylesheet">
    <link href="<?= base_url('asset/shrm/DataTables/datatables.css') ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= base_url('asset/shrm/css/daterangepicker.css') ?>"/>
    <link href="<?= base_url('asset/shrm/summernote/summernote-bs5.css') ?>" rel="stylesheet">
    <style>
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item.unread {
            background-color: #e3f2fd;
            border-left: 3px solid #007bff;
        }

        /* Notification bell styling to match your header */
        .notification-bell {
            position: relative;
            margin-left: 15px;
            margin-right: 15px;
            cursor: pointer;
            color: #fff;
            font-size: 18px;
            padding: 8px;
            transition: color 0.3s ease;
        }

        .notification-bell:hover {
            color: #f0f0f0;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>

</head>

<body>
<!-- Header -->
<header class="top-header">
    <div class="logo-section">
        <div class="logo">
            <i class="fas fa-building"></i>
        </div>
        <div class="brand-text">
            <h1>IHRMS</h1>
            <p>Human Resource Management System</p>
        </div>
    </div>

    <div class="nav-section">
        <a href="<?= base_url('shrm/dashboard') ?>" class="dashboard-btn" style="text-decoration: none">
            <i class="fas fa-tachometer-alt me-2"></i>
            Dashboard
        </a>

        <!-- FIXED: Notification Bell - properly integrated with your header structure -->
        <div class="dropdown">
            <div class="notification-bell" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false"
                 title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notification-badge d-none" id="notificationBadge">0</span>
            </div>

            <div class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
                <div class="dropdown-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bell me-2"></i>Notifications</span>
                    <button type="button" class="btn btn-sm btn-link p-0 text-primary" onclick="markAllAsRead()">
                        <small>Mark all read</small>
                    </button>
                </div>
                <div class="dropdown-divider"></div>

                <div id="notificationList">
                    <div class="text-center p-3">
                        <small class="text-muted">Click to load notifications</small>
                    </div>
                </div>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-center" href="<?= base_url('work-progress') ?>">
                    <small>View All Evaluations</small>
                </a>
            </div>
        </div>

        <!-- Your existing admin dropdown -->
        <div class="dropdown">
            <div class="admin-dropdown" data-bs-toggle="dropdown">
                <i class="fas fa-user-shield"></i>
                <?= ucfirst($this->session->userdata('user_name') ?? $this->session->userdata('name')) ?>
                <i class="fas fa-chevron-down"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php if ($this->session->userdata('role') !== 'e') : ?>
                    <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user me-2"></i>Profile</a>
                    </li>
                    <li><a class="dropdown-item" href="<?= base_url('change-password') ?>"><i
                                    class="fas fa-cog me-2"></i>Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                <?php endif ?>
                <li><a class="dropdown-item" href="<?= base_url('shrm/logout') ?>"><i
                                class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Main Content -->
<div class="main-content">