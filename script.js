
const targetDate = new Date("Dec 31, 2026 23:59:59").getTime();

const countdownTask = setInterval(function() {
    const now = new Date().getTime();
    const distance = targetDate - now;

   
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    
    document.getElementById("timer1").innerHTML = days + "g " + hours + "s " + minutes + "dk " + seconds + "sn ";

    
    if (distance < 0) {
        clearInterval(countdownTask);
        document.getElementById("timer1").innerHTML = "KAPSÜL AÇILDI!";
    }
}, 1000);
// Modal elemanlarını seçelim
const modal = document.getElementById("capsuleModal");
const openBtn = document.querySelector(".hero .btn-primary"); // Hero'daki buton
const closeBtn = document.querySelector(".close-btn");

// Butona basınca modalı aç
openBtn.onclick = function() {
    modal.style.display = "block";
}

// Çarpıya basınca kapat
closeBtn.onclick = function() {
    modal.style.display = "none";
}

// Dışarı tıklayınca kapat
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
// --- 3. FORM GÖNDERME KONTROLÜ ---
const capsuleForm = document.getElementById("capsuleForm");

if (capsuleForm) {
    capsuleForm.onsubmit = function(e) {
        e.preventDefault(); // Sayfanın yenilenmesini (refresh) engeller
        
        // Formdaki başlığı alalım
        const title = document.getElementById("capsuleTitle").value;
        
        // Şimdilik sadece bir uyarı mesajı gösterelim
        alert("Kapsül Hazırlanıyor: " + title + "\nBackend bağlantısı henüz kurulmadı.");
    };
}
const unlockDateInput = document.getElementById("unlockDate");

if (unlockDateInput) {
    // Takvimi açtığında "en erken bugün"ü seçebilmesi için minimum tarih belirle
    const now = new Date().toISOString().slice(0, 16);
    unlockDateInput.min = now;
}

// Form gönderilirken tarih kontrolü
capsuleForm.onsubmit = function(e) {
    const selectedDate = new Date(unlockDateInput.value).getTime();
    const now = new Date().getTime();

    if (selectedDate <= now) {
        e.preventDefault();
        alert("Lütfen gelecekteki bir tarih seçin! Geçmişe kapsül gönderemezsiniz.");
        return;
    }
    
    // ... diğer alert kodun buraya gelebilir ...
};