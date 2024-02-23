<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Chemin vers la base de données SQLite
$db_path = './database/nester.db';

$tables = ['system_info', 'performances', 'ping_result', 'network_host', 'wan_latency'];

// Tableau pour stocker les données de chaque table
$tableData = [];

try {
    // Connexion à la base de données SQLite
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données de chaque table
    foreach ($tables as $table) {
        $stmt = $db->query("SELECT * FROM $table");
        $tableData[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    // En cas d'erreur, affichage du message d'erreur
    echo 'Erreur : ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage de la base de données en temps réel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            margin-bottom: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th, .data-table td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f2f2f2;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <script>

        function loadRealtimeData(table) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        console.log('Données reçues pour la table ' + table + ':');
                        console.log(xhr.responseText);
                        document.getElementById(table).innerHTML = xhr.responseText;
                    } else {
                        console.error('Erreur lors de la récupération des données pour la table ' + table + '. Statut HTTP : ' + xhr.status);
                    }
                }
            };
            xhr.open('GET', 'check_db.php?table=' + table, true);
            xhr.send();
        }

        // Actualiser les données de toutes les tables toutes les 5 secondes (5000 millisecondes)
        function loadAllRealtimeData() {
            var tables = ['system_info', 'performances', 'ping_result', 'network_host', 'wan_latency'];
            tables.forEach(function(table) {
                loadRealtimeData(table);
            });
        }

        // Charger les données pour la première fois
        window.onload = function() {
            // Afficher les tables une seule fois avec leurs données
            var tableData = <?php echo json_encode($tableData); ?>;
            var tables = ['system_info', 'performances', 'ping_result', 'network_host', 'wan_latency'];
            tables.forEach(function(table) {
                var html = "<h2>Table " + table + "</h2><table class='data-table' id='" + table + "'>";
                html += "<thead><tr>";
                Object.keys(tableData[table][0]).forEach(function(key) {
                    html += "<th>" + key + "</th>";
                });
                html += "</tr></thead><tbody>";
                tableData[table].forEach(function(row) {
                    html += "<tr>";
                    Object.values(row).forEach(function(value) {
                        html += "<td>" + value + "</td>";
                    });
                    html += "</tr>";
                });
                html += "</tbody></table>";
                document.getElementById('tables-container').innerHTML += html;
            });

            // Actualiser les données en temps réel toutes les 1 secondes
            setInterval(loadAllRealtimeData, 1000);
        };
    </script>
</head>
<body>
    <?php
        echo '<form action="home.php?page=parametres" method="get">';
        echo '<button type="submit">Retour</button>';
        echo '</form><br>';

        echo 'Aujourd\'hui : '. time();
    ?>
    
    <div id="tables-container"></div>

    <script>
        const refreshTimer = document.getElementById('refresh-timer');

        // let timerInSeconds = 0;

        // setInterval(() => {
        // timerInSeconds += 1;

        // if (timerInSeconds >= 2) {
        //     window.location.reload();
        // }
        // }, 1000);
    </script>

</body>
</html>
