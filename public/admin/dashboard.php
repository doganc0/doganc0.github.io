<?php
require_once __DIR__ . '/partials/auth.php';

$pageTitle = 'Genel Bakış';

$insuranceRepo = new InsuranceType($db);
$formStepRepo = new FormStep($db);
$submissionRepo = new Submission($db);

$insuranceTypes = $insuranceRepo->all();
$submissions = $submissionRepo->all();

$totalSteps = 0;
foreach ($insuranceTypes as $type) {
    $totalSteps += count($formStepRepo->getByType((int)$type['id']));
}

require __DIR__ . '/partials/header.php';
?>
<div class="card-box">
    <h2>Proje Özeti</h2>
    <p>Magnus Akıllı Teklif Formu yönetim paneli üzerinden ürünleri, adımları ve kullanıcı başvurularını kolayca yönetebilirsiniz.</p>
</div>

<div class="card-box">
    <h3>İstatistikler</h3>
    <div class="form-inline">
        <div class="form-group">
            <label>Aktif Sigorta Türü</label>
            <div class="badge"><?= count($insuranceTypes) ?></div>
        </div>
        <div class="form-group">
            <label>Tanımlı Form Adımı</label>
            <div class="badge"><?= $totalSteps ?></div>
        </div>
        <div class="form-group">
            <label>Gelen Teklif</label>
            <div class="badge"><?= count($submissions) ?></div>
        </div>
    </div>
</div>

<div class="card-box">
    <h3>Son Teklifler</h3>
    <?php if ($submissions): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sigorta Türü</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($submissions, 0, 5) as $submission): ?>
                    <tr>
                        <td>#<?= $submission['id'] ?></td>
                        <td><?= htmlspecialchars($submission['insurance_name']) ?></td>
                        <td><?= htmlspecialchars($submission['status']) ?></td>
                        <td><?= htmlspecialchars($submission['created_at']) ?></td>
                        <td><a class="btn btn-secondary" href="submissions.php?view=<?= $submission['id'] ?>">Görüntüle</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Henüz bir teklif başvurusu bulunmuyor.</p>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php';
