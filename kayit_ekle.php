<?php include 'db.php'; ?>

<link rel="stylesheet" href="assets/style.css">

<!-- Ana Sayfa Butonu -->
<a href="index.php" class="anasayfa-button">🏠 Ana Sayfa</a>

<h2>➕ Yeni Gelir/Gider Kaydı</h2>

<form method="POST">
    Tutar (₺): <input type="number" step="0.01" name="miktar" required><br>
    Tarih: <input type="date" name="tarih" required><br>
    Açıklama: <input type="text" name="aciklama"><br>
    <button type="submit">Kaydet</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO islemler (miktar, tarih, aciklama) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['miktar'],
        $_POST['tarih'],
        $_POST['aciklama']
    ]);
    echo "<p style='color:green;'>✅ Kayıt eklendi!</p>";
}
?>
