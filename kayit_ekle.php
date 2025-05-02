<?php include 'db.php'; ?>

<link rel="stylesheet" href="assets/style.css">

<a href="index.php" class="anasayfa-button">🏠 Ana Sayfa</a>

<h2>➕ Yeni Gelir/Gider Kaydı</h2>

<form method="POST">
    <div class="form-grid">
        
            <label for="miktar">Tutar (₺):</label>
            <input type="number" step="0.01" name="miktar" required placeholder="Tutar girin">
          
            <label for="tarih">Tarih:</label>
            <input type="date" name="tarih" required value="<?php echo date('Y-m-d'); ?>">
        
            <label for="aciklama">Açıklama:</label>
            <input type="text" name="aciklama" placeholder="Açıklama girin">
        
            <label for="etiket">Etiket:</label>
            <input type="text" name="etiket" required placeholder="Etiket girin">
  
			<label for="odendi">Ödendi:</label>
            <input type="checkbox" name="odendi" value="1">
      
    </div>
    <button type="submit">Kaydet</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['miktar'])) {
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

<hr>

<h3>📦 Çoklu Kayıt Ekle</h3>

<form method="POST">
    <div class="form-grid">
        
            <label for="miktar_multi">Tutar (₺):</label>
            <input type="number" step="0.01" name="miktar_multi" required placeholder="Her taksit tutarı">  
        
            <label for="aciklama_multi">Açıklama:</label>
            <input type="text" name="aciklama_multi" placeholder="Açıklama girin">
             
            <label for="etiket_multi">Etiket:</label>
            <input type="text" name="etiket_multi" required placeholder="Etiket girin">
        
            <label for="baslangic_tarihi">Başlangıç:</label>
            <input type="date" name="baslangic_tarihi" required value="<?php echo date('Y-m-d'); ?>">
        
            <label for="siklik">Tekrar Sıklığı:</label>
            <select name="siklik">
                <option value="daily">Günlük</option>
                <option value="weekly">Haftalık</option>
                <option value="monthly" selected>Aylık</option>
                <option value="yearly">Yıllık</option>
            </select>
        
            <label for="tekrar_sayisi">Tekrar Sayısı:</label>
            <input type="number" name="tekrar_sayisi" placeholder="Kaç kez tekrar edilsin?" required>
        
			<label for="odendi">Ödendi:</label>
            <input type="checkbox" name="odendi_multi" value="1">
        
    </div>
    <button type="submit" name="multi_submit">Çoklu Kayıt Ekle</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['multi_submit'])) {
    $miktar = $_POST['miktar_multi'];
    $aciklama = $_POST['aciklama_multi'];
    $etiket = $_POST['etiket_multi'];
    $baslangic = new DateTime($_POST['baslangic_tarihi']);
    $siklik = $_POST['siklik'];
    $adet = (int)$_POST['tekrar_sayisi'];
    $odeme_tarihi = isset($_POST['odendi_multi']) ? date('Y-m-d') : null;

    for ($i = 1; $i <= $adet; $i++) {
        $taksit_aciklama = $aciklama . " ({$i}/{$adet})";
        $stmt = $pdo->prepare("INSERT INTO islemler (miktar, tarih, aciklama, etiket, odeme_tarihi) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $miktar,
            $baslangic->format('Y-m-d'),
            $taksit_aciklama,
            $etiket,
            $odeme_tarihi
        ]);

        switch ($siklik) {
            case 'daily': $baslangic->modify('+1 day'); break;
            case 'weekly': $baslangic->modify('+1 week'); break;
            case 'monthly': $baslangic->modify('+1 month'); break;
            case 'yearly': $baslangic->modify('+1 year'); break;
        }
    }
    echo "<p style='color:green;'>✅ Çoklu kayıtlar başarıyla eklendi!</p>";
}
?>
