// Show current date & time
document.getElementById("datetime").textContent = new Date().toLocaleString();


function toggleMode() {
    document.body.classList.toggle("dark-mode");
}
