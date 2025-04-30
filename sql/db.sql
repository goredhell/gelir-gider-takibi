CREATE DATABASE IF NOT EXISTS odemeler_db;
USE odemeler_db;

CREATE TABLE IF NOT EXISTS islemler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    miktar DECIMAL(10,2) NOT NULL,
    tarih DATE NOT NULL,
    aciklama VARCHAR(255)
);

ALTER TABLE islemler ADD COLUMN odendi TINYINT(1) DEFAULT 0;

ALTER TABLE islemler ADD COLUMN etiket VARCHAR(100) DEFAULT NULL;

CREATE TABLE kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
    sifre_hash TEXT NOT NULL
);

INSERT INTO kullanicilar (kullanici_adi, parola_hash) VALUES ("admin", "$2y$10$sJBFIkAj7EWY0X9OSajomOFIB7Lo/DAgVVKu4fGoWqfjGLN0LHSj.")

ALTER TABLE islemler ADD COLUMN odeme_tarihi DATE DEFAULT NULL;