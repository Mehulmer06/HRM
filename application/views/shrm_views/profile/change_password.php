<?php $this->load->view('shrm_views/includes/header');
include('./application/views/shrm_views/pages/message.php'); ?>
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-cog"></i>
        Settings
    </h1>
    <nav class="breadcrumb-nav">
        <a href="<?= base_url('dashboard') ?>">Dashboard</a> /
        <span class="text-muted">Settings</span>
    </nav>
</div>

<!-- Settings Container -->
<div class="row">
    <!-- Settings Navigation -->
    <div class="col-lg-3">
        <div class="form-card">
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action active" data-section="account">
                    <i class="fas fa-user-cog me-2"></i>
                    Account Settings
                </a>
                <a href="#" class="list-group-item list-group-item-action" data-section="security">
                    <i class="fas fa-shield-alt me-2"></i>
                    Security
                </a>
            </div>
        </div>
    </div>
    <!-- Settings Content -->
    <div class="col-lg-9">
        <!-- Account Settings -->
        <div class="settings-section" id="account">
            <div class="form-card">
                <h3 class="form-section-title">
                    <i class="fas fa-user-cog"></i>
                    Account Settings
                </h3>

                <form id="updatePasswordForm" action="<?= base_url('update-phone') ?>" method="post"
                      enctype="multipart/form-data">
                    <?php
                    $csrf = array(
                        'name' => $this->security->get_csrf_token_name(),
                        'hash' => $this->security->get_csrf_hash()
                    );
                    ?>
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
                    <div class="row form-row">
                        <div class="col-md-6">
                            <label class="form-label">Display Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="<?= $users->name ?>"
                                   readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="email"
                                   value="<?= $users->email ?>" readonly>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-md-6">
                            <img id="profilePreview"
                                 src="<?= base_url('uploads/photo/' . $users->photo) ?>"
                                 alt="Profile"
                                 class="rounded-circle"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-md-6">
                            <input type="file" class="form-control mb-2" accept="image/*" id="photo" name="photo">
                            <small class="text-muted">JPG, PNG files up to 2MB</small>
                        </div>
                    </div>

                    <div class="row form-row">
                        <div class="col-md-12">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="phone"
                                   value="<?= $users->phone ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="settings-section d-none" id="security">
            <div class="form-card">
                <h3 class="form-section-title">
                    <i class="fas fa-key"></i>
                    Change Password
                </h3>

                <form id="changePasswordForm" action="<?= base_url('change-update') ?>" method="post">
                    <?php
                    $csrf = array(
                        'name' => $this->security->get_csrf_token_name(),
                        'hash' => $this->security->get_csrf_hash()
                    );
                    ?>
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
                    <div class="form-row mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="current_password"
                               placeholder="Enter current password">
                    </div>
                    <div class="row form-row">
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password"
                                   placeholder="Enter new password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password"
                                   placeholder="Confirm new password">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('shrm_views/includes/footer'); ?>
<script>
    $(document).ready(function () {
        // Account Settings Form Validation
        $("#updatePasswordForm").validate({
            rules: {
                phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                }
            },
            messages: {
                phone: {
                    required: "Please enter your phone number",
                    minlength: "Phone number must be at least 10 digits",
                    maxlength: "Phone number must not exceed 10 digits",
                    digits: "Only digits are allowed"
                }
            },
            errorClass: "text-danger",
            errorElement: "div",

            // Highlight invalid input
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },

            // Unhighlight valid input
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // Password Form Validation
        $("#changePasswordForm").validate({
            rules: {
                current_password: {
                    required: true,
                    minlength: 6
                },
                new_password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "[name='new_password']"
                }
            },
            messages: {
                current_password: {
                    required: "Enter your current password",
                    minlength: "Password must be at least 6 characters"
                },
                new_password: {
                    required: "Enter a new password",
                    minlength: "Password must be at least 6 characters"
                },
                confirm_password: {
                    required: "Confirm your new password",
                    minlength: "Password must be at least 6 characters",
                    equalTo: "Passwords do not match"
                }
            },
            errorClass: "text-danger",
            errorElement: "div",

            // Highlight invalid input
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },

            // Unhighlight valid input
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            }
        });


        // Image preview + size validation
        $('#photo').on('change', function () {
            var file = this.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size exceeds 2MB.');
                    $(this).val('');
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#profilePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Tabs handler
        const navItems = document.querySelectorAll('.list-group-item[data-section]');
        const sections = document.querySelectorAll('.settings-section');

        navItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                navItems.forEach(nav => nav.classList.remove('active'));
                sections.forEach(section => section.classList.add('d-none'));

                this.classList.add('active');
                const sectionId = this.getAttribute('data-section');
                document.getElementById(sectionId).classList.remove('d-none');
            });
        });
    });
</script>
