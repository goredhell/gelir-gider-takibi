<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Varsayılan kullanıcıyı ekle (sadece bir kez)
//$defaultUser = 'admin';
//$defaultPass = 'admin';
//$check = $pdo->prepare("SELECT COUNT(*) FROM kullanicilar WHERE kullanici_adi = ?");
//$check->execute([$defaultUser]);

//if ($check->fetchColumn() == 0) {
//    $hash = password_hash($defaultPass, PASSWORD_DEFAULT);
//    $insert = $pdo->prepare("INSERT INTO kullanicilar (kullanici_adi, parola_hash) VALUES (?, ?)");
//    $insert->execute([$defaultUser, $hash]);
//}

// Kullanıcı ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['yeni_kullanici'], $_POST['yeni_parola'])) {
    $yeniKullanici = trim($_POST['yeni_kullanici']);
    $yeniparola = $_POST['yeni_parola'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM kullanicilar WHERE kullanici_adi = ?");
    $stmt->execute([$yeniKullanici]);
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash($yeniparola, PASSWORD_DEFAULT);
        $ekle = $pdo->prepare("INSERT INTO kullanicilar (kullanici_adi, parola_hash) VALUES (?, ?)");
        $ekle->execute([$yeniKullanici, $hash]);
    }
}

// Kullanıcı silme
if (isset($_GET['sil'])) {
    $kullaniciSil = $_GET['sil'];
    if (1 == 1) { // Silinmesini engellemek istediğimiz kullanıcı varsa buraya tanımlanabilir.
        $sil = $pdo->prepare("DELETE FROM kullanicilar WHERE kullanici_adi = ?");
        $sil->execute([$kullaniciSil]);
    }
}

// Parola değiştirme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['degistir_kullanici'], $_POST['yeni_parola_degistir'])) {
    $degistirKullanici = $_POST['degistir_kullanici'];
    $yeniparolaDegistir = $_POST['yeni_parola_degistir'];
    $hash = password_hash($yeniparolaDegistir, PASSWORD_DEFAULT);
    $guncelle = $pdo->prepare("UPDATE kullanicilar SET parola_hash = ? WHERE kullanici_adi = ?");
    $guncelle->execute([$hash, $degistirKullanici]);
}

?>

<link rel="stylesheet" href="assets/style.css">

<style>
    .form-container {
        max-width: 400px;
        margin: 40px auto;
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
    table {
        margin: 30px auto;
        border-collapse: collapse;
        width: 80%;
    }
    table th, table td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
    }
</style>

<h2 style="text-align:center;">👤 Kullanıcı Oluştur</h2>
<div class="form-container">
    <form method="POST">
        <div class="form-row">
            <label for="yeni_kullanici">Yeni Kullanıcı:</label>
            <input type="text" name="yeni_kullanici" id="yeni_kullanici" required>
        </div>
        <div class="form-row">
            <label for="yeni_parola">Yeni Parola:</label>
            <input type="password" name="yeni_parola" id="yeni_parola" required>
        </div>
        <button type="submit">Kullanıcı Ekle</button>
    </form>
</div>

<h2 style="text-align:center;">📋 Mevcut Kullanıcılar</h2>
<table>
    <tr>
        <th>Kullanıcı Adı</th>
        <th>Parola Değiştir</th>
        <th>Sil</th>
    </tr>
    <?php
    $users = $pdo->query("SELECT kullanici_adi FROM kullanicilar")->fetchAll();
    foreach ($users as $user) {
        $kadi = htmlspecialchars($user['kullanici_adi']);
        echo "<tr>";
        echo "<td>$kadi</td>";
        echo "<td>
            <form method='POST' style='display:flex; gap:5px; justify-content:center;'>
                <input type='hidden' name='degistir_kullanici' value='$kadi'>
                <input type='password' name='yeni_parola_degistir' placeholder='Yeni Parola' required>
                <button type='submit'>Değiştir</button>
            </form>
        </td>";
        if (1 == 1) {
            echo "<td><a href='?sil=$kadi' onclick='return confirm(\"$kadi adlı kullanıcı silinsin mi?\")'>❌ Sil</a></td>";
        } else {
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
    ?>
</table>
