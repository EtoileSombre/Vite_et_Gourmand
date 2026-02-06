/* Gestion des graphiques Chart.js pour les statistiques */

/**
 * Initialise le graphique des commandes par menu
 */
function initCommandesParMenuChart(data) {
    const ctx = document.getElementById('chartCommandesParMenu');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Nombre de Commandes',
                    data: data.commandes,
                    backgroundColor: 'rgba(139, 21, 56, 0.7)',
                    borderColor: 'rgba(139, 21, 56, 1)',
                    borderWidth: 2,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#8B1538',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#DAA520',
                    borderWidth: 2,
                    callbacks: {
                        label: function(context) {
                            return 'Commandes: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

/**
 * Initialise le graphique du CA par menu
 */
function initCAChart(data) {
    const ctx = document.getElementById('chartCA');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'CA TTC (€)',
                    data: data.data,
                    backgroundColor: 'rgba(218, 165, 32, 0.7)',
                    borderColor: 'rgba(218, 165, 32, 1)',
                    borderWidth: 2,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#8B1538',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#DAA520',
                    borderWidth: 2,
                    callbacks: {
                        label: function(context) {
                            const ca = context.parsed.y.toLocaleString('fr-FR', { 
                                style: 'currency', 
                                currency: 'EUR' 
                            });
                            const nbCommandes = data.commandes[context.dataIndex];
                            return [
                                'CA TTC: ' + ca,
                                'Commandes: ' + nbCommandes
                            ];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' €';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

