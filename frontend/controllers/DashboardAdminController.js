class DashboardAdminController {
    constructor() {
        this.BASE_PATH = '/app-gestion-parking';
        this.parkingTypesChart = null;
        this.init();
    }

    init() {
        window.addEventListener('load', () => {
            this.setupChart();
            this.loadData();
            this.startAutoRefresh();
        });
    }

    setupChart() {
        const ctx = document.getElementById('parkingTypesChart');
        if (ctx) {
            this.parkingTypesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#3b82f6',
                            '#22c55e',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    }

    async loadData() {
        try {
            await Promise.all([
                this.loadStats(),
                this.loadChartData()
            ]);
        } catch (error) {
            console.error('Erreur:', error);
            // Gérer l'erreur visuellement
            this.showError('Erreur lors du chargement des données');
        }
    }

    async loadStats() {
        const response = await fetch(`${this.BASE_PATH}/api/admin/stats`);
        const data = await response.json();

        if (data.success) {
            this.updateStats(data.data);
        } else {
            console.error('Erreur API:', data.message);
        }
    }

    async loadChartData() {
        const response = await fetch(`${this.BASE_PATH}/api/admin/chart-data`);
        const data = await response.json();

        if (data.success) {
            this.updateChart(data.data);
        } else {
            console.error('Erreur API:', data.message);
        }
    }

    updateStats(stats) {
        document.getElementById('totalReservations').textContent = stats.totalReservations || '0';
        document.getElementById('availableSpots').textContent = stats.availableSpots || '0';
        document.getElementById('activeUsers').textContent = stats.activeUsers || '0';
        document.getElementById('pendingReservations').textContent = stats.pendingReservations || '0';
    }

    updateChart(data) {
        if (this.parkingTypesChart && data.types) {
            this.parkingTypesChart.data.labels = data.types.map(item => item.type_place);
            this.parkingTypesChart.data.datasets[0].data = data.types.map(item => item.count);
            this.parkingTypesChart.update();
        }
    }

    showError(message) {
        const stats = ['totalReservations', 'availableSpots', 'activeUsers', 'pendingReservations'];
        stats.forEach(id => {
            document.getElementById(id).textContent = '-';
        });
    }

    startAutoRefresh() {
        setInterval(() => this.loadData(), 5 * 60 * 1000);
    }
}

new DashboardAdminController();