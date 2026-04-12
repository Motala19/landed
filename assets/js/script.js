document.addEventListener("DOMContentLoaded", function () {
    console.log("Requisition system loaded successfully.");
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 690686211b371f09f123f2c50ef3abae6750df65
});


document.querySelectorAll('.dashboard-card').forEach(card => {
    card.addEventListener('click', function () {

        // Remove active from all cards
        document.querySelectorAll('.dashboard-card').forEach(c => {
            c.classList.remove('active-card');
        });

        // Add active to clicked card
        this.classList.add('active-card');
    });
});
<<<<<<< HEAD


document.addEventListener("DOMContentLoaded", function () {

    if (requisitionStatus !== "Pending Finance") {

        // Show alert message
        let alertBox = document.createElement("div");
        alertBox.className = "alert alert-warning";
        alertBox.innerText = "This requisition has already been processed.";

        document.querySelector(".card").prepend(alertBox);

        // Disable buttons
        let buttons = document.querySelectorAll("button[type='submit']");
        buttons.forEach(btn => {
            btn.disabled = true;
        });

        // Optional: disable dropdown
        let select = document.querySelector("select[name='budget_check']");
        if (select) select.disabled = true;
    }

});
=======
=======
});
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283
>>>>>>> 690686211b371f09f123f2c50ef3abae6750df65
