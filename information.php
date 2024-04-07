<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            margin-left: 270px;
        }
        .container {
            display: flex;
            justify-content: space-around;
            margin-top: 50px;
        }
        .box {
            width: 30%;
            padding: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
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
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Connexion à la base de données SQLite
        $db = new SQLite3('database/nester.db');

        // Requête pour obtenir le nombre total d'agents
        $total_agents_query = $db->query("SELECT COUNT(*) AS total_agents FROM system_info");
        $total_agents_row = $total_agents_query->fetchArray(SQLITE3_ASSOC);
        $total_agents = $total_agents_row['total_agents'];

        // Requête pour obtenir le nombre d'agents activés
        $enabled_agents_query = $db->query("SELECT COUNT(*) AS enabled_agents FROM system_info WHERE status_machine = 'enable'");
        $enabled_agents_row = $enabled_agents_query->fetchArray(SQLITE3_ASSOC);
        $enabled_agents = $enabled_agents_row['enabled_agents'];

        // Calculer le nombre d'agents désactivés
        $disabled_agents = $total_agents - $enabled_agents;

        // Afficher les résultats dans des boîtes
        echo "<div class='box'><h3>Total Agents</h3><p class='total'>$total_agents</p></div>";
        echo "<div class='box'><h3>Enabled Agents</h3><p class='enabled'>$enabled_agents</p></div>";
        echo "<div class='box'><h3>Disabled Agents</h3><p class='disabled'>$disabled_agents</p></div>";

        // Fermer la connexion à la base de données
        $db->close();
        ?>
    </div>
    <div class="container">
        <?php
        ?>
    </div>
</body>
</html>
