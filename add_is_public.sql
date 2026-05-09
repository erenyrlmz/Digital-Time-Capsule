-- CAPSULES tablosuna gizlilik kolonu ekle
-- phpMyAdmin veya MySQL konsolundan çalıştırın

USE zaman_kapsulu;

ALTER TABLE CAPSULES
    ADD COLUMN `is_public` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT '0 = Gizli (sadece sahip görür), 1 = Herkese Açık'
    AFTER `status`;

-- Mevcut kapsülleri varsayılan olarak gizli bırak (0)
-- İsteğe bağlı: Mevcut açık kapsülleri herkese açık yap
-- UPDATE CAPSULES SET is_public = 1 WHERE status = 'Unlocked';

SELECT 'is_public kolonu başarıyla eklendi.' AS Sonuc;
DESCRIBE CAPSULES;
