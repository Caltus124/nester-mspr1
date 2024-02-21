<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner de Vulnérabilité</title>
    <!-- Inclure Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            margin-left: 270px;
        }
        .chart-container {
            width: 80%;
            margin: 50px auto;
        }
    </style>
</head>
<body>
    <div class="chart-container">
        <canvas id="line-chart"></canvas>
    </div>

    <div class="chart-container">
        <canvas id="bar-chart"></canvas>
    </div>

    <div class="chart-container">
        <canvas id="vulnerability-chart"></canvas>
    </div>


    <script>
        // Données de test pour les graphiques
        var labels = ["Janvier", "Février", "Mars", "Avril", "Mai"];
        var vulnerabilityData = [10, 20, 15, 25, 30];
        var colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"];

        // Création du graphique de ligne
        var lineCtx = document.getElementById('line-chart').getContext('2d');
        var lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: "Nombre de vulnérabilités",
                    backgroundColor: "rgba(75, 192, 192, 0.2)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 2,
                    data: vulnerabilityData,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        // Création du graphique à barres
        var barCtx = document.getElementById('bar-chart').getContext('2d');
        var barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "Nombre de vulnérabilités",
                    backgroundColor: colors,
                    data: vulnerabilityData,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        // Données de test pour les graphiques
        var labels = ["Low", "Medium", "High", "Critical"];
        var vulnerabilityData = [15, 10, 5, 2]; // Exemple de données
        var colors = ["#36A2EB", "#FFCE56", "#FF6384", "#9966FF"];

        // Création du graphique à barres
        var ctx = document.getElementById('vulnerability-chart').getContext('2d');
        var barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "Nombre de vulnérabilités",
                    backgroundColor: colors,
                    data: vulnerabilityData,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
</body>
</html>
