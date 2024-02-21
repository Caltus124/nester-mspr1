<?php
// Adresse IP et port à écouter
$address = '0.0.0.0';
$port = 5556;

// Chemin vers la base de données SQLite
$db_path = './database/nester.db';

// Création d'un socket TCP/IP
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "Erreur lors de la création du socket : " . socket_strerror(socket_last_error()) . "\n";
    exit(1);
}

// Liaison du socket à l'adresse et au port
$result = socket_bind($socket, $address, $port);
if ($result === false) {
    echo "Erreur lors de la liaison du socket à l'adresse et au port : " . socket_strerror(socket_last_error()) . "\n";
    exit(1);
}

// Attente de connexions sur le port spécifié
$result = socket_listen($socket, 3);
if ($result === false) {
    echo "Erreur lors de l'écoute sur le socket : " . socket_strerror(socket_last_error()) . "\n";
    exit(1);
}

echo "En attente de connexion...\n";

try {
    // Connexion à la base de données SQLite
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Boucle infinie pour traiter les connexions entrantes
    while (true) {
        // Accepter la connexion et recevoir les données
        $clientSocket = socket_accept($socket);
        if ($clientSocket === false) {
            echo "Erreur lors de l'acceptation de la connexion du client : " . socket_strerror(socket_last_error()) . "\n";
            continue;
        }

        echo "Connexion acceptée.\n";

        // Lire les données envoyées par le client
        $input = socket_read($clientSocket, 1024);
        if ($input === false) {
            echo "Erreur lors de la lecture des données : " . socket_strerror(socket_last_error()) . "\n";
            continue;
        }

        echo "Données reçues : $input\n";

        // Décoder les données JSON
        $data = json_decode($input, true);

        // Insérer les données dans les tables respectives
        foreach ($data as $table => $tableData) {
            $keys = implode(", ", array_keys($tableData));
            $values = "'" . implode("', '", array_values($tableData)) . "'";
            $db->exec("INSERT INTO $table ($keys) VALUES ($values)");
        }

        // Fermer le socket client
        socket_close($clientSocket);
    }
} catch (PDOException $e) {
    // En cas d'erreur de la base de données, affichage du message d'erreur
    echo 'Erreur de base de données : ' . $e->getMessage();
} finally {
    // Fermer le socket serveur
    socket_close($socket);
}
?>

