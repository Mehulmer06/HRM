<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IHRMS - Login</title>
    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="auth-body">
<div class="login-container">
    <div class="login-header">
        <div class="logo-container">
            <div class="auth-logo">
                <i class="fas fa-building"></i>
            </div>
            <h1 class="brand-title">IHRMS</h1>
            <p class="brand-subtitle">Human Resource Management System</p>
        </div>
    </div>

    <div class="login-body">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="welcome-text">
            <h3>Welcome Back</h3>
            <p>Please sign in to your account to continue</p>
        </div>

        <form id="loginForm" action="<?= base_url('login/authenticate') ?>" method="post">
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>
                    Email Address
                </label>
                <div class="input-wrapper">
                    <input type="email" class="form-control" name="email" id="email"
                           placeholder="Enter your email address" required autofocus value="gela@mailinator.com">
                    <div class="error-message">Please enter a valid email address</div>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>
                    Password
                </label>
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password" id="password"
                           placeholder="Enter your password" required value="12345678">
                    <div class="error-message">Password is required</div>
                </div>
            </div>

            <div class="form-options">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="forgot.html" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <i class="fas fa-sign-in-alt me-2"></i>
                Sign In
            </button>
        </form>
    </div>
</div>

<script src="<?= base_url('assets/vendor/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/jquery_validate/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script>
    $(document).ready(function () {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Initialize jQuery validation
        $("#loginForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                email: {
                    required: "Email is required",
                    email: "Please enter a valid email address"
                },
                password: {
                    required: "Password is required",
                    minlength: "Password must be at least 6 characters"
                }
            },
            errorClass: "error-message",
            validClass: "valid",
            errorPlacement: function (error, element) {
                element.parent('.input-wrapper').find('.error-message').html(error.text());
                element.parent('.input-wrapper').addClass('show-error');
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('error').removeClass(validClass);
                $(element).parent('.input-wrapper').addClass('show-error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('error').addClass(validClass);
                $(element).parent('.input-wrapper').removeClass('show-error');
            },
            submitHandler: function (form) {
                const loginBtn = $('#loginBtn');

                // Show loading state
                loginBtn.addClass('loading');
                loginBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Signing In...');

                // Submit form
                form.submit();
            }
        });

        // Clear errors on input
        $('.form-control').on('input', function () {
            if ($(this).hasClass('error')) {
                $(this).removeClass('error');
                $(this).parent('.input-wrapper').removeClass('show-error');
            }
        });
    });
</script>
</body>
</html>