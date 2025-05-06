<?php
session_start();
include 'db.php';

// Sadece admin kullanıcı erişebilsin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header('Location: index.php');
    exit;
}

// Kullanıcı silme işlemi
if (isset($_GET['sil'])) {
    $sil = $_GET['sil'];
    $stmt = $pdo->prepare("DELETE FROM kullanicilar WHERE kullanici_adi = ?");
    $stmt->execute([$sil]);
    header("Location: user_panel.php");
    exit;
}

// Kullanıcı parola değiştirme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['degistir_kullanici'], $_POST['yeni_parola_degistir'])) {
    $kadi = $_POST['degistir_kullanici'];
    $yeniParola = $_POST['yeni_parola_degistir'];
    $hash = password_hash($yeniParola, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE kullanicilar SET parola_hash = ? WHERE kullanici_adi = ?");
    $stmt->execute([$hash, $kadi]);
    header("Location: user_panel.php");
    exit;
}

// Yeni kullanıcı ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['yeni_kullanici'], $_POST['yeni_parola'])) {
    $yeniKullanici = trim($_POST['yeni_kullanici']);
    $yeniparola = $_POST['yeni_parola'];
    $adminMi = isset($_POST['admin_mi']) ? 1 : 0;

    if ($yeniKullanici !== '' && $yeniparola !== '') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM kullanicilar WHERE kullanici_adi = ?");
        $stmt->execute([$yeniKullanici]);
        if ($stmt->fetchColumn() == 0) {
            $hash = password_hash($yeniparola, PASSWORD_DEFAULT);
            $ekle = $pdo->prepare("INSERT INTO kullanicilar (kullanici_adi, parola_hash, admin) VALUES (?, ?, ?)");
            $ekle->execute([$yeniKullanici, $hash, $adminMi]);
        }
    }
    header("Location: user_panel.php");
    exit;
}

// Admin güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_guncelle'])) {
    $kullaniciAdi = $_POST['admin_guncelle'];
    $adminDurumu = isset($_POST['admin']) ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE kullanicilar SET admin = ? WHERE kullanici_adi = ?");
    $stmt->execute([$adminDurumu, $kullaniciAdi]);
    header("Location: user_panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Paneli</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        form { display: inline-block; }
        .form-row {
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-row label {
            min-width: 100px;
            font-weight: bold;
        }
        .form-row input[type="text"],
        .form-row input[type="password"] {
            flex: 1;
            padding: 8px;
        }
        button {
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<h2>👤 Yeni Kullanıcı Ekle</h2>
<form method="POST">
    <div class="form-row">
        <label for="yeni_kullanici">Yeni Kullanıcı:</label>
        <input type="text" name="yeni_kullanici" id="yeni_kullanici" required>
    </div>
    <div class="form-row">
        <label for="yeni_parola">Yeni Parola:</label>
        <input type="password" name="yeni_parola" id="yeni_parola" required>
    </div>
    <div class="form-row">
        <label for="admin_mi">Admin mi?</label>
        <input type="checkbox" name="admin_mi" id="admin_mi">
    </div>
    <button type="submit">➕ Kullanıcı Ekle</button>
</form>

<h2>📋 Mevcut Kullanıcılar</h2>
<table>
    <tr>
        <th>Kullanıcı Adı</th>
        <th>Admin mi?</th>
        <th>Parola Değiştir</th>
        <th>Sil</th>
    </tr>
    <?php
    $users = $pdo->query("SELECT kullanici_adi, admin FROM kullanicilar")->fetchAll();
    foreach ($users as $user) {
        $kadi = htmlspecialchars($user['kullanici_adi']);
        $adminMi = $user['admin'] ? 'checked' : '';

        echo "<tr>";
        echo "<td>$kadi</td>";
        echo "<td>
            <form method='POST'>
                <input type='hidden' name='admin_guncelle' value='$kadi'>
                <input type='checkbox' name='admin' onchange='this.form.submit()' $adminMi>
            </form>
        </td>";
        echo "<td>
            <form method='POST' style='display:flex; gap:5px; justify-content:center;'>
                <input type='hidden' name='degistir_kullanici' value='$kadi'>
                <input type='password' name='yeni_parola_degistir' placeholder='Yeni Parola' required>
                <button type='submit'>Değiştir</button>
            </form>
        </td>";
        echo "<td><a href='?sil=$kadi' onclick='return confirm(\"$kadi adlı kullanıcı silinsin mi?\")'>❌ Sil</a></td>";
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>
