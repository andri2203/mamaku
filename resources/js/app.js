import "./bootstrap";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

Alpine.plugin(collapse);
window.Alpine = Alpine;

async function boot() {
    // Cek apakah ada elemen dashboard
    if (document.querySelector("#dashboard-page")) {
        const module = await import("./dashboard.js");
        module.initDashboard();
    }

    Alpine.start();
}

boot();
