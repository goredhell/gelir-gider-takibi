<?php
session_start();
include 'db.php'; // Veritabanı bağlantı dosyası

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici = $_POST['kullanici'] ?? '';
    $parola = $_POST['parola'] ?? '';

    $stmt = $pdo->prepare("SELECT parola_hash FROM kullanicilar WHERE kullanici_adi = ?");
    $stmt->execute([$kullanici]);
    $kullaniciVerisi = $stmt->fetch();

    if ($kullaniciVerisi && password_verify($parola, $kullaniciVerisi['parola_hash'])) {
        $_SESSION['user'] = $kullanici;
        header('Location: index.php');
        exit;
    } else {
        $hata = "Kullanıcı adı veya parola yanlış!";
    }
}
?>

<link rel="stylesheet" href="assets/style.css">

<style>
    .form-container {
        max-width: 400px;
        margin: 100px auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .form-row {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .form-row label {
        width: 120px;
        font-weight: bold;
    }

    .form-row input {
        flex: 1;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form-container button {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .form-container button:hover {
        background-color: #0056b3;
    }

    .error {
        color: red;
        text-align: center;
        margin-top: 10px;
    }
</style>

<h2 style="text-align:center;">🔐 Giriş Yap</h2>

<div class="form-container">
    <form method="POST">
        <div class="form-row">
            <label for="kullanici">Kullanıcı Adı:</label>
            <input type="text" name="kullanici" id="kullanici" required>
        </div>
        <div class="form-row">
            <label for="parola">Parola:</label>
            <input type="password" name="parola" id="parola" required>
        </div>
        <button type="submit">Giriş</button>
        <?php if (isset($hata)) echo "<p class='error'>$hata</p>"; ?>
    </form>
</div>
