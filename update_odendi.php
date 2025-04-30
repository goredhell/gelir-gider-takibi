<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $odendi = $_POST['odendi'] ?? null;

    if ($id !== null && ($odendi === "1" || $odendi === "0")) {
        if ($odendi == "1") {
            // Ödendi işaretlenmişse, bugünün tarihini kaydet
            $odeme_tarihi = date('Y-m-d');
        } else {
            // İşaret kaldırılmışsa, ödeme tarihini null yap
            $odeme_tarihi = null;
        }

        $stmt = $pdo->prepare("UPDATE islemler SET odendi = ?, odeme_tarihi = ? WHERE id = ?");
        $stmt->execute([$odendi, $odeme_tarihi, $id]);

        echo "Güncellendi";
    } else {
        echo "Geçersiz veri";
    }
} else {
    echo "Geçersiz istek";
}
