-- ============================================================
-- ÖRNEK VERİLER - database_backup.sql'den SONRA çalıştırın
-- Tüm demo kullanıcıların şifresi: 123456
-- ============================================================
USE zaman_kapsulu;

-- Demo Kullanıcılar (şifre: 123456)
INSERT INTO USERS (first_name, last_name, email, password_hash) VALUES
('Ahmet',   'Yılmaz',  'ahmet@demo.com',   '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'),
('Zeynep',  'Kaya',    'zeynep@demo.com',  '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'),
('Mert',    'Demir',   'mert@demo.com',    '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'),
('Elif',    'Sahin',   'elif@demo.com',    '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'),
('Can',     'Ozturk',  'can@demo.com',     '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'),
('Selin',   'Arslan',  'selin@demo.com',   '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'),
('Burak',   'Celik',   'burak@demo.com',   '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'),
('Ayse',    'Dogan',   'ayse@demo.com',    '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92');

-- ============================================================
-- Kapsüller (CAPSULES)
-- Ahmet: 23 kapsül → Kronik Seyyah ⚡
-- ============================================================
INSERT INTO CAPSULES (sender_id, title, target_date, status) VALUES
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Mezuniyet Mektubu', '2024-06-15', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Gelecekteki Kariyerim', '2027-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'İlk Evim', '2028-05-10', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Annemin Doğum Günü', '2024-03-08', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Arkadaşlara Veda', '2024-08-01', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), '5 Yıl Sonra Kendime', '2031-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Tatil Anıları 2024', '2024-09-01', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Yeni Yıl Dileklerim', '2025-01-01', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Evlilik Yıl Dönümü', '2030-07-14', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Çocuğuma Mektup', '2035-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Büyükannemin Tarifleri', '2024-11-10', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'İş Hayatı Başlangıcı', '2025-09-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Dünya Turu Hayalleri', '2029-06-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Emeklilik Planı', '2055-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Üniversite İlk Gün', '2024-10-05', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Spor Hedeflerim', '2026-12-31', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Kitap Önerileri', '2027-06-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Şehir Değiştiriyorum', '2026-08-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Kardeşime Sürpriz', '2026-04-15', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Müzik Listesi 2024', '2024-12-31', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Yeni Hobi: Fotoğraf', '2027-03-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'İlk Arabam', '2028-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='ahmet@demo.com'), 'Kod Yolculuğum', '2030-01-01', 'Locked');

-- Zeynep: 14 kapsül → Galaksi Kâşifi 🌌
INSERT INTO CAPSULES (sender_id, title, target_date, status) VALUES
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Lisans Bitişi', '2024-07-01', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Yurt Dışı Fırsatı', '2027-09-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Sevgiliye Sürpriz', '2026-02-14', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Anılar Kutusu', '2025-12-31', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Kariyer Planı 5 Yıl', '2031-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Tatil Albümü', '2024-09-15', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Yeni Ev Hayali', '2028-06-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Arkadaşlığımız 10. Yıl', '2034-05-20', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Doğum Günü Mesajı', '2025-03-25', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Staj Dönemi Anıları', '2024-08-30', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'İlk Maaş Anısı', '2025-07-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Aile Toplantısı 2025', '2025-08-15', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Seyahat Günlüğü', '2026-11-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='zeynep@demo.com'), 'Hayallerim Listesi', '2030-01-01', 'Locked');

-- Mert: 8 kapsül → Zaman Yolcusu 🛸
INSERT INTO CAPSULES (sender_id, title, target_date, status) VALUES
((SELECT user_id FROM USERS WHERE email='mert@demo.com'), 'Okul Bitişi', '2024-06-30', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='mert@demo.com'), 'Spor Hedefleri', '2026-12-31', 'Locked'),
((SELECT user_id FROM USERS WHERE email='mert@demo.com'), 'Teknoloji Tahminleri', '2030-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='mert@demo.com'), 'Kodlama Yolculuğu', '2027-06-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='mert@demo.com'), 'İlk Uygulamam', '2026-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='mert@demo.com'), 'Ekibe Katılış', '2025-09-15', 'Locked'),
((SELECT user_id FROM USERS WHERE email='mert@demo.com'), 'Kış Tatili 2024', '2024-12-27', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='mert@demo.com'), 'Startup Hayali', '2028-01-01', 'Locked');

