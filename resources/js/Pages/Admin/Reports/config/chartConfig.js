export const chartConfig = {
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: 'currentColor',
                    font: { size: 12 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                padding: 10,
                displayColors: false
            }
        },
        scales: {
            x: {
                ticks: { color: 'currentColor' },
                grid: { color: 'rgba(128, 128, 128, 0.2)' }
            },
            y: {
                ticks: { color: 'currentColor' },
                grid: { color: 'rgba(128, 128, 128, 0.2)' }
            }
        }
    },
    colors: {
        primary: '#3B82F6',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444'
    }
};
