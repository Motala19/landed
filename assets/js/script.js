document.addEventListener("DOMContentLoaded", function () {
    console.log("Requisition system loaded successfully.");

    // Card click expansion
    document.querySelectorAll('.dashboard-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.dashboard-card').forEach(c => c.classList.remove('active-card'));
            this.classList.add('active-card');
        });
    });

    // Collapse events expansion
    document.querySelectorAll('.collapse').forEach(collapseEl => {
        collapseEl.addEventListener('show.bs.collapse', function () {
            document.querySelectorAll('.dashboard-card').forEach(c => c.classList.remove('active-card'));
            const card = document.querySelector('[data-bs-target="#' + this.id + '"]');
            if (card) card.classList.add('active-card');
        });

        collapseEl.addEventListener('hide.bs.collapse', function () {
            const card = document.querySelector('[data-bs-target="#' + this.id + '"]');
            if (card) card.classList.remove('active-card');
        });
    });

    // Example: requisition status check
    if (typeof requisitionStatus !== "undefined" && requisitionStatus !== "Pending Finance") {
        let alertBox = document.createElement("div");
        alertBox.className = "alert alert-warning";
        alertBox.innerText = "This requisition has already been processed.";
        document.querySelector(".card").prepend(alertBox);

        document.querySelectorAll("button[type='submit']").forEach(btn => btn.disabled = true);
        let select = document.querySelector("select[name='budget_check']");
        if (select) select.disabled = true;
    }

    // Form validation
    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", function(e) {
            let action = document.activeElement.value;
            let rejectReason = document.querySelector("[name='reject_reason']").value;
            if (action === "reject" && rejectReason.trim() === "") {
                e.preventDefault();
                alert("Rejection reason is required");
            }
        });
    }
});
