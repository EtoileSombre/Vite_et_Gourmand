/* Gestion des graphiques Chart.js pour les statistiques */

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('stats-container');
    if (container && container.dataset.chartData) {
        try {
            const chartData = JSON.parse(container.dataset.chartData);
            initStatsCharts(chartData);
        } catch (e) {
            console.error('Erreur lors du parsing des données:', e);
        }
    }
});

/**
 * Initialise les 2 graphiques
 */
function initStatsCharts(chartData) {
    console.log('Initialisation des graphiques avec:', chartData);
    //Graphique des commandes par menu
    if (chartData.commandesParMenu && chartData.commandesParMenu.labels && chartData.commandesParMenu.labels.length > 0) {
        initCommandesParMenuChart(chartData.commandesParMenu);
    }
    
    // Graphique du CA par menu
    if (chartData.ca && chartData.ca.data && chartData.ca.data.length > 0) {
        initCAChart(chartData.ca);
    }
}

/**
 * Affiche un message dans un canvas vide
 */
function showEmptyChart(canvasId, message) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Fond gris clair
    ctx.fillStyle = '#f8f9fa';
    ctx.fillRect(0, 0, width, height);
    
    // Texte centré
    ctx.fillStyle = '#6c757d';
    ctx.font = '16px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(message, width / 2, height / 2);
    
    // Icône
    ctx.font = '32px Arial';
    ctx.fillText('📊', width / 2, height / 2 - 40);
}

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
                    yAxisID: 'y'
                },
                {
                    label: 'Total Personnes',
                    data: data.personnes,
                    backgroundColor: 'rgba(218, 165, 32, 0.7)',
                    borderColor: 'rgba(218, 165, 32, 1)',
                    borderWidth: 2,
                    yAxisID: 'y1'
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
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Nombre de Commandes',
                        color: 'rgba(139, 21, 56, 1)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Total Personnes',
                        color: 'rgba(218, 165, 32, 1)'
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
                    text: 'Comparaison des Commandes par Menu',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    color: '#8B1538'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y;
                            return label;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Affiche un message dans un canvas vide
 */
function showEmptyChart(canvasId, message) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Fond gris clair
    ctx.fillStyle = '#f8f9fa';
    ctx.fillRect(0, 0, width, height);
    
    // Texte centré
    ctx.fillStyle = '#6c757d';
    ctx.font = '16px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(message, width / 2, height / 2);
    
    // Icône
    ctx.font = '32px Arial';
    ctx.fillText('📊', width / 2, height / 2 - 40);
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
                backgroundColor: 'rgba(139, 21, 56, 0.7)',
                borderColor: 'rgba(139, 21, 56, 1)',
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
                    text: 'Chiffre d\'Affaires par Menu',
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

