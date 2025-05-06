<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Admin kontrolü (int olarak kontrol edilir)
$isAdmin = (int) ($_SESSION['admin'] ?? 0) === 1;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Finans Takip Sistemi</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .container h1 {
            margin-bottom: 30px;
        }

        .button {
            display: block;
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .admin-link {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>📊 Finans Takip Sistemi</h1>
    <p>Hoş geldin, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>!</p>

    <a href="rapor_liste.php" class="button">📁 Kayıtları Listele</a>
    <a href="kayit_ekle.php" class="button">➕ Yeni Kayıt Ekle</a>
    <a href="rapor_aylik.php" class="button">📅 Aylık Özet</a>

    <?php if ($isAdmin): ?>
        <div class="admin-link">
            <a href="user_panel.php" class="button">👥 Kullanıcı Yönetimi</a>
        </div>
    <?php endif; ?>

    <a href="logout.php" class="button" style="background-color: #dc3545;">🚪 Çıkış Yap</a>
</div>

</body>
</html>
