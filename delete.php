<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM islemler WHERE id = ?");
    $stmt->execute([$id]);
}

// Geri yönlendirme için filtre parametrelerini tekrar gönder
$baslangic = urlencode($_GET['baslangic'] ?? '');
$bitis = urlencode($_GET['bitis'] ?? '');
$sadece_odenmemisler = isset($_GET['sadece_odenmemisler']) && $_GET['sadece_odenmemisler'] == 1 ? '&sadece_odenmemisler=1' : '';
$etiket = urlencode($_GET['etiket'] ?? '');

header("Location: rapor_liste.php?baslangic=$baslangic&bitis=$bitis&etiket=$etiket$sadece_odenmemisler");
exit;
