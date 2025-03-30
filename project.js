document.addEventListener("DOMContentLoaded", function () {

    // Dark Mode Toggle
    const darkModeToggle = document.getElementById("darkModeToggle");
    const body = document.body;

    if (localStorage.getItem("dark-mode") === "enabled") {
        body.classList.add("dark-mode");
        darkModeToggle.textContent = "Light Mode";
    }

    darkModeToggle.addEventListener("click", function () {
        body.classList.toggle("dark-mode");
        localStorage.setItem("dark-mode", body.classList.contains("dark-mode") ? "enabled" : "disabled");
        darkModeToggle.textContent = body.classList.contains("dark-mode") ? "Light Mode" : "Dark Mode";
    });

    // Auto Slide Carousel
    new bootstrap.Carousel(document.getElementById('carouselExampleIndicators'), {
        interval: 3000,
        wrap: true
    });

});

document.addEventListener("DOMContentLoaded", function () {

    // 🔍 Live Search in Blood Bank List
    document.getElementById("searchBloodBank").addEventListener("input", function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#bloodBankTable tbody tr");

        rows.forEach(row => {
            let bankName = row.cells[0].textContent.toLowerCase();
            let location = row.cells[1].textContent.toLowerCase();
            row.style.display = (bankName.includes(filter) || location.includes(filter)) ? "" : "none";
        });
    });

    // ✔ Blood Donation Eligibility Checker
    document.getElementById("checkEligibility").addEventListener("click", function () {
        let age = parseInt(document.getElementById("eligibilityAge").value);
        let weight = parseInt(document.getElementById("eligibilityWeight").value);
        let result = document.getElementById("eligibilityResult");

        if (age >= 18 && weight >= 50) {
            result.textContent = "✔ You are eligible to donate blood!";
            result.style.color = "green";
        } else {
            result.textContent = "❌ You are NOT eligible to donate blood.";
            result.style.color = "red";
        }
    });

    // 🩸 Donor Registration Form Submission
    document.getElementById("donorForm").addEventListener("submit", function (event) {
        event.preventDefault();
        let name = document.getElementById("fullName").value;
        let age = document.getElementById("age").value;
        let bloodGroup = document.getElementById("bloodGroup").value;
        let successMessage = document.getElementById("successMessage");

        if (name && age >= 18 && bloodGroup) {
            successMessage.style.display = "block";
            successMessage.textContent = "✔ Registration Successful!";
            successMessage.style.color = "green";
            successMessage.style.opacity = "1";

            setTimeout(() => {
                successMessage.style.opacity = "0";
                successMessage.style.display = "none";
            }, 3000);

            this.reset();
        } else {
            alert("Please fill all fields correctly. Age must be 18 or above.");
        }
    });

    // 🌙 Dark Mode Toggle with Local Storage
    const darkModeToggle = document.getElementById("darkModeToggle");
    const body = document.body;

    // Check if dark mode was previously enabled
    if (localStorage.getItem("dark-mode") === "enabled") {
        body.classList.add("dark-mode");
        darkModeToggle.textContent = "Light Mode";
    }

    darkModeToggle.addEventListener("click", function () {
        body.classList.toggle("dark-mode");

        if (body.classList.contains("dark-mode")) {
            localStorage.setItem("dark-mode", "enabled");
            this.textContent = "Light Mode";
        } else {
            localStorage.setItem("dark-mode", "disabled");
            this.textContent = "Dark Mode";
        }
    });

});

