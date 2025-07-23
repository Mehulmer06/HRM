</div>

<script src="<?= base_url('asset/shrm/vendor/jquery.min.js') ?>"></script>
<script src="<?= base_url('asset/shrm/vendor/jquery_validate/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('asset/shrm/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('asset/shrm/DataTables/datatables.js') ?>"></script>
<script src="<?= base_url('asset/shrm/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('asset/shrm/summernote/summernote-bs5.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('asset/shrm/js/moment.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('asset/shrm/js/daterangepicker.min.js') ?>"></script>


<script>
    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert-close');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
</script>
<!-- JavaScript for Notifications -->
<script>
    $(document).ready(function () {
        console.log('Notification system starting...');

        // Load notifications when bell is clicked
        $('#notificationBell').on('click', function () {
            loadNotifications();
        });

        // Check for new notifications every 30 seconds
        checkNotificationCount();
        setInterval(checkNotificationCount, 30000);
    });

    function checkNotificationCount() {
        $.get('<?= base_url("work-progress/notifications/count") ?>')
            .done(function (data) {
                console.log('Count response:', data);
                updateBadge(data.count || 0);
            })
            .fail(function (xhr) {
                console.error('Count error:', xhr.responseText);
            });
    }

    function loadNotifications() {
        console.log('Loading UNREAD notifications only...');
        $('#notificationList').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

        $.get('<?= base_url("work-progress/notifications") ?>')
            .done(function (data) {
                console.log('Notifications response:', data);
                if (data.success && data.notifications) {
                    displayNotifications(data.notifications);
                } else {
                    $('#notificationList').html('<div class="text-center p-3 text-muted">Failed to load notifications</div>');
                }
            })
            .fail(function (xhr) {
                console.error('Notification error:', xhr.responseText);
                $('#notificationList').html(`
                <div class="text-center p-3 text-danger">
                    Error loading notifications<br>
                    <small>Status: ${xhr.status}</small><br>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadNotifications()">Try Again</button>
                </div>
            `);
            });
    }

    function displayNotifications(notifications) {
        if (notifications.length === 0) {
            $('#notificationList').html(`
            <div class="text-center p-4 text-muted">
                <i class="fas fa-check-circle fa-2x mb-2" style="color: #28a745;"></i><br>
                <span>All caught up!</span><br>
                <small>No new notifications</small>
            </div>
        `);
            return;
        }

        let html = '';
        notifications.forEach(function (notif) {
            // All notifications from server are unread now
            const timeAgo = getTimeAgo(notif.created_at);

            html += `
            <div class="notification-item unread" onclick="handleNotificationClick(${notif.id}, ${notif.evaluation_id})">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-1">
                        <div class="fw-bold small">${notif.commenter_name}</div>
                        <div class="text-muted small">commented on "${notif.evaluation_title}"</div>
                        <div class="text-muted" style="font-size: 11px;">${notif.comment_preview}</div>
                        <div class="text-muted smaller">${timeAgo}</div>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-circle" style="font-size: 8px;" title="New notification"></i>
                    </div>
                </div>
            </div>
        `;
        });

        $('#notificationList').html(html);
    }

    function handleNotificationClick(commentId, evaluationId) {
        console.log('Notification clicked:', commentId, evaluationId);

        // Mark as read first
        $.post('<?= base_url("work-progress/notifications/mark-read") ?>', {
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
            comment_id: commentId
        }).done(function (response) {
            console.log('Mark as read response:', response);
            if (response.success) {
                console.log('✅ Marked as read - notification will disappear from list');

                // Remove the notification from the current list immediately
                $(`[onclick*="${commentId}"]`).fadeOut(300, function () {
                    $(this).remove();

                    // Check if any notifications are left
                    if ($('.notification-item').length === 0) {
                        $('#notificationList').html(`
                        <div class="text-center p-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2" style="color: #28a745;"></i><br>
                            <span>All caught up!</span><br>
                            <small>No new notifications</small>
                        </div>
                    `);
                    }
                });

                // Update badge count
                checkNotificationCount();
            }
        });

        // Close dropdown
        $('#notificationBell').dropdown('hide');

        // Redirect to comments page after short delay
        setTimeout(() => {
            const encodedId = (evaluationId * 15394).toString(36);
            window.location.href = '<?= base_url("work-progress/comments/") ?>' + encodedId;
        }, 300);
    }

    function markAllAsRead() {
        console.log('Marking all notifications as read...');

        // Get all visible notifications
        const unreadNotifications = [];
        $('.notification-item').each(function () {
            const onclick = $(this).attr('onclick');
            if (onclick) {
                const match = onclick.match(/handleNotificationClick\((\d+),/);
                if (match) {
                    unreadNotifications.push(match[1]);
                }
            }
        });

        if (unreadNotifications.length === 0) {
            $('#notificationList').html(`
            <div class="text-center p-4 text-muted">
                <i class="fas fa-check-circle fa-2x mb-2" style="color: #28a745;"></i><br>
                <span>All caught up!</span><br>
                <small>No notifications to mark as read</small>
            </div>
        `);
            return;
        }

        // Show loading
        $('#notificationList').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Marking all as read...</div>');

        // Mark each notification as read
        let completed = 0;
        unreadNotifications.forEach((commentId) => {
            $.post('<?= base_url("work-progress/notifications/mark-read") ?>', {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                comment_id: commentId
            }).done(function () {
                completed++;
                if (completed === unreadNotifications.length) {
                    // All marked as read, show success message
                    $('#notificationList').html(`
                    <div class="text-center p-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2" style="color: #28a745;"></i><br>
                        <span>All notifications marked as read!</span><br>
                        <small>Notification list is now clear</small>
                    </div>
                `);

                    // Update badge
                    updateBadge(0);
                }
            });
        });
    }

    function updateBadge(count) {
        const badge = $('#notificationBadge');
        if (count > 0) {
            badge.text(count > 99 ? '99+' : count).removeClass('d-none');
        } else {
            badge.addClass('d-none');
        }
    }

    function getTimeAgo(dateString) {
        try {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return diffMins + ' min ago';
            if (diffHours < 24) return diffHours + ' hr ago';
            if (diffDays < 7) return diffDays + ' days ago';
            return date.toLocaleDateString();
        } catch (e) {
            return 'Recently';
        }
    }
</script>
<script type="text/javascript">
    setTimeout(function() {
        alert("Session Terminated. Please log in again!");
        window.location.href = '<?= base_url("shrm/logout"); ?>';
    }, 300000); // 5 minutes = 300,000 ms
</script>
</body>

</html>