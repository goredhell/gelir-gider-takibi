<?php include 'db.php'; ?>

<link rel="stylesheet" href="assets/style.css">

<!-- Ana Sayfa Butonu -->
<a href="index.php" class="anasayfa-button">🏠 Ana Sayfa</a>

<h2>📋 Tarih Aralığında İşlem Listesi</h2>

<form method="GET">
    Başlangıç: <input type="date" name="baslangic" required value="<?php echo $_GET['baslangic'] ?? ''; ?>">
    Bitiş: <input type="date" name="bitis" required value="<?php echo $_GET['bitis'] ?? ''; ?>">
    <label>
        <input type="checkbox" name="sadece_odenmemisler" value="1" <?php if (isset($_GET['sadece_odenmemisler'])) echo 'checked'; ?>>
        Sadece ödenmemişler
    </label>
    <button type="submit">Listele</button>
</form>

<?php
if (isset($_GET['baslangic']) && isset($_GET['bitis'])) {
    $sql = "SELECT * FROM islemler WHERE tarih BETWEEN ? AND ?";
    $params = [$_GET['baslangic'], $_GET['bitis']];

    if (isset($_GET['sadece_odenmemisler'])) {
        $sql .= " AND odendi = 0";
    }

    $sql .= " ORDER BY tarih ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Küçük Bonus: Eğer filtre aktifse uyarı mesajı göster
    if (isset($_GET['sadece_odenmemisler'])) {
        echo "<p style='color:red; font-weight:bold;'>❗ Şu anda sadece ÖDENMEMİŞ kayıtlar listeleniyor.</p>";
    }

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Ödendi</th><th>Tarih</th><th>Tutar</th><th>Açıklama</th></tr>";

    while ($row = $stmt->fetch()) {
        $checked = $row['odendi'] ? 'checked' : '';
        $rowClass = $row['odendi'] ? 'class="odendi"' : '';

        echo "<tr $rowClass>";
        echo "<td><input type='checkbox' class='odendi-checkbox' data-id='{$row['id']}' $checked></td>";
        echo "<td>{$row['tarih']}</td>";
        echo "<td>{$row['miktar']} ₺</td>";
        echo "<td>{$row['aciklama']}</td>";
        echo "</tr>";
    }
    echo "</table>";
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
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id + '&odendi=' + odendi
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            location.reload(); // Sayfayı yenile ki checkbox ve satır rengi güncellensin
        });
    });
});
</script>
