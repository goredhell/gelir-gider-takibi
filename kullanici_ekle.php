<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $yeniKullanici = trim($_POST['yeni_kullanici'] ?? '');
    $yeniparola = $_POST['yeni_parola'] ?? '';
    $admin = isset($_POST['admin']) ? 1 : 0; // admin checkbox işaretlendiyse 1, değilse 0

    if ($yeniKullanici === '' || $yeniparola === '') {
        die('Kullanıcı adı ve parola boş olamaz.');
    }

    // Aynı kullanıcı adı var mı kontrol et
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM kullanicilar WHERE kullanici_adi = ?");
    $stmt->execute([$yeniKullanici]);
    if ($stmt->fetchColumn() > 0) {
        die('❌ Bu kullanıcı adı zaten mevcut.');
    }

    $hash = password_hash($yeniparola, PASSWORD_DEFAULT);
    $ekle = $pdo->prepare("INSERT INTO kullanicilar (kullanici_adi, parola_hash, admin) VALUES (?, ?, ?)");
    $ekle->execute([$yeniKullanici, $hash, $admin]);

    echo "✅ Kullanıcı başarıyla eklendi. <a href='login.php'>Giriş Sayfasına Dön</a>";
} else {
    header('Location: login.php');
    exit;
}
?>
