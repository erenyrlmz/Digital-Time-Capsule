document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("capsuleModal");
    const openBtns = document.querySelectorAll(".open-modal-btn");
    const closeBtn = document.querySelector(".close-btn");

    if (openBtns.length > 0 && modal) {
        openBtns.forEach(btn => {
            btn.onclick = function (e) {
                e.preventDefault();
                modal.style.display = "block";
                setTimeout(() => modal.classList.add("show"), 10);
            }
        });
    }

    if (closeBtn) {
        closeBtn.onclick = function () {
            modal.classList.remove("show");
            setTimeout(() => modal.style.display = "none", 300);
        }
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.classList.remove("show");
            setTimeout(() => modal.style.display = "none", 300);
        }
    }

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1); // Varsayılan olarak yarını seç

    let sDay = tomorrow.getDate();
    let sMonth = tomorrow.getMonth() + 1;
    let sYear = tomorrow.getFullYear();

    const dispDay = document.getElementById("dispDay");
    const dispMonth = document.getElementById("dispMonth");
    const dispYear = document.getElementById("dispYear");
    const hiddenDateInput = document.getElementById("unlockDate");

    if (dispDay && dispMonth && dispYear) {
        function updateDateDisplay() {
            let dStr = sDay < 10 ? "0" + sDay : sDay;
            let mStr = sMonth < 10 ? "0" + sMonth : sMonth;

            dispDay.innerText = dStr;
            dispMonth.innerText = mStr;
            dispYear.innerText = sYear;

            if (hiddenDateInput) {
                hiddenDateInput.value = `${sYear}-${mStr}-${dStr}`;
            }
        }

        if (document.getElementById("btnDayUp")) {
            document.getElementById("btnDayUp").onclick = () => { sDay = sDay >= 31 ? 1 : sDay + 1; updateDateDisplay(); };
            document.getElementById("btnDayDown").onclick = () => { sDay = sDay <= 1 ? 31 : sDay - 1; updateDateDisplay(); };
            document.getElementById("btnMonthUp").onclick = () => { sMonth = sMonth >= 12 ? 1 : sMonth + 1; updateDateDisplay(); };
            document.getElementById("btnMonthDown").onclick = () => { sMonth = sMonth <= 1 ? 12 : sMonth - 1; updateDateDisplay(); };
        }

        // --- YIL BUTONLARI VE YENİ UZAY SIÇRAMASI (WARP) ÖZELLİĞİ ---

        // Özellik 1: Yıl rakamına tıklayıp klavyeden hızlıca yazabilme (Hack mode)
        dispYear.style.cursor = "pointer";
        dispYear.title = "Klavye ile hızlıca yazmak için tıkla!";
        // --- SIKICI PROMPT YERİNE ÖZEL UZAY KUTUSUNU AÇMA ---
        const uzayPrompt = document.getElementById("uzayPrompt");
        const uzayInput = document.getElementById("uzayInput");

        dispYear.style.cursor = "pointer";
        dispYear.title = "Klavye ile yıl girmek için tıkla!";

        // Yıla tıklayınca bizim neon kutu açılsın
        dispYear.onclick = () => {
            uzayPrompt.style.display = "block";
            uzayInput.value = sYear; // Mevcut yılı kutuya yaz
            uzayInput.focus(); // İmleci direkt kutuya al
        };

        // Yıl warp butonu ve onay/iptal kontrolleri (sadece modal varsa)
        if (uzayPrompt && uzayInput && document.getElementById("uzayOnay")) {
            // Işınlan (Onay) butonuna basınca
            document.getElementById("uzayOnay").onclick = () => {
                let yeniYil = parseInt(uzayInput.value);
                if (yeniYil >= 2026) {
                    sYear = yeniYil;
                    updateDateDisplay();
                    uzayPrompt.style.display = "none";
                } else {
                    alert("Zaman paradoksu: 2026'dan daha geçmişe ışınlanılamaz!");
                }
            };

            // İptal butonuna basınca sadece gizle
            document.getElementById("uzayIptal").onclick = () => {
                uzayPrompt.style.display = "none";
            };
        }

        // Özellik 2: Rokete basılı tutarak ışık hızına çıkma
        let roketMotoru;
        const btnYearUp = document.getElementById("btnYearUp");

        if (btnYearUp) {
            // Tıklama başladığında (Mouse tuşuna basınca)
            btnYearUp.onmousedown = () => {
                sYear++; updateDateDisplay(); // Normal 1 kere artır

                // Eğer basılı tutmaya devam ederse, saniyede 10 kere (100ms) artır
                roketMotoru = setInterval(() => {
                    sYear++;
                    updateDateDisplay();
                }, 100);
            };

            // Mouse tuşunu bırakınca veya fare roketten kayınca motoru durdur
            btnYearUp.onmouseup = () => clearInterval(roketMotoru);
            btnYearUp.onmouseleave = () => clearInterval(roketMotoru);
        }

        // Aşağı oku için normal tıklama
        const btnYearDown = document.getElementById("btnYearDown");
        if (btnYearDown) {
            btnYearDown.onclick = () => {
                if (sYear > 2026) { sYear--; updateDateDisplay(); }
            };
        }

        updateDateDisplay();
    }

    function updateTimers() {
        // 1. Sayfadaki tüm timer divlerini bul (Sadece timer1'i değil, hepsini!)
        const timers = document.querySelectorAll('.timer');

        timers.forEach(timer => {
            // 2. Tarihi sabit bir yazıdan değil, PHP'nin karta gizlediği data-date'den al
            const targetDate = new Date(timer.getAttribute('data-date')).getTime();
            const now = new Date().getTime();
            const distance = targetDate - now;

            // Süre bittiyse durdur
            if (distance < 0) {
                timer.innerHTML = "KAPSÜL AÇILDI!";
                return;
            }

            // Matematiksel hesaplamalar (Arkadaşının yazdığı kısımlar birebir aynı)
            const years = Math.floor(distance / (1000 * 60 * 60 * 24 * 365));
            const days = Math.floor((distance % (1000 * 60 * 60 * 24 * 365)) / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Ekrana yazdır
            let timeHTML = "";
            if (years > 0) {
                timeHTML += `<div class="timer-block"><span class="timer-val">${years}</span><span class="timer-lbl">YIL</span></div>`;
            }
            timeHTML += `<div class="timer-block"><span class="timer-val">${days}</span><span class="timer-lbl">GÜN</span></div>`;
            timeHTML += `<div class="timer-block"><span class="timer-val">${hours}</span><span class="timer-lbl">SAAT</span></div>`;
            timeHTML += `<div class="timer-block"><span class="timer-val">${minutes}</span><span class="timer-lbl">DK</span></div>`;
            timeHTML += `<div class="timer-block"><span class="timer-val">${seconds}</span><span class="timer-lbl">SN</span></div>`;

            timer.innerHTML = timeHTML;
        });
    }

    // Sayacı her saniye güncelle ve ilk açılışta hemen çalıştır
    setInterval(updateTimers, 1000);
    updateTimers();

    const capsuleForm = document.getElementById("capsuleForm");

    if (capsuleForm) {
        capsuleForm.onsubmit = function (e) {
            //e.preventDefault(); 

            const selectedDateStr = hiddenDateInput ? hiddenDateInput.value : "";
            const selectedDate = new Date(selectedDateStr).getTime();
            const now = new Date().getTime();

            if (selectedDate <= now) {
                alert("Lütfen gelecekteki bir tarih seçin! Geçmişe kapsül gönderemezsiniz.");
                return false;
            }

            const title = document.getElementById("capsuleTitle").value;
            //alert("Kapsül Hazırlanıyor: " + title + "\nBackend bağlantısı bekleniyor...");
        };
    }

    // AJAX Silme İşlemi (Sil Butonları)
    const deleteBtns = document.querySelectorAll('.delete-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const capsuleId = this.getAttribute('data-id');
            if (confirm("Bu kapsülü zaman çizelgesinden tamamen silmek istediğine emin misin?")) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('capsule_id', capsuleId);

                fetch('../backend/ajax_islemler.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Kartı sayfadan kaldır (Küçülerek yok olma animasyonu)
                            const card = this.closest('.capsule-card');
                            card.style.transform = 'scale(0)';
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 400);
                        } else {
                            alert("Hata: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("AJAX Hatası:", error);
                        alert("Kapsül silinirken bir iletişim hatası oluştu.");
                    });
            }
        });
    });

    // YENİ: Satır İçi (Inline) Kapsül Açılışı İçin JavaScript
    const openInlineBtns = document.querySelectorAll('.open-inline-btn');
    const closeInlineBtns = document.querySelectorAll('.close-inline-btn');

    openInlineBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const contentPanel = document.getElementById(targetId);
            const card = this.closest('.capsule-card');

            if (contentPanel) {
                // Diğer açık olanları kapat (Opsiyonel)
                document.querySelectorAll('.inline-capsule-content.show').forEach(el => {
                    if (el.id !== targetId) {
                        el.classList.remove('show');
                        if (el.closest('.capsule-card')) {
                            el.closest('.capsule-card').classList.remove('is-expanded');
                        }
                    }
                });

                // Tıklananı aç/kapat
                contentPanel.classList.toggle('show');
                if (card) {
                    card.classList.toggle('is-expanded');
                }
            }
        });
    });

    closeInlineBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const contentPanel = document.getElementById(targetId);
            const card = this.closest('.capsule-card');

            if (contentPanel) {
                contentPanel.classList.remove('show');
                if (card) {
                    card.classList.remove('is-expanded');
                }
            }
        });
    });

});