<?php
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload || empty($payload['product_id']) || empty($payload['responses'])) {
    echo json_encode(['success' => false, 'message' => 'Eksik veya hatalı veri gönderildi.']);
    exit;
}

try {
    $db = Database::getInstance();
    $insuranceRepo = new InsuranceType($db);
    $submissionRepo = new Submission($db);
    $settingsRepo = new Settings($db);

    $product = $insuranceRepo->find((int)$payload['product_id']);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Sigorta ürünü bulunamadı.']);
        exit;
    }

    $submissionId = $submissionRepo->create((int)$payload['product_id'], $payload['responses']);

    $settings = $settingsRepo->getSettings();
    $emailTo = !empty($settings['notification_email']) ? $settings['notification_email'] : MAIL_RECIPIENT;

    $lines = [
        "Yeni bir teklif başvurusu alındı.",
        "Ürün: " . $product['name'],
        "Başvuru No: #" . $submissionId,
        "---"
    ];

    foreach ($payload['responses'] as $step) {
        $lines[] = strtoupper($step['step_title']);
        foreach ($step['answers'] as $answer) {
            $label = $answer['label'];
            $value = $answer['value'] === '' ? '-' : $answer['value'];
            $lines[] = "- {$label}: {$value}";
        }
        $lines[] = '';
    }

    $message = implode("\n", $lines);
    $headers = 'From: ' . MAIL_FROM . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
    @mail($emailTo, 'Magnus - Yeni Teklif Başvurusu', $message, $headers);

    echo json_encode(['success' => true, 'submission_id' => $submissionId]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Teklif kaydedilirken hata oluştu.']);
}
