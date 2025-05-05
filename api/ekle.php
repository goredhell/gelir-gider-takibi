<?php
include '../db.php';

// --- Güvenlik: API Key Kontrolü ---
$expected_key = "ABC123XYZ"; // Burayı kendine özel ve güçlü bir anahtarla değiştir
$api_key = $_GET['api_key'] ?? $_POST['api_key'] ?? '';

if ($api_key !== $expected_key) {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Yetkisiz erişim. Geçersiz API anahtarı."
    ]);
    exit;
}

// --- Gelen verileri işle ---
$tarih = $_POST['tarih'] ?? null;
$miktar = $_POST['miktar'] ?? null;
$aciklama = $_POST['aciklama'] ?? null;
$etiket = $_POST['etiket'] ?? null;

if ($tarih && $miktar !== null) {
    $stmt = $pdo->prepare("INSERT INTO islemler (tarih, miktar, aciklama, etiket) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$tarih, $miktar, $aciklama, $etiket]);

    echo json_encode(["success" => $success]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Eksik veri gönderildi."
    ]);
}
?>
