<?php
include 'db.php';

if (isset($_POST['id']) && isset($_POST['odendi'])) {
    $stmt = $pdo->prepare("UPDATE islemler SET odendi = ? WHERE id = ?");
    $stmt->execute([$_POST['odendi'], $_POST['id']]);
    echo "Başarılı";
} else {
    echo "Eksik veri";
}
?>
