/* Gestion des graphiques Chart.js pour la page de statistiques*/

/**
 * Initialise tous les graphiques de la page stats
 */
function initStatsCharts(chartData) {
    console.log('Initialisation des graphiques avec:', chartData);
    
    // Graphique des menus les plus consultés (Doughnut)
    if (chartData.menus && chartData.menus.data && chartData.menus.data.length > 0) {
        initMenusChart(chartData.menus);
    }
    
    // Graphique Commandes par Menu (Bar Chart comparatif)
    if (chartData.commandesParMenu && chartData.commandesParMenu.commandes && chartData.commandesParMenu.commandes.length > 0) {
        initCommandesChart(chartData.commandesParMenu);
    }
    
    // Graphique du CA par menu (Bar horizontal)
    if (chartData.ca && chartData.ca.data && chartData.ca.data.length > 0) {
        initCAChart(chartData.ca);
    }
}

/**
 * Initialise le graphique des menus les plus consultés
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
 *Initialise le graphique Commandes par Menu (Bar)
 */
function initCommandesChart(data) {
    const ctx = document.getElementById('chartCommandes');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Nombre de Commandes',
                    data: data.commandes,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Total Personnes',
                    data: data.personnes,
                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Comparaison des Commandes par Menu (MongoDB)',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            }
        }
    });
}

/**
 * Initialise le graphique du CA par menu (Bar horizontal)
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
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
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
                title: {
                    display: true,
                    text: 'Chiffre d\'Affaires par Menu (MongoDB)',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const ca = context.parsed.x.toLocaleString('fr-FR', {
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
