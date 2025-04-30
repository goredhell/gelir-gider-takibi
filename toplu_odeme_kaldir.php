<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['secili_idler'])) {
    $secili_idler = $_POST['secili_idler'];

    // Güvenli şekilde ID'leri virgülle ayrılmış bir string haline getir
    $placeholders = implode(',', array_fill(0, count($secili_idler), '?'));

    $sql = "UPDATE islemler SET odendi = 0, odeme_tarihi = NULL WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($secili_idler);
}

// Geri yönlendirme için filtre parametrelerini tekrar gönder
$baslangic = urlencode($_POST['baslangic'] ?? '');
$bitis = urlencode($_POST['bitis'] ?? '');
$sadece_odenmemisler = isset($_POST['sadece_odenmemisler']) && $_POST['sadece_odenmemisler'] == 1 ? '&sadece_odenmemisler=1' : '';
$etiket = urlencode($_POST['etiket'] ?? '');

header("Location: rapor_liste.php?baslangic=$baslangic&bitis=$bitis&etiket=$etiket$sadece_odenmemisler");
exit;
