<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IHRMS - Dashboard</title>
    <link href="<?= base_url('asset/shrm/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link href="<?= base_url('asset/shrm/css/style.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link
            href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.3.2/b-3.2.4/b-html5-3.2.4/b-print-3.2.4/r-3.0.5/datatables.min.css"
            rel="stylesheet" integrity="sha384-rOhuyMBXv6TLUQTpQsGUd5KPW3slZlre5DLAV7bf0pYiIWehVz4nZLupIt4Vp6k8"
            crossorigin="anonymous">
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