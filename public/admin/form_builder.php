<?php
require_once __DIR__ . '/partials/auth.php';

$pageTitle = 'Dinamik Formlar';
$insuranceRepo = new InsuranceType($db);
$stepRepo = new FormStep($db);
$fieldRepo = new FormField($db);

$types = $insuranceRepo->all();
$typeId = isset($_GET['type_id']) ? (int)$_GET['type_id'] : (isset($types[0]['id']) ? (int)$types[0]['id'] : 0);
$selectedType = $typeId ? $insuranceRepo->find($typeId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $redirectTypeId = (int)($_POST['type_id'] ?? 0);
    $redirectStepId = (int)($_POST['step_id'] ?? 0);

    switch ($action) {
        case 'add_step':
            $stepRepo->create([
                'insurance_type_id' => $redirectTypeId,
                'step_title' => trim($_POST['step_title'] ?? ''),
                'step_order' => (int)($_POST['step_order'] ?? 0),
            ]);
            break;
        case 'update_step':
            $stepRepo->update($redirectStepId, [
                'step_title' => trim($_POST['step_title'] ?? ''),
                'step_order' => (int)($_POST['step_order'] ?? 0),
            ]);
            break;
        case 'delete_step':
            $fieldRepo->deleteByStep($redirectStepId);
            $stepRepo->delete($redirectStepId);
            $redirectStepId = 0;
            break;
        case 'add_field':
            $fieldRepo->create([
                'step_id' => $redirectStepId,
                'label' => trim($_POST['label'] ?? ''),
                'field_type' => $_POST['field_type'] ?? 'text',
                'placeholder' => trim($_POST['placeholder'] ?? ''),
                'options' => trim($_POST['options'] ?? ''),
                'is_required' => isset($_POST['is_required']) ? 1 : 0,
                'field_order' => (int)($_POST['field_order'] ?? 0),
            ]);
            break;
        case 'update_field':
            $fieldId = (int)($_POST['field_id'] ?? 0);
            $fieldRepo->update($fieldId, [
                'label' => trim($_POST['label'] ?? ''),
                'field_type' => $_POST['field_type'] ?? 'text',
                'placeholder' => trim($_POST['placeholder'] ?? ''),
                'options' => trim($_POST['options'] ?? ''),
                'is_required' => isset($_POST['is_required']) ? 1 : 0,
                'field_order' => (int)($_POST['field_order'] ?? 0),
            ]);
            break;
        case 'delete_field':
            $fieldId = (int)($_POST['field_id'] ?? 0);
            $fieldRepo->delete($fieldId);
            break;
    }

    $query = http_build_query(array_filter([
        'type_id' => $redirectTypeId,
        'step_id' => $redirectStepId,
    ]));
    header('Location: form_builder.php' . ($query ? '?' . $query : ''));
    exit;
}

$steps = $selectedType ? $stepRepo->getByType($selectedType['id']) : [];
$stepId = isset($_GET['step_id']) ? (int)$_GET['step_id'] : (isset($steps[0]['id']) ? (int)$steps[0]['id'] : 0);
$selectedStep = $stepId ? $stepRepo->find($stepId) : null;
$fields = $selectedStep ? $fieldRepo->getByStep($selectedStep['id']) : [];
$fieldTypes = [
    'text' => 'Metin',
    'email' => 'E-posta',
    'phone' => 'Telefon',
    'number' => 'Sayı',
    'dropdown' => 'Seçim (Dropdown)',
    'checkbox' => 'Onay Kutusu',
    'date' => 'Tarih',
    'textarea' => 'Metin Alanı'
];

