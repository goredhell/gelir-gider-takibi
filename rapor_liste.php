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

<?php
if (isset($_GET['baslangic']) && isset($_GET['bitis'])) {
    $sql = "SELECT * FROM islemler WHERE tarih BETWEEN ? AND ?";
    $params = [$_GET['baslangic'], $_GET['bitis']];

    if (isset($_GET['sadece_odenmemisler'])) {
        $sql .= " AND odendi = 0";
    }

    if (!empty($_GET['etiket'])) {
        $sql .= " AND etiket = ?";
        $params[] = $_GET['etiket'];
    }

    $sql .= " ORDER BY tarih ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if (isset($_GET['sadece_odenmemisler'])) {
        echo "<p style='color:red; font-weight:bold;'>❗ Şu anda sadece ÖDENMEMİŞ kayıtlar listeleniyor.</p>";
    }
    if (!empty($_GET['etiket'])) {
        echo "<p style='color:blue; font-weight:bold;'>🔎 Etiket filtresi aktif: " . htmlspecialchars($_GET['etiket']) . "</p>";
    }

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Ödendi</th><th>Tarih</th><th>Ödeme Tarihi</th><th>Tutar</th><th>Açıklama</th><th>Etiket</th><th>Sil</th></tr>";

    $total_income = 0;
    $total_expense = 0;

    while ($row = $stmt->fetch()) {
        $checked = $row['odendi'] ? 'checked' : '';
        $rowClass = $row['odendi'] ? 'class="odendi"' : '';
        $odemeTarihi = $row['odeme_tarihi'] ?? '-';

        echo "<tr $rowClass>";
        echo "<td><input type='checkbox' class='odendi-checkbox' data-id='{$row['id']}' $checked></td>";
        echo "<td>{$row['tarih']}</td>";
        echo "<td>" . ($row['odendi'] ? $odemeTarihi : '-') . "</td>";
        echo "<td>{$row['miktar']} ₺</td>";
        echo "<td>{$row['aciklama']}</td>";
        echo "<td>" . htmlspecialchars($row['etiket']) . "</td>";
        echo "<td><a href='delete.php?id={$row['id']}' onclick='return confirm(\"Bu kaydı silmek istediğinize emin misiniz?\")' style='color:red;'>🗑️</a></td>";
        echo "</tr>";

        if ($row['miktar'] > 0) $total_income += $row['miktar'];
        else $total_expense += $row['miktar'];
    }

    echo "</table><br>";
    echo "<div style='font-size:18px; font-weight:bold;'>";
    echo "💰 Toplam Gelir: <span style='color:green;'>" . number_format($total_income, 2, ',', '.') . " ₺</span><br>";
    echo "💸 Toplam Gider: <span style='color:red;'>" . number_format(abs($total_expense), 2, ',', '.') . " ₺</span>";
    echo "</div>";
}
?>

<script>
// Checkbox değişince veritabanına kaydet
document.querySelectorAll('.odendi-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        let id = this.getAttribute('data-id');
        let odendi = this.checked ? 1 : 0;

        fetch('update_odendi.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id + '&odendi=' + odendi
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            location.reload(); // Güncellemeden sonra yenile
        });
    });
});
</script>
