<?php include 'db.php'; ?>

<link rel="stylesheet" href="assets/style.css">

<a href="index.php" class="anasayfa-button">🏠 Ana Sayfa</a>

<h2>📋 Tarih Aralığında İşlem Listesi</h2>

<?php
$today = date("Y-m-d");
$first_day_of_month = date("Y-m-01");
$last_day_of_month = date("Y-m-t");
?>

<form method="GET" action="rapor_liste.php">
    <label for="baslangic">Başlangıç:</label>
    <input type="date" name="baslangic" required value="<?= $_GET['baslangic'] ?? $first_day_of_month; ?>">

    <label for="bitis">Bitiş:</label>
    <input type="date" name="bitis" required value="<?= $_GET['bitis'] ?? $last_day_of_month; ?>">

    <label>
        <input type="checkbox" name="sadece_odenmemisler" value="1" <?= isset($_GET['sadece_odenmemisler']) ? 'checked' : '' ?>>
        Sadece ödenmemişler
    </label>

    <label for="etiket">Etikete Göre Filtrele:</label>
    <select name="etiket" id="etiket">
        <option value="">-- Hepsi --</option>
        <?php
        $etiketler = $pdo->query("SELECT DISTINCT etiket FROM islemler WHERE etiket IS NOT NULL")->fetchAll();
        foreach ($etiketler as $etiket) {
            $secili = ($_GET['etiket'] ?? '') == $etiket['etiket'] ? 'selected' : '';
            echo "<option value='" . htmlspecialchars($etiket['etiket']) . "' $secili>" . htmlspecialchars($etiket['etiket']) . "</option>";
        }
        ?>
    </select>

    <button type="submit">Listele</button>
</form>

<?php if (isset($_GET['baslangic']) && isset($_GET['bitis'])): ?>
    <form id="odemeForm" method="POST">
        <input type="hidden" name="baslangic" value="<?= htmlspecialchars($_GET['baslangic']) ?>">
        <input type="hidden" name="bitis" value="<?= htmlspecialchars($_GET['bitis']) ?>">
        <input type="hidden" name="sadece_odenmemisler" value="<?= isset($_GET['sadece_odenmemisler']) ? 1 : 0 ?>">
        <input type="hidden" name="etiket" value="<?= htmlspecialchars($_GET['etiket'] ?? '') ?>">

        <div style="margin: 10px 0;">
            <button type="submit" formaction="toplu_odeme_yap.php" style="background-color: green; color: white; padding: 10px;">🧾 Ödeme Yap</button>
            <button type="submit" formaction="toplu_odeme_kaldir.php" style="background-color: darkred; color: white; padding: 10px; margin-left:10px;">↩️ Ödemeyi Geri Al</button>
        </div>

        <table border='1' cellpadding='5' cellspacing='0'>
            <tr>
                <th>Seç</th>
                <th>Tarih</th>
                <th>Ödeme Tarihi</th>
                <th>Tutar</th>
                <th>Açıklama</th>
                <th>Etiket</th>
                <th>Sil</th>
            </tr>
            <?php
            $sql = "SELECT * FROM islemler WHERE tarih BETWEEN ? AND ?";
            $params = [$_GET['baslangic'], $_GET['bitis']];

            if (isset($_GET['sadece_odenmemisler'])) $sql .= " AND odendi = 0";
            if (!empty($_GET['etiket'])) {
                $sql .= " AND etiket = ?";
                $params[] = $_GET['etiket'];
            }
            $sql .= " ORDER BY tarih ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $toplamGelir = 0;
            $toplamGider = 0;

            while ($row = $stmt->fetch()):
                $rowClass = $row['odendi'] ? 'class="odendi"' : '';
                $odemeTarihi = $row['odeme_tarihi'] ?? '-';
                $miktar = $row['miktar'];
                if ($miktar > 0) $toplamGelir += $miktar;
                else $toplamGider += $miktar;
            ?>
                <tr <?= $rowClass ?>>
                    <td>
                        <input type="checkbox" class="sec-checkbox" name="secili_idler[]" value="<?= $row['id'] ?>" data-miktar="<?= $miktar ?>">
                    </td>
                    <td><?= $row['tarih'] ?></td>
                    <td><?= $row['odendi'] ? $odemeTarihi : '-' ?></td>
                    <td><?= number_format($miktar, 2, ',', '.') ?> ₺</td>
                    <td><?= $row['aciklama'] ?></td>
                    <td><?= htmlspecialchars($row['etiket']) ?></td>
                    <td>
                        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?')" style="color:red;">🗑️</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <div style='font-size:18px; font-weight:bold;'>
            💰 Toplam Gelir: <span style='color:green;'><?= number_format($toplamGelir, 2, ',', '.') ?> ₺</span><br>
            💸 Toplam Gider: <span style='color:red;'><?= number_format(abs($toplamGider), 2, ',', '.') ?> ₺</span>
        </div>
        <div style="margin-top:20px; font-size:18px; font-weight:bold;">
            📌 Seçili İşlemlerin Toplamı: <span id="seciliToplam">0.00 ₺</span>
        </div>
    </form>
<?php endif; ?>

<script>
document.querySelectorAll('.sec-checkbox').forEach(function(cb) {
    cb.addEventListener('change', hesaplaSeciliToplam);
});

function hesaplaSeciliToplam() {
    let toplam = 0;
    document.querySelectorAll('.sec-checkbox:checked').forEach(function(cb) {
        toplam += parseFloat(cb.dataset.miktar);
    });
    document.getElementById('seciliToplam').textContent = new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(toplam);
}
</script>