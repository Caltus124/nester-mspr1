<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des machines</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            margin-left: 270px;
            margin-top: 50px;
        }
        .machine-container {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            width: 80%;
            display: flex;
            justify-content: space-around;
            align-items: center;
            transition: transform 0.3s ease; /* Ajout d'une transition pour l'effet de survol */
            font-weight: bold;

        }
        .machine-container:hover {
            transform: scale(1.1); /* Agrandir le conteneur lors du survol */
            cursor: pointer;
        }
        .machine-info {
            margin-bottom: 10px;
        }
        .machine-info span {
            font-weight: bold;
        }
        .machine-details {
            flex-grow: 1; /* Pour que le div occupe tout l'espace disponible */
        }
        .machine-image {
            margin-right: 20px;
            width: 50px;
            height: 50px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            margin-top: 50px;
        }
        .text {
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis; /* Couper le texte qui dépasse */
            white-space: nowrap; /* Empêcher le texte de passer à la ligne */
        }
        a{
            text-decoration: none;
            color: black;
        }
        .green-dot{
            color: green;
            font-size: 4em;
        }
        .red-dot{
            color: red;
            font-size: 4em;
        }
        .box {
            width: 30%;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        h3 {
            margin-top: 0;
            color: #333333;
        }
        p {
            margin-bottom: 0;
            font-size: 36px;
            font-weight: bold;
        }
        .total {
            color: #4070f4;
        }
        .enabled {
            color: green;
        }
        .disabled {
            color: red;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            width: 80%;
            display: flex;
            justify-content: space-around;
            align-items: center;
            transition: transform 0.3s ease; /* Ajout d'une transition pour l'effet de survol */
            font-weight: bold;

        }
    </style>
</head>
<body>
    <?php
        // Connexion à la base de données SQLite
        $db_path = 'database/nester.db';

        try {
            // Connexion à la base de données SQLite
            $db = new PDO('sqlite:' . $db_path);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Requête pour obtenir le nombre total d'agents
            $total_agents_query = $db->query("SELECT COUNT(*) AS total_agents FROM system_info");
            $total_agents_row = $total_agents_query->fetch(PDO::FETCH_ASSOC);
            $total_agents = $total_agents_row['total_agents'];

            // Requête pour obtenir le nombre d'agents activés
            $enabled_agents_query = $db->query("SELECT COUNT(*) AS enabled_agents FROM system_info WHERE status_machine = 'enable'");
            $enabled_agents_row = $enabled_agents_query->fetch(PDO::FETCH_ASSOC);
            $enabled_agents = $enabled_agents_row['enabled_agents'];

            // Calculer le nombre d'agents désactivés
            $disabled_agents = $total_agents - $enabled_agents;
            
            // Récupérer le statut à partir de la requête GET
            $status = isset($_GET['status']) ? $_GET['status'] : 'all';
            switch ($status) {
                case 'all':
                    $title = "Agents all";
                    break;
                case 'enable':
                    $title = "Agents enabled";
                    break;
                case 'disable':
                    $title = "Agents disabled";
                    break;
                default:
                    $title = "Agents list";
                    break;
            }

            // Afficher les résultats dans des boîtes
            echo "<div class='container'>";
            echo "<div class='box'><a href='?page=agents&status=all'><h3>Total Agents</h3><p class='total'>$total_agents</p></a></div>";
            echo "<div class='box'><a href='?page=agents&status=enable'><h3>Enabled Agents</h3><p class='enabled'>$enabled_agents</p></a></div>";
            echo "<div class='box'><a href='?page=agents&status=disable'><h3>Disabled Agents</h3><p class='disabled'>$disabled_agents</p></a></div>";
            echo "</div>";
            echo "<h1>$title</h1>";

            // Modifier la requête SQL en fonction du statut
            if ($status === 'all') {
                $query = $db->query('SELECT * FROM system_info');
            } else {
                $query = $db->prepare('SELECT * FROM system_info WHERE status_machine = :status');
                $query->execute(['status' => $status]);
            }

            // Boucle pour récupérer les données et les afficher
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $agentId = $row['id'];
                $status = $row['status_machine'];
                $dotColorClass = ($status == 'disable') ? 'red-dot' : 'green-dot';
                echo "<a href='home.php?page=info_agent&ids=$agentId' class='agent-link'>";
                echo "<div class='machine-container'>";
                echo "<div class='machine-info'><div class='text'>{$row['machine_name']}</div></div>";
                echo "<div class='machine-info'><div class='text'>{$row['ip_address']}</div></div>";
                // Ajouter l'image du logo de l'OS en fonction du type d'OS
                $os_logo_path = getOsLogoPath($row['os_info']);
                if ($os_logo_path) {
                    echo "<div class='machine-info'><img src='{$os_logo_path}' alt='Logo OS' class='machine-image'></div>";
                }
                echo "<div class='machine-info'><div class='text $dotColorClass'>.</div></div>";
                echo "</div>";
                echo "</a>";
            }
            } catch (PDOException $e) {
            // En cas d'erreur, affichage du message d'erreur
            echo 'Erreur : ' . $e->getMessage();
            }


        // Fonction pour obtenir le chemin d'accès au logo de l'OS en fonction du type d'OS
        function getOsLogoPath($type_os) {
            switch ($type_os) {
                case 'Windows':
                    return './images/windows_logo.png';
                case 'Linux':
                    return './images/linux_logo.png';
                case 'Darwin':
                    return './images/macos_logo.png';
                default:
                    return '';
            }
        }
        ?>

</body>
</html>
