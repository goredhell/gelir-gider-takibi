<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM islemler WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: rapor_liste.php?baslangic=" . $_GET['baslangic'] . "&bitis=" . $_GET['bitis']);
    exit;
}
?>
