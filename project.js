document.addEventListener("DOMContentLoaded", function() {
    // Example of dynamic update (Can be fetched from API later)
    let donorsCount = document.getElementById("donorsCount");
    let bloodUnits = document.getElementById("bloodUnits");

    setInterval(() => {
        donorsCount.textContent = (parseInt(donorsCount.textContent) + Math.floor(Math.random() * 10)).toLocaleString();
        bloodUnits.textContent = (parseInt(bloodUnits.textContent) + Math.floor(Math.random() * 5)).toLocaleString();
    }, 3000);
});