<?php include 'db.php'; ?>

<link rel="stylesheet" href="assets/style.css">

<h2>📋 Tarih Aralığında İşlem Listesi</h2>

<form method="GET">
    Başlangıç: <input type="date" name="baslangic" required>
    Bitiş: <input type="date" name="bitis" required>
    <button type="submit">Listele</button>
</form>

<?php
if (isset($_GET['baslangic']) && isset($_GET['bitis'])) {
    $stmt = $pdo->prepare("SELECT * FROM islemler WHERE tarih BETWEEN ? AND ? ORDER BY tarih ASC");
    $stmt->execute([$_GET['baslangic'], $_GET['bitis']]);

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
            location.reload(); // sayfayı yenile ki renkler güncellensin
        });
    });
});
</script>
