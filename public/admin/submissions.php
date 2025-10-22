<?php
require_once __DIR__ . '/partials/auth.php';

$pageTitle = 'Gelen Teklifler';
$submissionRepo = new Submission($db);

$submission = null;
$answers = [];

if (isset($_GET['view'])) {
    $submissionId = (int)$_GET['view'];
    $submission = $submissionRepo->find($submissionId);
    if ($submission) {
        $submissionRepo->markAsRead($submissionId);
        $answers = json_decode($submission['submission_data'], true) ?? [];
    }
}

$submissions = $submissionRepo->all();

require __DIR__ . '/partials/header.php';
?>
<div class="card-box">
    <h2>Teklif Başvuruları</h2>
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
                <?php foreach ($submissions as $item): ?>
                    <tr>
                        <td>#<?= $item['id'] ?></td>
                        <td><?= htmlspecialchars($item['insurance_name']) ?></td>
                        <td><?= htmlspecialchars($item['status']) ?></td>
                        <td><?= htmlspecialchars($item['created_at']) ?></td>
                        <td><a class="btn btn-secondary" href="submissions.php?view=<?= $item['id'] ?>">Detay</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Henüz başvuru alınmamış.</p>
    <?php endif; ?>
</div>

<?php if (!empty($submission)): ?>
    <div class="card-box">
        <h2>#<?= $submission['id'] ?> numaralı başvuru</h2>
        <p><strong>Sigorta Türü:</strong> <?= htmlspecialchars($submission['insurance_name']) ?></p>
        <p><strong>Başvuru Tarihi:</strong> <?= htmlspecialchars($submission['created_at']) ?></p>
        <?php foreach ($answers as $step): ?>
            <div class="card-box" style="box-shadow:none; border:1px solid #ececff; margin-top:1rem;">
                <h3><?= htmlspecialchars($step['step_title']) ?></h3>
                <ul>
                    <?php foreach ($step['answers'] as $answer): ?>
                        <li><strong><?= htmlspecialchars($answer['label']) ?>:</strong> <?= htmlspecialchars($answer['value']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php';
