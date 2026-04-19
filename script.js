window.onload = function() {
    const modal = document.getElementById("capsuleModal");
    const openBtn = document.querySelector(".hero .btn-primary");
    const closeBtn = document.querySelector(".close-btn");

    if (openBtn && modal) {
        openBtn.onclick = function() {
            modal.style.display = "block";
        }
    }

    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    let sDay = 1;
    let sMonth = 1;
    let sYear = 2026;

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

            if(hiddenDateInput) {
                hiddenDateInput.value = `${sYear}-${mStr}-${dStr}`;
            }
        }

        document.getElementById("btnDayUp").onclick = () => { sDay = sDay >= 31 ? 1 : sDay + 1; updateDateDisplay(); };
        document.getElementById("btnDayDown").onclick = () => { sDay = sDay <= 1 ? 31 : sDay - 1; updateDateDisplay(); };

        document.getElementById("btnMonthUp").onclick = () => { sMonth = sMonth >= 12 ? 1 : sMonth + 1; updateDateDisplay(); };
        document.getElementById("btnMonthDown").onclick = () => { sMonth = sMonth <= 1 ? 12 : sMonth - 1; updateDateDisplay(); };

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

// Işınlan (Onay) butonuna basınca
document.getElementById("uzayOnay").onclick = () => {
    let yeniYil = parseInt(uzayInput.value);
    if (yeniYil >= 2026) {
        sYear = yeniYil;
        updateDateDisplay();
        uzayPrompt.style.display = "none"; // İş bitince kutuyu gizle
    } else {
        alert("Zaman paradoksu: 2026'dan daha geçmişe ışınlanılamaz!");
    }
};

// İptal butonuna basınca sadece gizle
document.getElementById("uzayIptal").onclick = () => {
    uzayPrompt.style.display = "none";
};

// Özellik 2: Rokete basılı tutarak ışık hızına çıkma
let roketMotoru;
const btnYearUp = document.getElementById("btnYearUp");

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

// Aşağı oku için normal tıklama (Geçmişe çok hızlı gitmesin, tek tek insin)
document.getElementById("btnYearDown").onclick = () => { 
    if(sYear > 2026) { sYear--; updateDateDisplay(); } 
};

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
       let timeString = "";
        if (years > 0) {
            timeString += `${years}y `;
        }
        timer.innerHTML = `${timeString}${days}g ${hours}s ${minutes}dk ${seconds}sn`;
    });
}

// Sayacı her saniye güncelle ve ilk açılışta hemen çalıştır
setInterval(updateTimers, 1000);
updateTimers();

    const capsuleForm = document.getElementById("capsuleForm");
    
    if (capsuleForm) {
        capsuleForm.onsubmit = function(e) {
            //e.preventDefault(); 
            
            const selectedDateStr = hiddenDateInput ? hiddenDateInput.value : "";
            const selectedDate = new Date(selectedDateStr).getTime();
            const now = new Date().getTime();

            if (selectedDate <= now) {
                alert("Lütfen gelecekteki bir tarih seçin! Geçmişe kapsül gönderemezsiniz.");
                return;
            }

            const title = document.getElementById("capsuleTitle").value;
            //alert("Kapsül Hazırlanıyor: " + title + "\nBackend bağlantısı bekleniyor...");
        };
    }
};