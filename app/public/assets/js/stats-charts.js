/**
 * Gestion des graphiques Chart.js pour la page de statistiques
 * Séparation du JavaScript de la vue PHP pour respecter les bonnes pratiques MVC
 */

/**
 * Initialise tous les graphiques de la page stats
 */
function initStatsCharts(chartData) {
    // Graphique des menus les plus consultés (Doughnut)
    if (chartData.menus && chartData.menus.data && chartData.menus.data.length > 0) {
        initMenusChart(chartData.menus);
    }
    
    // Graphique de l'évolution des commandes (Line)
    if (chartData.commandes && chartData.commandes.data && chartData.commandes.data.length > 0) {
        initCommandesChart(chartData.commandes);
    }
    
    // Graphique du CA par menu (Bar)
    if (chartData.ca && chartData.ca.data && chartData.ca.data.length > 0) {
        initCAChart(chartData.ca);
    }
}

/**
 * Initialise le graphique des menus les plus consultés (Doughnut/Pie)
 */
function initMenusChart(data) {
    const ctx = document.getElementById('chartMenus');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                backgroundColor: data.colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: false
                }
            }
        }
    });
}

/**
 * Initialise le graphique de l'évolution des commandes (Line)
 */
function initCommandesChart(data) {
    const ctx = document.getElementById('chartCommandes');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Nombre de commandes',
                data: data.data,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}

/**
 * Initialise le graphique du CA par menu (Bar)
 */
function initCAChart(data) {
    const ctx = document.getElementById('chartCA');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Chiffre d\'Affaires (€)',
                data: data.data,
                backgroundColor: data.colors.slice(0, data.labels.length),
                borderColor: data.colors.slice(0, data.labels.length).map(c => c.replace('0.8', '1')),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' €';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const ca = context.parsed.y.toLocaleString('fr-FR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            const nbCmd = data.commandes[context.dataIndex];
                            return [
                                'CA : ' + ca + ' €',
                                'Commandes : ' + nbCmd
                            ];
                        }
                    }
                }
            }
        }
    });
}
