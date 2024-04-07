
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations de l'agent</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>
<body>
    <h1>Informations de l'agent</h1>
    <div class="container">
        <!-- Utilisation des variables PHP pour afficher les informations de l'agent -->
        <div class="agent-info">
            <?php
            // PHP code starts here
            // Include your PHP code here
            $db_path = './database/nester.db';
            $agentNom = "";
            $agentIP = "";

            function getOsLogoPath($type_os) {
                switch ($type_os) {
                    case 'Windows':
                        return './images/windows_logo.png'; // Chemin vers le logo de Windows
                    case 'Linux':
                        return './images/linux_logo.png'; // Chemin vers le logo de Linux
                    case 'Darwin':
                        return './images/macos_logo.png'; // Chemin vers le logo de MacOS
                    default:
                        return ''; // Si le type d'OS n'est pas reconnu, retourner une chaîne vide
                }
            }

            ## machine


            try {
                $db = new PDO('sqlite:' . $db_path);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                if(isset($_GET['ids'])) {
                    $agentId = $_GET['ids'];

                    $stmt = $db->prepare('SELECT * FROM system_info WHERE id = :id');
                    $stmt->bindValue(':id', $agentId, PDO::PARAM_INT);
                    $stmt->execute();

                    $agent = $stmt->fetch(PDO::FETCH_ASSOC);

                    if($agent) {
                        $agentNom = htmlspecialchars($agent['machine_name']);
                        $agentIP = htmlspecialchars($agent['ip_address']);
                        $mac_address = htmlspecialchars($agent['mac_address']);
                    } else {
                        echo "Aucun agent trouvé avec cet identifiant.";
                    }
                } else {
                    echo "Identifiant de l'agent non spécifié.";
                }
            } catch(PDOException $e) {
                echo "Erreur de connexion à la base de données : " . $e->getMessage();
            }

            ## performances
   
            try {
                $db2 = new PDO('sqlite:' . $db_path);
                $db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                if(isset($_GET['ids'])) {
                    $agentId2 = $_GET['ids'];
            
                    $stmt = $db2->prepare('SELECT * FROM performances WHERE machine_id = :id ORDER BY id DESC LIMIT 1');
                    // Remplacez "timestamp_column" par le nom de votre colonne de date/heure
                    $stmt->bindValue(':id', $agentId2, PDO::PARAM_INT);
                    $stmt->execute();
            
                    $agent2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
                    if($agent2) {
                        $cpu_usage = htmlspecialchars($agent2['cpu_usage']);

                        $ram_usage = htmlspecialchars($agent2['ram_used']);
                        $storage_usage = htmlspecialchars($agent2['storage_used']);
                        
                        // Récupérer les autres valeurs nécessaires
                        $ram_free = htmlspecialchars($agent2['ram_free']);
                        $ram_total = htmlspecialchars($agent2['ram_total']);
                        $storage_free = htmlspecialchars($agent2['storage_free']);
                        $storage_total = htmlspecialchars($agent2['storage_total']);
                        
                        // Calculer le pourcentage d'utilisation de la RAM et du stockage
                        $ram_percent = round(($ram_usage / $ram_total) * 100, 2);
                        $storage_percent = round(($storage_usage / $storage_total) * 100, 2);

                    } else {
                        echo "Aucun agent trouvé avec cet identifiant.";
                    }
                } else {
                    echo "Identifiant de l'agent non spécifié.";
                }
            } catch(PDOException $e) {
                echo "Erreur de connexion à la base de données : " . $e->getMessage();
            }

            function getUsageClass($value) {
                if ($value <= 65) {
                    return "low-usage";
                } elseif ($value <= 80) {
                    return "medium-usage";
                } else {
                    return "high-usage";
                }
            }
            if (isset($_GET['ids'])) {
                $machine_id = $_GET['ids'];
            
                // Requête pour récupérer les données de performances en fonction de l'ID de la machine
                $stmt = $db->prepare('SELECT * FROM performances WHERE machine_id = :id');
                $stmt->bindParam(':id', $machine_id);
                $stmt->execute();
            
                $timestamps_ram = [];
                $ramPercentages = [];
                $storagePercentages = [];
                $cpuPercentages = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $ram_used = $row['ram_used'];
                    $ram_total = $row['ram_total'];
                    $ram_free = $row['ram_free'];
                    $date_time_unix = $row['date_time'];
        
                    $storage_used = $row['storage_used'];
                    $storage_total = $row['storage_total'];
                    $storage_free = $row['storage_free'];
                    $cpu = $row['cpu_usage'];
                    // Convertir le timestamp Unix en date/heure au format UTC
                    $formatted_date_time = gmdate('Y-m-d H:i:s', $date_time_unix);
                
                    $ram_percentage = ($ram_used / $ram_total) * 100;
                    $storage_percentage = ($storage_used / $storage_total) * 100;
        
                    // Conversion en GB
                    $storage_total_gb = round($storage_total / (1024 * 1024 * 1024), 2);
                    $ram_total_gb = round($ram_total / (1024 * 1024 * 1024), 2);

                    $storage_used_gb = round($storage_used / (1024 * 1024 * 1024), 2);
                    $ram_used_gb = round($ram_used / (1024 * 1024 * 1024), 2);
        
                    // Ajouter les valeurs aux tableaux
                    array_push($timestamps_ram, $formatted_date_time);
                    array_push($ramPercentages, round($ram_percentage, 2));
                    array_push($storagePercentages, round($storage_percentage, 2));
                    array_push($cpuPercentages, $cpu);
        
                }
        
        
            } else {
                echo "ID de la machine non fourni en paramètre GET.";
            }
            ?>
            <!-- PHP code ends here -->
            <p style="color: #4070f4;"><?php echo $agentIP; ?></p>
            <p><?php echo $agentNom; ?></p>
            <p style="color: gray; font-size: 1em;"><?php echo $mac_address; ?></p>
            <!-- Ajouter d'autres informations si nécessaire -->
        </div>
        <?php 
        $type_os = $agent['os_info'];
        $logo_path = getOsLogoPath($type_os);
        if (!empty($logo_path)) {
            echo '<div class="os-logo">';
            echo '<img src="' . $logo_path . '" alt="' . $type_os . '">';
            echo '</div>';
        }

        ?>
        <div class="agent-info">
            <p>Stockage : <?php echo $storage_total_gb; ?>GB</p>
            <p>RAM : <?php echo $ram_total_gb; ?>GB</p>
            <p style="color: gray; font-size: 1em;">Dernier scan : <?php echo $formatted_date_time; ?></p>
        </div>
    </div>
    <div class="performance-info">
        <div class="performance-box">
            <div class="performance-box-variable">
                <p class="info-text <?php echo getUsageClass($storage_percent); ?>"><?php echo $storage_percent; ?>%</p>
            </div>
            <div class="performance-box-nom">
                <p>STORAGE</p>
            </div>
        </div>
        <div class="performance-box">
            <div class="performance-box-variable">
                <p class="info-text <?php echo getUsageClass($ram_percent); ?>"><?php echo $ram_percent; ?>%</p>
            </div>
            <div class="performance-box-nom">
                <p>RAM</p>
            </div>
        </div>
        <div class="performance-box">
            <div class="performance-box-variable">
                <p class="info-text <?php echo getUsageClass($cpu_usage); ?>"><?php echo $cpu_usage; ?>%</p>
            </div>
            <div class="performance-box-nom">
                <p>CPU</p>
            </div>
        </div>
    </div>
    <div class="chart-container1">
        <canvas id="progressCanvas" width="400" height="400"></canvas>
        <canvas id="storageChart" width="400" height="400"></canvas>
        <canvas id="ramChart" width="400" height="400"></canvas>
        <canvas id="cpuChart" width="400" height="400"></canvas>
    </div>
    <div class="chart-container2">
        <canvas id="ramChart2" width="200" height="80%"></canvas>
    </div>
    <div class="chart-container2">
        <canvas id="storageChart2" width="200" height="80%"></canvas>
    </div>
    <div class="chart-container2">
        <canvas id="cpu" width="200" height="80%"></canvas>
    </div>
    <script>
    //////////////////////////////////////////////
    var storageUsage = <?php echo $storage_percent; ?>;
    var storageFree = 100 - storageUsage;

    var ramUsage = <?php echo $ram_percent; ?>;
    var ramFree = 100 - ramUsage;

    var cpuUsage = <?php echo $cpu_usage; ?>;

    var canvas = document.getElementById('progressCanvas');
    var context = canvas.getContext('2d');

    // Dessiner la barre de progression pour le stockage
    context.fillStyle = 'lightgrey';
    context.fillRect(0, 70, canvas.width, 40);

    var storageBarWidth = (storageUsage / 100) * canvas.width;
    context.fillStyle = '#36A2EB';
    context.fillRect(0, 70, storageBarWidth, 40);

    // Dessiner la barre de progression pour la RAM
    context.fillStyle = 'lightgrey';
    context.fillRect(0, 150, canvas.width, 40);

    var ramBarWidth = (ramUsage / 100) * canvas.width;
    context.fillStyle = '#FFCE56';
    context.fillRect(0, 150, ramBarWidth, 40);

    // Dessiner la barre de progression pour le CPU
    context.fillStyle = 'lightgrey';
    context.fillRect(0, 230, canvas.width, 40);

    var cpuBarWidth = (cpuUsage / 100) * canvas.width;
    context.fillStyle = '#FF5733';
    context.fillRect(0, 230, cpuBarWidth, 40);

    // Définir le style de texte
    context.fillStyle = 'black';
    context.font = '25px Arial';
    context.textAlign = 'center';

    // Afficher le texte pour le stockage au centre du canvas
    context.fillText('Stockage : ' + <?php echo $storage_used_gb; ?> + 'GB / ' + <?php echo $storage_total_gb; ?> + 'GB ', canvas.width / 2, 60);

    // Afficher le texte pour la RAM au centre du canvas
    context.fillText('RAM : ' + <?php echo $ram_used_gb; ?> + 'GB / ' + <?php echo $ram_total_gb; ?> + 'GB ', canvas.width / 2, 140);

    // Afficher le texte pour le CPU au centre du canvas
    context.fillText('CPU : ' + <?php echo $cpu_usage; ?> + '% / 100%', canvas.width / 2, 220);

    //$data_time_utc = gmdate('Y-m-d H:i:s', $date_time_unix);

    

    //////////////////////////////////////////////
    // Données du graphique pour la RAM
    var ramUsage = <?php echo $ram_percent; ?>;
    var ramFree = 100 - ramUsage;

    var ramData = {
        labels: ['RAM Utilisée', 'RAM Libre'],
        datasets: [{
            data: [ramUsage, ramFree],
            backgroundColor: ['#FF6384', '#36A2EB']
        }]
    };

    // Configuration du graphique pour la RAM
    var ramOptions = {
        responsive: true,
        plugins: {
            datalabels: {
                display: true,
                color: '#000',
                font: {
                    weight: 'bold'
                },
                formatter: function(value, context) {
                    return context.chart.data.labels[context.dataIndex] + ': ' + value + '%';
                }
            },
            title: {
                display: true,
                text: 'Utilisation de la RAM', // Titre du graphique
                font: {
                    size: 32,
                    weight: 'bold'
                },
            }
        }
    };

    // Création du graphique pour la RAM
    var ramCtx = document.getElementById('ramChart').getContext('2d');
    var ramChart = new Chart(ramCtx, {
        type: 'doughnut',
        data: ramData,
        options: ramOptions
    });

    // Données du graphique pour le stockage
    var storageUsage = <?php echo $storage_percent; ?>;
    var storageFree = 100 - storageUsage;

    var storageData = {
        labels: ['Stockage Utilisé', 'Stockage Libre'],
        datasets: [{
            data: [storageUsage, storageFree],
            backgroundColor: ['#FF6384', '#36A2EB']
        }]
    };

    // Configuration du graphique pour le stockage
    var storageOptions = {
        responsive: true,
        plugins: {
            datalabels: {
                display: true,
                color: '#000',
                font: {
                    weight: 'bold'
                },
                formatter: function(value, context) {
                    return context.chart.data.labels[context.dataIndex] + ': ' + value + '%';
                }
            },
            title: {
                display: true,
                text: 'Utilisation du stockage', // Titre du graphique
                font: {
                    size: 32,
                    weight: 'bold'
                },
            }
        }
    };

    // Création du graphique pour le stockage
    var storageCtx = document.getElementById('storageChart').getContext('2d');
    var storageChart = new Chart(storageCtx, {
        type: 'doughnut',
        data: storageData,
        options: storageOptions
    });



    // Données du graphique pour le cpu
    var storageUsage = <?php echo $cpu_usage; ?>;
    var storageFree = 100 - storageUsage;

    var storageData = {
        labels: ['CPU Utilisé', 'CPU Libre'],
        datasets: [{
            data: [storageUsage, storageFree],
            backgroundColor: ['#FF6384', '#36A2EB']
        }]
    };

    // Configuration du graphique pour le cpu
    var storageOptions = {
        responsive: true,
        plugins: {
            datalabels: {
                display: true,
                color: '#000',
                font: {
                    weight: 'bold'
                },
                formatter: function(value, context) {
                    return context.chart.data.labels[context.dataIndex] + ': ' + value + '%';
                }
            },
            title: {
                display: true,
                text: 'Utilisation du CPU', // Titre du graphique
                font: {
                    size: 32,
                    weight: 'bold'
                },
            }
        }
    };

    // Création du graphique pour le cpu
    var storageCtx = document.getElementById('cpuChart').getContext('2d');
    var storageChart = new Chart(storageCtx, {
        type: 'doughnut',
        data: storageData,
        options: storageOptions
    });

    // RAM LINE GRAF
    
    // Récupérer les données PHP dans JavaScript
    var timestamps = <?php echo json_encode($timestamps_ram); ?>;
    var ramPercentages = <?php echo json_encode($ramPercentages); ?>;

    // Créer le graphique avec Chart.js
    var ctx = document.getElementById('ramChart2').getContext('2d');
    var ramChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: timestamps,
            datasets: [{
                label: 'Pourcentage de RAM utilisée',
                data: ramPercentages,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Pourcentage de RAM utilisée'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date/Heure (UTC)'
                    }
                }
            }
        }
    });


    // STORAGE LINE GRAF
    
    // Récupérer les données PHP dans JavaScript
    var storagePercentages = <?php echo json_encode($storagePercentages); ?>;

    // Créer le graphique avec Chart.js
    var ctx = document.getElementById('storageChart2').getContext('2d');
    var ramChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: timestamps,
            datasets: [{
                label: 'Pourcentage du stockage utilisée',
                data: storagePercentages,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Pourcentage du stockage utilisée'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date/Heure (UTC)'
                    }
                }
            }
        }
    });

    // CPU LINE GRAF
    
    // Récupérer les données PHP dans JavaScript
    var cpuPercentages = <?php echo json_encode($cpuPercentages); ?>;

    // Créer le graphique avec Chart.js
    var ctx = document.getElementById('cpu').getContext('2d');
    var ramChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: timestamps,
            datasets: [{
                label: 'Pourcentage du CPU utilisée',
                data: cpuPercentages,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Pourcentage du stockage utilisée'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date/Heure (UTC)'
                    }
                }
            }
        }
    });

