document.querySelectorAll(".nav-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {

        e.stopPropagation(); // prevent closing instantly

        let menu = this.nextElementSibling;

        document.querySelectorAll(".dropdown").forEach(drop => {
            if (drop !== menu) {
                drop.style.display = "none";
            }
        });

        menu.style.display = menu.style.display === "block" ? "none" : "block";
    });
});

document.addEventListener("click", function() {
    document.querySelectorAll(".dropdown").forEach(menu => {
        menu.style.display = "none";
    });
});