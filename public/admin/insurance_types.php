<?php
require_once __DIR__ . '/partials/auth.php';

$pageTitle = 'Sigorta Türleri';
$insuranceRepo = new InsuranceType($db);
$message = null;

function uploadImage(string $field, ?string $current = null): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return $current;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return $current;
    }

    $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/svg+xml' => 'svg'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        return $current;
    }

    $extension = $allowed[$mime];
    $filename = uniqid('product_', true) . '.' . $extension;
    $destinationDir = realpath(__DIR__ . '/../uploads');
    if (!$destinationDir) {
        $destinationDir = __DIR__ . '/../uploads';
    }
    $destination = $destinationDir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return '/uploads/' . $filename;
    }

    return $current;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $image = uploadImage('image_path');
        $insuranceRepo->create([
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'image_path' => $image,
            'status' => isset($_POST['status']) ? 1 : 0,
        ]);
        $message = 'Sigorta türü eklendi.';
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $type = $insuranceRepo->find($id);
        if ($type) {
            $image = uploadImage('image_path', $type['image_path']);
            $insuranceRepo->update($id, [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'image_path' => $image,
                'status' => isset($_POST['status']) ? 1 : 0,
            ]);
            $message = 'Sigorta türü güncellendi.';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $insuranceRepo->delete($id);
            $message = 'Sigorta türü silindi.';
        }
    }
}

$types = $insuranceRepo->all();
$editType = null;
if (isset($_GET['edit'])) {
    $editType = $insuranceRepo->find((int)$_GET['edit']);
}

require __DIR__ . '/partials/header.php';
?>
<div class="card-box">
    <h2><?= $editType ? 'Sigorta Türünü Güncelle' : 'Yeni Sigorta Türü Ekle' ?></h2>
    <?php if ($message): ?>
        <p class="badge" style="background: rgba(52, 199, 89, 0.2); color:#34c759;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?= $editType ? 'update' : 'create' ?>">
        <?php if ($editType): ?>
            <input type="hidden" name="id" value="<?= $editType['id'] ?>">
        <?php endif; ?>
        <div class="form-inline">
            <div class="form-group">
                <label for="name">Ürün Adı</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($editType['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Kısa Açıklama</label>
                <input type="text" id="description" name="description" class="form-control" value="<?= htmlspecialchars($editType['description'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="image_path">Görsel</label>
                <input type="file" id="image_path" name="image_path" class="form-control">
                <?php if ($editType && $editType['image_path']): ?>
                    <small>Mevcut: <a href="<?= htmlspecialchars($editType['image_path']) ?>" target="_blank">Görseli görüntüle</a></small>
                <?php endif; ?>
            </div>
            <div class="form-group" style="align-self:flex-end;">
                <label class="checkbox-group">
                    <input type="checkbox" name="status" <?= isset($editType['status']) ? ((int)$editType['status'] === 1 ? 'checked' : '') : 'checked' ?>>
                    <span>Aktif</span>
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editType ? 'Güncelle' : 'Ekle' ?></button>
        <?php if ($editType): ?>
            <a href="insurance_types.php" class="btn btn-secondary">Vazgeç</a>
        <?php endif; ?>
    </form>
</div>

<div class="card-box">
    <h2>Tüm Sigorta Türleri</h2>
    <?php if ($types): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Adı</th>
                    <th>Açıklama</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td><?= $type['id'] ?></td>
                        <td><?= htmlspecialchars($type['name']) ?></td>
                        <td><?= htmlspecialchars($type['description']) ?></td>
                        <td><?= ((int)$type['status'] === 1) ? 'Aktif' : 'Pasif' ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary" href="insurance_types.php?edit=<?= $type['id'] ?>">Düzenle</a>
                            <form method="post" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $type['id'] ?>">
                                <button type="submit" class="btn btn-primary" style="background:#ff5a5f; box-shadow:none;">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Henüz sigorta türü eklenmedi.</p>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php';
