<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $subject ?></title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
<div style="max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2 style="color: #333; border-bottom: 1px solid #ddd; padding-bottom: 10px;">System Alert</h2>

    <p>Dear Team,</p>

    <p><strong><?= $user_name ?></strong> has reported a system issue:</p>

    <blockquote style="background: #f0f0f0; padding: 10px 15px; border-left: 5px solid #ff0000;">
        <?= $description ?>
    </blockquote>

    <?php if (!empty($document)) : ?>
        <p><strong>Attached Document:</strong>
            <a href="<?= base_url('uploads/request_issue/' . $document) ?>" target="_blank">
                <?= $document ?>
            </a>
        </p>
    <?php endif; ?>

    <p>Please take the necessary actions to resolve the issue as soon as possible.</p>

    <p>Regards,<br>
        MyApp Notification System</p>
</div>
</body>
</html>
