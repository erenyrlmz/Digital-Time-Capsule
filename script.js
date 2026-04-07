
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