-- Elif: 4 kapsül → Zaman Yolcusu 🛸
INSERT INTO CAPSULES (sender_id, title, target_date, status) VALUES
((SELECT user_id FROM USERS WHERE email='elif@demo.com'), 'Anneme Mektup', '2030-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='elif@demo.com'), 'Sevdiklerime', '2025-12-25', 'Locked'),
((SELECT user_id FROM USERS WHERE email='elif@demo.com'), 'Mezuniyet Anısı', '2024-07-10', 'Unlocked'),
((SELECT user_id FROM USERS WHERE email='elif@demo.com'), 'Yeni Yıl Kapsülü', '2026-01-01', 'Locked');

-- Can: 2 kapsül → Zaman Yolcusu 🛸
INSERT INTO CAPSULES (sender_id, title, target_date, status) VALUES
((SELECT user_id FROM USERS WHERE email='can@demo.com'), 'İlk Kapsülüm', '2027-01-01', 'Locked'),
((SELECT user_id FROM USERS WHERE email='can@demo.com'), 'Harika Anlar', '2024-11-01', 'Unlocked');

-- Selin: 1 kapsül → Zaman Yolcusu 🛸
INSERT INTO CAPSULES (sender_id, title, target_date, status) VALUES
((SELECT user_id FROM USERS WHERE email='selin@demo.com'), 'Geleceğe Selam', '2029-06-01', 'Locked');

-- Burak & Ayse: 0 kapsül → Zaman Çırağı 🥚 (boş)

-- ============================================================
-- İçerikler (CONTENTS) - Her kapsüle bir metin içeriği
-- ============================================================
INSERT INTO CONTENTS (capsule_id, content_type, text_body)
SELECT c.capsule_id, 'Text',
    CASE c.title
        WHEN 'Mezuniyet Mektubu'     THEN 'Sevgili gelecekteki ben, bugün mezun oldum. Yılların emeği sonuç verdi. Umuyorum ki şimdi harika bir yerdeysindir!'
        WHEN 'Gelecekteki Kariyerim' THEN '2027 yılında hangi şirkette çalışıyorum? Hedefim yazılım mühendisi olmak. Başardım mı?'
        WHEN 'İlk Evim'              THEN 'Belki 2028 yılında ilk evimi almış olurum. Nereden bakıyorsun şu an?'
        WHEN 'Annemin Doğum Günü'   THEN 'Anneme özel bu kapsülü sadece onun için kilitledi.'
        WHEN 'Arkadaşlara Veda'     THEN 'Şehir değiştiriyorum, arkadaşlarım burada kalıyor. Bir gün hepimiz bir araya geleceğiz.'
        WHEN 'Lisans Bitişi'        THEN 'Dört yıllık bir maratondu. Her şeye değdi. Gelecekteki Zeynep, gurur duyuyorum senden!'
        WHEN 'Okul Bitişi'          THEN 'Okul bitti, yeni bir sayfa açılıyor. Heyecanlıyım ve biraz da korkuyorum.'
        WHEN 'Mezuniyet Anısı'      THEN 'Tüm o gecelerin, derslerin, sınavların sonunda işte buradayım.'
        WHEN 'İlk Kapsülüm'         THEN 'Merhaba! Bu benim ilk zaman kapsülüm. Sistemi keşfetmek için harika bir yer.'
        WHEN 'Harika Anlar'         THEN 'Bugün harika şeyler yaşandı. Detayları unutan biri olarak bunu yazıyorum.'
        WHEN 'Geleceğe Selam'       THEN 'Merhaba, 2029 yılındaki Selin! Umarım her şey yolundadır.'
        ELSE CONCAT('Bu kapsül "', c.title, '" başlığıyla ', c.target_date, ' tarihinde açılmak üzere mühürlendi. İçindeki sürpriz seni bekliyor!')
    END
FROM CAPSULES c
JOIN USERS u ON c.sender_id = u.user_id
WHERE u.email IN ('ahmet@demo.com','zeynep@demo.com','mert@demo.com','elif@demo.com','can@demo.com','selin@demo.com')
  AND NOT EXISTS (SELECT 1 FROM CONTENTS ct WHERE ct.capsule_id = c.capsule_id);

-- ============================================================
-- Kategori Eşleştirmeleri (CAPSULE_CATEGORY)
-- ============================================================
INSERT INTO CAPSULE_CATEGORY (capsule_id, category_id)
SELECT c.capsule_id,
    CASE (c.capsule_id % 3)
        WHEN 0 THEN 1  -- Kişisel
        WHEN 1 THEN 2  -- Eğitim
        WHEN 2 THEN 3  -- Eğlence
    END
FROM CAPSULES c
JOIN USERS u ON c.sender_id = u.user_id
WHERE u.email IN ('ahmet@demo.com','zeynep@demo.com','mert@demo.com','elif@demo.com','can@demo.com','selin@demo.com')
  AND NOT EXISTS (SELECT 1 FROM CAPSULE_CATEGORY cc WHERE cc.capsule_id = c.capsule_id);

SELECT 'Demo verileri başarıyla yüklendi!' AS Sonuc;
SELECT u.first_name, u.last_name, COUNT(c.capsule_id) AS kapsul_sayisi
FROM USERS u LEFT JOIN CAPSULES c ON u.user_id = c.sender_id
WHERE u.email LIKE '%@demo.com'
GROUP BY u.user_id ORDER BY kapsul_sayisi DESC;
