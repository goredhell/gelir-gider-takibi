<?php
include 'db.php';

$currentYear = date('Y');
$startYear = $currentYear + 0;
$endYear = $currentYear + 10;

// Verileri çek
$veriler = [];
for ($y = $startYear; $y <= $endYear; $y++) {
    for ($m = 1; $m <= 12; $m++) {
        $ay = str_pad($m, 2, '0', STR_PAD_LEFT);
        $baslangic = "$y-$ay-01";
        $bitis = date("Y-m-t", strtotime($baslangic));

        $stmt = $pdo->prepare("SELECT SUM(miktar) as toplam FROM islemler WHERE tarih BETWEEN ? AND ?");
        $stmt->execute([$baslangic, $bitis]);
        $toplam = $stmt->fetchColumn();
        $veriler[$y][$m] = $toplam ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>📊 Aylık Rapor</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<a href="index.php" class="anasayfa-button">🏠 Ana Sayfa</a>
<h2>📅 10 Yıllık Aylık Gelir/Gider Tablosu</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Ay</th>
        <?php for ($y = $startYear; $y <= $endYear; $y++): ?>
            <th><?= $y ?></th>
        <?php endfor; ?>
    </tr>
    <?php
    $aylar = [
        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
        5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
        9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
    ];

    foreach ($aylar as $m => $ayAdi): ?>
        <tr>
            <td><strong><?= $ayAdi ?></strong></td>
            <?php for ($y = $startYear; $y <= $endYear; $y++):
                $deger = round($veriler[$y][$m] ?? 0);
                $renk = $deger >= 0 ? 'green' : 'red';
            ?>
                <td style="color: <?= $renk ?>; text-align: right;">
                    <?= number_format($deger, 0, ',', '.') ?> ₺
                </td>
            <?php endfor; ?>
        </tr>
    <?php endforeach; ?>
</table>

<h2 style="margin-top:50px;">📈 Grafiksel Özet (Son 12 Ay)</h2>
<canvas id="gelirGiderChart" width="1200" height="400"></canvas>

<script>
const ctx = document.getElementById('gelirGiderChart').getContext('2d');

const labels = [];
const dataValues = [];

<?php
// Son 12 ay için verileri alalım
$grafikData = [];
for ($i = 11; $i >= 0; $i--) {
    $tarih = strtotime("-$i months");
    $yil = date("Y", $tarih);
    $ay = date("n", $tarih);
    $etiket = date("M Y", $tarih);
    $deger = round($veriler[$yil][$ay] ?? 0);
    $grafikData[] = ['label' => $etiket, 'value' => $deger];
}
?>

<?php foreach ($grafikData as $d): ?>
    labels.push("<?= $d['label'] ?>");
    dataValues.push(<?= $d['value'] ?>);
<?php endforeach; ?>

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Aylık Toplam',
            data: dataValues,
            backgroundColor: dataValues.map(v => v >= 0 ? 'rgba(0, 128, 0, 0.7)' : 'rgba(200, 0, 0, 0.7)'),
            borderColor: dataValues.map(v => v >= 0 ? 'green' : 'red'),
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('tr-TR') + ' ₺';
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>
