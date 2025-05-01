<?php include 'db.php'; ?>

<link rel="stylesheet" href="assets/style.css">

<!-- Ana Sayfa Butonu -->
<a href="index.php" class="anasayfa-button">🏠 Ana Sayfa</a>

<h2>📊 Aylık Rapor</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Ay</th>
        <?php 
        $yil = date('Y');
        for ($i = $yil; $i < $yil + 10; $i++) {
            echo "<th>$i</th>";
        }
        ?>
    </tr>

<?php
$aylar = [
    1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
    5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
    9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
];

foreach ($aylar as $num => $isim) {
    echo "<tr>";
    echo "<td><b>$isim</b></td>";

    for ($y = $yil; $y < $yil + 10; $y++) {
        $start = "$y-" . str_pad($num, 2, '0', STR_PAD_LEFT) . "-01";
        $end = date("Y-m-t", strtotime($start));

        $stmt = $pdo->prepare("SELECT SUM(miktar) as toplam FROM islemler WHERE tarih BETWEEN ? AND ?");
        $stmt->execute([$start, $end]);
        $toplam = $stmt->fetch()['toplam'] ?? 0;

        $renk = ($toplam >= 0) ? 'green' : 'red';
        $toplam = number_format(round($toplam), 0, ',', '.');

        echo "<td style='color:$renk;'>$toplam ₺</td>";
    }

    echo "</tr>";
}
?>
</table>
