<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$oturumKullanici = $_SESSION['user'];
?>

<link rel="stylesheet" href="assets/style.css">

<h1>💵 Kişisel Finans Takip Sistemi</h1>

<div class="menu-container">

    <div class="menu-card">
        <a href="kayit_ekle.php">
            <div class="menu-icon">➕</div>
            <div class="menu-text">Yeni Kayıt Ekle</div>
        </a>
    </div>

    <div class="menu-card">
        <a href="rapor_liste.php">
            <div class="menu-icon">📋</div>
            <div class="menu-text">Kayıtları Listele</div>
        </a>
    </div>

    <div class="menu-card">
        <a href="rapor_aylik.php">
            <div class="menu-icon">📈</div>
            <div class="menu-text">Aylık Rapor</div>
        </a>
    </div>

    <?php if ($oturumKullanici === 'admin'): ?>
    <div class="menu-card">
        <a href="user_panel.php">
            <div class="menu-icon">👥</div>
            <div class="menu-text">Kullanıcı Paneli</div>
        </a>
    </div>
    <?php endif; ?>

    <div class="menu-card">
        <a href="logout.php">
            <div class="menu-icon">🚪</div>
            <div class="menu-text">Çıkış Yap</div>
        </a>
    </div>

</div>
