<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IHRMS - Login</title>
    <link href="<?= base_url('asset/shrm/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('asset/shrm/css/style.css') ?>" rel="stylesheet">
    <style>
        .captcha-image {
            height: 50px;
            width: 150px;
        }

        .captcha-refresh {
            border: none;
            background: none;
            color: #007bff;
            font-size: 18px;
        }

        .captcha-refresh:hover {
            color: #0056b3;
        }
    </style>
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

        <form id="loginForm" action="<?= base_url('shrm/login/authenticate') ?>" method="post">
            <?php
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>
                    Email Address
                </label>
                <div class="input-wrapper">
                    <input type="email" class="form-control" name="email" id="email"
                           placeholder="Enter your email address" required autofocus >
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
                           placeholder="Enter your password" required>
                    <div class="error-message">Password is required</div>
                </div>
            </div>

            <!-- Math Captcha -->
            <!-- Math Captcha -->
            <div class="form-group">
                <label for="captcha" class="form-label">
                    <i class="fas fa-calculator me-2"></i>
                    Security Check
                </label>
                <div class="row align-items-center g-2">
                    <div class="col-auto">
                        <img src="<?= base_url('shrm/login/captcha') ?>" id="captcha-image"
                             class="captcha-image border rounded bg-light" alt="Math Captcha">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="captcha-refresh btn btn-link p-2" onclick="refreshCaptcha()"
                                title="Refresh Captcha">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="col-auto">
                        <div class="input-wrapper">
                            <input type="number" class="form-control text-center" name="captcha" id="captcha"
                                   placeholder="Answer" required style="width: 100px;">
                            <div class="error-message">Please solve the math problem</div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <i class="fas fa-sign-in-alt me-2"></i>
                Sign In
            </button>
        </form>
    </div>
</div>

<script src="<?= base_url('asset/shrm/vendor/jquery.min.js') ?>"></script>
<script src="<?= base_url('asset/shrm/vendor/jquery_validate/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('asset/shrm/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script>
    function refreshCaptcha() {
        const captchaImage = document.getElementById('captcha-image');
        captchaImage.src = '<?= base_url('shrm/login/captcha') ?>?' + Math.random();
        document.getElementById('captcha').value = '';
    }

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
                },
                captcha: {
                    required: true,
                    number: true
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
                },
                captcha: {
                    required: "Please solve the math problem",
                    number: "Please enter a valid number"
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

        // Refresh captcha on failed login
        <?php if ($this->session->flashdata('error')): ?>
        setTimeout(function () {
            refreshCaptcha();
        }, 1000);
        <?php endif; ?>
    });
</script>
<!--Disable to right-click-->
<script>
	// Disable right-click
	$(document).on("contextmenu", function (e) {
		e.preventDefault();
	});

	// Disable F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S, etc.
	$(document).keydown(function (e) {
		if (
			e.keyCode === 123 || // F12
			(e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
			(e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
			(e.ctrlKey && e.keyCode === 85) || // Ctrl+U
			(e.ctrlKey && e.keyCode === 83) || // Ctrl+S
			(e.ctrlKey && e.shiftKey && e.keyCode === 67) // Ctrl+Shift+C
		) {
			return false;
		}
	});
</script>
</body>
</html>
