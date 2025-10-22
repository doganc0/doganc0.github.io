<?php
require_once __DIR__ . '/partials/auth.php';

$pageTitle = 'Genel Ayarlar';
$settingsRepo = new Settings($db);
$settings = $settingsRepo->getSettings();

$message = null;

function handleUpload(string $field, ?string $currentPath): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return $currentPath;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return $currentPath;
    }

    $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/svg+xml' => 'svg'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        return $currentPath;
    }

    $extension = $allowed[$mime];
    $filename = uniqid('asset_', true) . '.' . $extension;
    $destinationDir = realpath(__DIR__ . '/../uploads');
    if (!$destinationDir) {
        $destinationDir = __DIR__ . '/../uploads';
    }
    $destination = $destinationDir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return '/uploads/' . $filename;
    }

    return $currentPath;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteLogo = handleUpload('site_logo', $settings['site_logo']);
    $formAvatar = handleUpload('form_avatar', $settings['form_avatar']);

    $settingsRepo->update([
        'site_logo' => $siteLogo,
        'site_phone' => trim($_POST['site_phone'] ?? ''),
        'form_avatar' => $formAvatar,
        'redirect_url' => trim($_POST['redirect_url'] ?? ''),
        'notification_email' => trim($_POST['notification_email'] ?? ''),
        'id' => $settings['id'],
    ]);

    $settings = $settingsRepo->getSettings();
    $message = 'Ayarlar başarıyla güncellendi.';
}

require __DIR__ . '/partials/header.php';
?>
<div class="card-box">
    <h2>Genel Site Ayarları</h2>
    <?php if ($message): ?>
        <p class="badge" style="background: rgba(52, 199, 89, 0.2); color:#34c759;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="site_logo">Logo</label>
            <input type="file" id="site_logo" name="site_logo" class="form-control">
            <?php if (!empty($settings['site_logo'])): ?>
                <small>Mevcut: <a href="<?= htmlspecialchars($settings['site_logo']) ?>" target="_blank">Logoyu görüntüle</a></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="form_avatar">Form Temsilci Görseli</label>
            <input type="file" id="form_avatar" name="form_avatar" class="form-control">
            <?php if (!empty($settings['form_avatar'])): ?>
                <small>Mevcut: <a href="<?= htmlspecialchars($settings['form_avatar']) ?>" target="_blank">Avatarı görüntüle</a></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="site_phone">Telefon Numarası</label>
            <input type="text" id="site_phone" name="site_phone" class="form-control" value="<?= htmlspecialchars($settings['site_phone']) ?>" required>
        </div>
        <div class="form-group">
            <label for="redirect_url">Başarı Yönlendirme Adresi</label>
            <input type="url" id="redirect_url" name="redirect_url" class="form-control" value="<?= htmlspecialchars($settings['redirect_url']) ?>" placeholder="https://magnussigorta.com" required>
        </div>
        <div class="form-group">
            <label for="notification_email">Bildirim E-postası</label>
            <input type="email" id="notification_email" name="notification_email" class="form-control" value="<?= htmlspecialchars($settings['notification_email']) ?>" placeholder="info@magnussigorta.com">
        </div>
        <button type="submit" class="btn btn-primary">Kaydet</button>
    </form>
</div>
<?php require __DIR__ . '/partials/footer.php';