require __DIR__ . '/partials/header.php';
?>
<div class="card-box">
    <form method="get" class="form-inline">
        <div class="form-group">
            <label for="type_id">Sigorta Türü Seçiniz</label>
            <select name="type_id" id="type_id" class="form-control" onchange="this.form.submit()">
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type['id'] ?>" <?= $typeId === (int)$type['id'] ? 'selected' : '' ?>><?= htmlspecialchars($type['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<?php if ($selectedType): ?>
    <div class="card-box">
        <h2>Adımlar</h2>
        <form method="post" class="form-inline" style="margin-bottom:1.5rem;">
            <input type="hidden" name="action" value="add_step">
            <input type="hidden" name="type_id" value="<?= $selectedType['id'] ?>">
            <div class="form-group">
                <label>Adım Başlığı</label>
                <input type="text" name="step_title" class="form-control" required placeholder="Örn. Kişisel Bilgiler">
            </div>
            <div class="form-group">
                <label>Sıra No</label>
                <input type="number" name="step_order" class="form-control" value="<?= count($steps) + 1 ?>" min="1">
            </div>
            <button type="submit" class="btn btn-primary">Adım Ekle</button>
        </form>

        <?php if ($steps): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Başlık</th>
                        <th>Sıra</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($steps as $step): ?>
                        <tr>
                            <td><?= $step['id'] ?></td>
                            <td><?= htmlspecialchars($step['step_title']) ?></td>
                            <td><?= htmlspecialchars($step['step_order']) ?></td>
                            <td class="actions">
                                <a class="btn btn-secondary" href="form_builder.php?type_id=<?= $selectedType['id'] ?>&step_id=<?= $step['id'] ?>">Alanları Yönet</a>
                                <form method="post" onsubmit="return confirm('Adımı silmek istediğinize emin misiniz? İlişkili sorular da silinecektir.');">
                                    <input type="hidden" name="action" value="delete_step">
                                    <input type="hidden" name="type_id" value="<?= $selectedType['id'] ?>">
                                    <input type="hidden" name="step_id" value="<?= $step['id'] ?>">
                                    <button type="submit" class="btn btn-primary" style="background:#ff5a5f; box-shadow:none;">Sil</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Bu ürüne ait henüz adım eklenmedi.</p>
        <?php endif; ?>
    </div>

    <?php if ($selectedStep): ?>
        <div class="card-box">
            <h2><?= htmlspecialchars($selectedStep['step_title']) ?> Adımı</h2>
            <form method="post" class="form-inline" style="margin-bottom:1.5rem;">
                <input type="hidden" name="action" value="update_step">
                <input type="hidden" name="type_id" value="<?= $selectedType['id'] ?>">
                <input type="hidden" name="step_id" value="<?= $selectedStep['id'] ?>">
                <div class="form-group">
                    <label>Adım Başlığı</label>
                    <input type="text" name="step_title" class="form-control" value="<?= htmlspecialchars($selectedStep['step_title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Sıra No</label>
                    <input type="number" name="step_order" class="form-control" value="<?= htmlspecialchars($selectedStep['step_order']) ?>" min="1">
                </div>
                <button type="submit" class="btn btn-primary">Adımı Güncelle</button>
            </form>

            <h3>Yeni Soru Ekle</h3>
            <form method="post" class="form-inline" style="margin-bottom:1.5rem;">
                <input type="hidden" name="action" value="add_field">
                <input type="hidden" name="type_id" value="<?= $selectedType['id'] ?>">
                <input type="hidden" name="step_id" value="<?= $selectedStep['id'] ?>">
                <div class="form-group">
                    <label>Soru Başlığı</label>
                    <input type="text" name="label" class="form-control" required placeholder="Örn. Adınız Soyadınız">
                </div>
                <div class="form-group">
                    <label>Alan Tipi</label>
                    <select name="field_type" class="form-control">
                        <?php foreach ($fieldTypes as $value => $label): ?>
                            <option value="<?= $value ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Placeholder</label>
                    <input type="text" name="placeholder" class="form-control" placeholder="Örn. Lütfen adınızı girin">
                </div>
                <div class="form-group">
                    <label>Seçenekler</label>
                    <input type="text" name="options" class="form-control" placeholder="Sadece dropdown için: Seçenek1, Seçenek2">
                </div>
                <div class="form-group">
                    <label>Sıra No</label>
                    <input type="number" name="field_order" class="form-control" value="<?= count($fields) + 1 ?>" min="1">
                </div>
                <div class="form-group" style="align-self:flex-end;">
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_required" checked>
                        <span>Zorunlu</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Soru Ekle</button>
            </form>

            <h3>Adımdaki Sorular</h3>
            <?php if ($fields): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Başlık</th>
                            <th>Tip</th>
                            <th>Zorunlu</th>
                            <th>Sıra</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fields as $field): ?>
                            <tr>
                                <td><?= $field['id'] ?></td>
                                <td><?= htmlspecialchars($field['label']) ?></td>
                                <td><?= htmlspecialchars($fieldTypes[$field['field_type']] ?? $field['field_type']) ?></td>
                                <td><?= (int)$field['is_required'] === 1 ? 'Evet' : 'Hayır' ?></td>
                                <td><?= htmlspecialchars($field['field_order']) ?></td>
                                <td class="actions">
                                    <details>
                                        <summary class="btn btn-secondary">Düzenle</summary>
                                        <form method="post" style="margin-top:1rem; display:grid; gap:0.75rem;">
                                            <input type="hidden" name="action" value="update_field">
                                            <input type="hidden" name="type_id" value="<?= $selectedType['id'] ?>">
                                            <input type="hidden" name="step_id" value="<?= $selectedStep['id'] ?>">
                                            <input type="hidden" name="field_id" value="<?= $field['id'] ?>">
                                            <label>Soru Başlığı
                                                <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($field['label']) ?>" required>
                                            </label>
                                            <label>Alan Tipi
                                                <select name="field_type" class="form-control">
                                                    <?php foreach ($fieldTypes as $value => $label): ?>
                                                        <option value="<?= $value ?>" <?= $field['field_type'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </label>
                                            <label>Placeholder
                                                <input type="text" name="placeholder" class="form-control" value="<?= htmlspecialchars($field['placeholder']) ?>">
                                            </label>
                                            <label>Seçenekler
                                                <input type="text" name="options" class="form-control" value="<?= htmlspecialchars($field['options']) ?>">
                                            </label>
                                            <label>Sıra
                                                <input type="number" name="field_order" class="form-control" value="<?= htmlspecialchars($field['field_order']) ?>">
                                            </label>
                                            <label class="checkbox-group">
                                                <input type="checkbox" name="is_required" <?= (int)$field['is_required'] === 1 ? 'checked' : '' ?>>
                                                <span>Zorunlu</span>
                                            </label>
                                            <div class="actions">
                                                <button type="submit" class="btn btn-primary">Kaydet</button>
                                            </div>
                                        </form>
                                        <form method="post" onsubmit="return confirm('Soruyu silmek istediğinize emin misiniz?');" style="margin-top:0.75rem;">
                                            <input type="hidden" name="action" value="delete_field">
                                            <input type="hidden" name="type_id" value="<?= $selectedType['id'] ?>">
                                            <input type="hidden" name="step_id" value="<?= $selectedStep['id'] ?>">
                                            <input type="hidden" name="field_id" value="<?= $field['id'] ?>">
                                            <button type="submit" class="btn btn-primary" style="background:#ff5a5f; box-shadow:none;">Sil</button>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Bu adım için henüz soru eklenmedi.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="card-box">
        <p>Öncelikle sigorta türü oluşturmalısınız.</p>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php';
