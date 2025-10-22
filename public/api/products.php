<?php
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::getInstance();
    $insuranceType = new InsuranceType($db);
    $products = $insuranceType->all(true);
    echo json_encode(['success' => true, 'products' => $products]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası']);
}
