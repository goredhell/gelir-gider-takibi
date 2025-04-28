<?php
session_start();

$kullaniciAdi = 'admin';
$sifre = 'admin';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['kullanici'] == $kullaniciAdi && $_POST['sifre'] == $sifre) {
        $_SESSION['user'] = $kullaniciAdi;
        header('Location: index.php');
        exit;
    } else {
        $hata = "Kullanıcı adı veya şifre yanlış!";
    }
}
?>

<link rel="stylesheet" href="assets/style.css">

<h2>🔐 Giriş Yap</h2>
<form method="POST">
    Kullanıcı Adı: <input type="text" name="kullanici" required><br>
    Şifre: <input type="password" name="sifre" required><br>
    <button type="submit">Giriş</button>
    <?php if (isset($hata)) echo "<p style='color:red;'>$hata</p>"; ?>
</form>
