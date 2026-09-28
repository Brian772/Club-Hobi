import ApexCharts from "apexcharts";
import { stroke } from "apexcharts/unit-shapes";

document.addEventListener("turbo:load", function () {
    const options = {
        chart: {
            type: "area",
            height: 350,
            zoom: {
                autoScaleYaxis: true,
            },
        },
        stroke: {
            curve: "smooth",
        },
        dataLabels: {
            enabled: false,
        },
        markers: {
            size: 0,
            style: 'hollow',
        },
        series: [],
        xaxis: {
            categories: [],
        },
        color: ["#476cff"],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.6,
                opacityTo: 0.9,
                stops: [0, 90, 100],
            },
        },
    };
    const chartElement = document.querySelector("#userChart");
    if (chartElement) {
        const chart = new ApexCharts(chartElement, options);
        chart.render();

        const apiUrl = chartElement.getAttribute("data-url");
        fetch(apiUrl)
            .then((response) => response.json())
            .then((data) => {
                chart.updateSeries([
                    {
                        name: "Jumlah Pengguna",
                        data: data.total_users,
                    },
                ]);
                chart.updateOptions({
                    xaxis: {
                        categories: data.months,
                    },
                });
            })
            .catch((error) =>
                console.error("Error fetching chart data:", error),
            );
    }
});
