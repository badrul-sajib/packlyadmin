export const initOrderStatusChart = () => {
    const chartElement = document.querySelector('#chartOrderStatus');
    if (!chartElement) return;

    const options = {
        series: [
            {
                name: 'Pending',
                data: [42, 65, 30, 47, 40, 70, 65, 90, 138, 195],
            },
            {
                name: 'Processing',
                data: [3, 2, 2, 4, 2, 12, 3, 5, 13, 22],
            },
            {
                name: 'Delivered',
                data: [145, 170, 110, 165, 153, 230, 142, 65, 10, 5],
            },
            {
                name: 'Cancelled',
                data: [72, 42, 28, 40, 47, 60, 47, 55, 35, 32],
            },
        ],
        colors: ['#465fff', '#f59e0b', '#22c55e', '#ef4444'],
        chart: {
            fontFamily: 'Outfit, sans-serif',
            type: 'bar',
            height: 350,
            toolbar: {
                show: false,
            },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 3,
                borderRadiusApplication: 'end',
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent'],
        },
        xaxis: {
            categories: [
                '31 Mar',
                '01 Apr',
                '02 Apr',
                '03 Apr',
                '04 Apr',
                '05 Apr',
                '06 Apr',
                '07 Apr',
                '08 Apr',
                '09 Apr',
            ],
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'center',
            fontFamily: 'Outfit',
            markers: {
                radius: 99,
            },
        },
        yaxis: {
            title: {
                text: 'Number of Orders',
                style: {
                    fontSize: '13px',
                    fontWeight: 500,
                    fontFamily: 'Outfit, sans-serif',
                },
            },
        },
        grid: {
            yaxis: {
                lines: {
                    show: true,
                },
            },
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + ' orders';
                },
            },
        },
    };

    const chart = new ApexCharts(chartElement, options);
    chart.render();

    return chart;
};

export default initOrderStatusChart;
