document.addEventListener("DOMContentLoaded", function () {
    console.log("Requisition system loaded successfully.");
<<<<<<< HEAD
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
=======
});
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283
