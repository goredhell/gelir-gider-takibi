<?php include 'db.php'; ?>
<link rel="stylesheet" href="assets/style.css">

<a href="index.php" class="anasayfa-button">🏠 Ana Sayfa</a>

<h2>➕ Yeni Gelir/Gider Kaydı</h2>

<!-- Tekli Kayıt Formu -->
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

    <button type="submit" name="tekli_kaydet">Kaydet</button>
</form>

<!-- Çoklu Kayıt Formu -->
<hr style="margin: 40px 0;">
<h2>📦 Çoklu Kayıt Ekle</h2>

<form method="POST">
    <label for="toplam_tutar">Tutar (₺):</label>
    <input type="number" step="0.01" name="toplam_tutar" required placeholder="Her taksit tutarı">

    <label for="aciklama_multi">Açıklama:</label>
    <input type="text" name="aciklama_multi" placeholder="Açıklama girin">

    <label for="etiket_multi">Etiket:</label>
    <input type="text" name="etiket_multi" required placeholder="Etiket girin">

    <label for="baslangic_tarihi">Başlangıç:</label>
    <input type="date" name="baslangic_tarihi" required value="<?php echo date('Y-m-d'); ?>">

    <label for="frekans">Tekrar Sıklığı:</label>
    <select name="frekans" required>
        <option value="daily">Günlük</option>
        <option value="weekly">Haftalık</option>
        <option value="monthly" selected>Aylık</option>
        <option value="yearly">Yıllık</option>
    </select>

    <label for="tekrar_sayisi">Tekrar Sayısı:</label>
    <input type="number" name="tekrar_sayisi" min="1" required placeholder="Kaç kez tekrarlansın?">

    <label>
        <input type="checkbox" name="odendi_multi" value="1"> Ödendi
    </label><br>

    <button type="submit" name="coklu_kaydet">Çoklu Kayıt Ekle</button>
</form>

<?php
// Tekli kayıt işlemi
if (isset($_POST['tekli_kaydet'])) {
    $odeme_tarihi = isset($_POST['odendi']) ? date('Y-m-d') : null;

    $stmt = $pdo->prepare("INSERT INTO islemler (miktar, tarih, aciklama, etiket, odeme_tarihi) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['miktar'],
        $_POST['tarih'],
        $_POST['aciklama'],
        $_POST['etiket'],
        $odeme_tarihi
    ]);

    echo "<p style='color:green;'>✅ Tekli kayıt eklendi!</p>";
}

// Çoklu kayıt işlemi
if (isset($_POST['coklu_kaydet'])) {
    $tutar = floatval($_POST['toplam_tutar']);
    $aciklama = $_POST['aciklama_multi'];
    $etiket = $_POST['etiket_multi'];
    $baslangicTarihi = $_POST['baslangic_tarihi'];
    $frekans = $_POST['frekans'];
    $tekrarSayisi = intval($_POST['tekrar_sayisi']);
    $odeme_tarihi = isset($_POST['odendi_multi']) ? date('Y-m-d') : null;

    if ($tekrarSayisi > 0) {
        $tarih = new DateTime($baslangicTarihi);
        $stmt = $pdo->prepare("INSERT INTO islemler (miktar, tarih, aciklama, etiket, odeme_tarihi) VALUES (?, ?, ?, ?, ?)");

        for ($i = 1; $i <= $tekrarSayisi; $i++) {
            $aciklamaTaksitli = $aciklama . " ($i/$tekrarSayisi)";
            $stmt->execute([
                $tutar,
                $tarih->format('Y-m-d'),
                $aciklamaTaksitli,
                $etiket,
                $odeme_tarihi
            ]);

            // Tarihi artır
            switch ($frekans) {
                case 'daily':   $tarih->modify('+1 day'); break;
                case 'weekly':  $tarih->modify('+1 week'); break;
                case 'monthly': $tarih->modify('+1 month'); break;
                case 'yearly':  $tarih->modify('+1 year'); break;
            }
        }

        echo "<p style='color:green;'>✅ $tekrarSayisi adet $tutar ₺ tutarında kayıt eklendi!</p>";
    } else {
        echo "<p style='color:red;'>❌ Geçerli bir tekrar sayısı girin.</p>";
    }
}
?>
