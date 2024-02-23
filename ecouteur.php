<?php
// Adresse IP et port à écouter
$address = '0.0.0.0';
$port = 55000;

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

        // Insérer les données dans les tables en fonction des données reçues
        if (isset($data['system_info'])) {
            $systemInfo = $data['system_info'][0];
            $machineName = $systemInfo['machine_name'];
            $osInfo = $systemInfo['os_info'];
            $ipAddress = $systemInfo['ip_address'];
            $currentTimeUTC = time();

            // Vérifier si le nom de la machine existe déjà dans la table system_info
            $existingMachine = $db->prepare("SELECT id FROM system_info WHERE machine_name = :machine_name");
            $existingMachine->execute([':machine_name' => $machineName]);
            $machineId = $existingMachine->fetchColumn();

            if (!$machineId) {
                // Le nom n'existe pas, insérer une nouvelle ligne dans la table system_info
                $stmt = $db->prepare("INSERT INTO system_info (machine_name, os_info, ip_address, status_machine, date_time) VALUES (:machine_name, :os_info, :ip_address, 'disable', :date_time)");
                $stmt->execute([':machine_name' => $machineName, ':os_info' => $osInfo, ':ip_address' => $ipAddress, ':date_time' => time()]);
                // Récupérer l'ID de la machine nouvellement insérée
                $machineId = $db->lastInsertId();
            }

            // Insérer les données dans la table performances
            $ramInfo = $systemInfo['ram_info'];
            $storageInfo = $systemInfo['storage_info'];
            $cpuUsage = $systemInfo['cpu_usage'];

            $stmt = $db->prepare("INSERT INTO performances (machine_id, cpu_usage, ram_total, ram_used, ram_free, storage_total, storage_used, storage_free, date_time) VALUES (:machine_id, :cpu_usage, :ram_total, :ram_used, :ram_free, :storage_total, :storage_used, :storage_free, :date_time)");
            $stmt->execute([
                ':machine_id' => $machineId,
                ':cpu_usage' => $cpuUsage,
                ':ram_total' => $ramInfo['total'],
                ':ram_used' => $ramInfo['used'],
                ':ram_free' => $ramInfo['free'],
                ':storage_total' => $storageInfo['total'],
                ':storage_used' => $storageInfo['used'],
                ':storage_free' => $storageInfo['free'],
                ':date_time' => time()
            ]);
        }

        if (isset($data['ping_result'])) {
            foreach ($data['ping_result'] as $pingResult) {
                $host = $pingResult['host'];
                $result = $pingResult['result'];
                $timestamp = $pingResult['timestamp'];

                $stmt = $db->prepare("INSERT INTO ping_result (machine_id, date_time, host, result) VALUES (:machine_id, :date_time, :host, :result)");
                $stmt->execute([':machine_id' => $machineId, ':date_time' => time(), ':host' => $host, ':result' => $result]);
            }
        }

        if (isset($data['network_host'])) {
            foreach ($data['network_host'] as $networkHost) {
                $timestamp = $networkHost['timestamp'];
                foreach ($networkHost['hosts'] as $host) {
                    $stmt = $db->prepare("INSERT INTO network_host (machine_id, date_time, host) VALUES (:machine_id, :date_time, :host)");
                    $stmt->execute([':machine_id' => $machineId, ':date_time' => time(), ':host' => $host]);
                }
            }
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
