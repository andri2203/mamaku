import Chart from "chart.js/auto";

export function initDashboard() {
    console.log("Dashboard.js is loaded");
    document.addEventListener("alpine:init", () => {
        Alpine.data("pieChart", (trendData = []) => ({
            chart: null,
            data: trendData["data"],
            labels: trendData["labels"],
            renderChart() {
                const ctx = document
                    .getElementById("myPieChart")
                    .getContext("2d");
                this.chart = new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: this.labels,
                        datasets: [
                            {
                                data: this.data,
                                backgroundColor: [
                                    "#4ade80",
                                    "#60a5fa",
                                    "#f87171",
                                    "#facc15",
                                ],
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: "bottom" } },
                    },
                });
            },
        }));
    });
}
