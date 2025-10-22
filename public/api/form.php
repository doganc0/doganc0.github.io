<?php
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$productId = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ürün seçimi']);
    exit;
}

try {
    $db = Database::getInstance();
    $typeRepository = new InsuranceType($db);
    $selectedType = $typeRepository->find($productId);

    if (!$selectedType || (int)$selectedType['status'] !== 1) {
        echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı']);
        exit;
    }

    $stepsRepository = new FormStep($db);
    $fieldsRepository = new FormField($db);
    $steps = $stepsRepository->getByType($productId);

    foreach ($steps as &$step) {
        $step['fields'] = $fieldsRepository->getByStep((int)$step['id']);
    }

    echo json_encode([
        'success' => true,
        'product' => $selectedType,
        'steps' => $steps
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası']);
}
