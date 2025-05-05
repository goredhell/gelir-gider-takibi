<?php
include '../db.php';

$headers = getallheaders();
$api_key = $headers['X-API-KEY'] ?? '';

$VALID_API_KEY = '123456789'; // Güçlü bir anahtar belirleyin

if ($api_key !== $VALID_API_KEY) {
    http_response_code(401);
    echo json_encode(['error' => 'Geçersiz API anahtarı']);
    exit;
}

header('Content-Type: application/json');

$baslangic = $_GET['baslangic'] ?? date('Y-m-01');
$bitis = $_GET['bitis'] ?? date('Y-m-t');
$sadece_odenmemisler = isset($_GET['sadece_odenmemisler']) ? true : false;
$etiket = $_GET['etiket'] ?? '';

$sql = "SELECT * FROM islemler WHERE tarih BETWEEN ? AND ?";
$params = [$baslangic, $bitis];

if ($sadece_odenmemisler) {
    $sql .= " AND odendi = 0";
}

if (!empty($etiket)) {
    $sql .= " AND etiket = ?";
    $params[] = $etiket;
}

$sql .= " ORDER BY tarih ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sonuclar = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($sonuclar);