</script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            margin-left: 270px;
            overflow-x: hidden;
        }
        .progress-bar {
            margin-bottom: 20px;
        }

        .progress-bar h2 {
            margin-bottom: 5px;
        }

        .progress {
            height: 20px;
            background-color: lightgrey;
            border-radius: 5px;
            position: relative;
        }

        .progress::after {
            content: '';
            display: block;
            height: 100%;
            width: 100%;
            background-color: #36A2EB;
            border-radius: 5px;
        }

        .percent {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            color: black;
        }

        .container {
            max-width: 90%;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-around;
            align-items: center;
            background-color: #ffff;
        }

        .agent-info p {
            margin: 10px 0;
            font-size: 2em;
            font-weight: bold;
        }

        .os-logo img {
            max-width: 100px;
            height: auto;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
        }

        .performance-info {
            display: flex;
            justify-content: space-between;
            max-width: 90%;
            align-items: center;
            margin-left: auto;
            margin-right: auto;
        }

        .performance-box {
            width: calc(33.33% - 20px);
            padding-top: calc(20% - 20px);
            margin: 0 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative;
            background-color: #fff;
        }

        .performance-box p {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0;
            text-align: center;
        }
        .performance-box-nom p {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            padding-top: 20%;
        }
        .info-text{
            font-size: 5em;
        }
        .low-usage {
            color: green;
        }

        .medium-usage {
            color: orange;
        }

        .high-usage {
            color: red;
        }
        .chart-container1 {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            max-width: 90%;
            margin: 0 auto;
            height: 30em;
            margin-bottom: 580px;
            margin-top: 50px;
        }

        .chart-container1 > * {
            flex-basis: 45%; /* Ajustez cette valeur en fonction de vos besoins */
            margin-bottom: 50px; /* Espace entre les éléments */
        }
        .chart-container2 {
            display: flex;
            justify-content: space-around;
            max-width: 500px;
            margin: 0 auto; 
            max-width: 90%;
            margin-bottom: 50px;
        }
        canvas {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 50px;
        }
    </style>
</body>
</html>
