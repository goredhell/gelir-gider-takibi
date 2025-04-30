<?php include 'db.php'; ?>

<link rel="stylesheet" href="assets/style.css">

<!-- Ana Sayfa Butonu -->
<a href="index.php" class="anasayfa-button">🏠 Ana Sayfa</a>

<h2>➕ Yeni Gelir/Gider Kaydı</h2>

<form method="POST">
    <label for="miktar">Tutar (₺):</label>
    <input type="number" step="0.01" name="miktar" required placeholder="Tutar girin">
    
    <label for="tarih">Tarih:</label>
    <input type="date" name="tarih" required value="<?php echo date('Y-m-d'); ?>">
    
    <label for="aciklama">Açıklama:</label>
    <input type="text" name="aciklama" placeholder="Açıklama girin">
    
    <label for="etiket">Etiket:</label>
    <input type="text" name="etiket" id="etiket" required placeholder="Etiket girin">
	
    <label>
        <input type="checkbox" name="odendi" value="1"> Ödendi
    </label><br>

    <button type="submit">Kaydet</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $odeme_tarihi = isset($_POST['odendi']) ? date('Y-m-d') : null;

    $stmt = $pdo->prepare("INSERT INTO islemler (miktar, tarih, aciklama, etiket, odeme_tarihi) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['miktar'],
        $_POST['tarih'],
        $_POST['aciklama'],
        $_POST['etiket'],
        $odeme_tarihi
    ]);

    echo "<p style='color:green;'>✅ Kayıt eklendi!</p>";
}
?>
