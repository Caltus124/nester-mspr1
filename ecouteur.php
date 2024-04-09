<?php
ini_set('post_max_size', '500M');
ini_set('upload_max_filesize', '500M');
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
        $input = socket_read($clientSocket, 10008192);
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
            $mac_address = $systemInfo['mac_address'];
            $currentTimeUTC = time();

            // Vérifier si le nom de la machine existe déjà dans la table system_info
            $existingMachine = $db->prepare("SELECT id FROM system_info WHERE machine_name = :machine_name");
            $existingMachine->execute([':machine_name' => $machineName]);
            $machineId = $existingMachine->fetchColumn();

            if (!$machineId) {
                // Le nom n'existe pas, insérer une nouvelle ligne dans la table system_info
                $stmt = $db->prepare("INSERT INTO system_info (machine_name, os_info, ip_address, status_machine, date_time, mac_address) VALUES (:machine_name, :os_info, :ip_address, 'enable', :date_time, :mac_address)");
                $stmt->execute([':machine_name' => $machineName, ':os_info' => $osInfo, ':ip_address' => $ipAddress, ':date_time' => time(), ':mac_address' => $mac_address]);
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
                $receivedHosts = $networkHost['hosts'];
        
                // Récupérer les hôtes enregistrés dans la base de données pour cette machine
                $existingHostsStmt = $db->prepare("SELECT host FROM network_host WHERE machine_id = :machine_id");
                $existingHostsStmt->execute([':machine_id' => $machineId]);
                $existingHosts = $existingHostsStmt->fetchAll(PDO::FETCH_COLUMN);
        
                // Ajouter les nouveaux hôtes et supprimer ceux qui ne sont plus dans les données reçues
                foreach ($receivedHosts as $host) {
                    if (!in_array($host, $existingHosts)) {
                        // Ajouter l'hôte s'il n'existe pas déjà dans la base de données
                        $insertStmt = $db->prepare("INSERT INTO network_host (machine_id, date_time, host) VALUES (:machine_id, :date_time, :host)");
                        $insertStmt->execute([':machine_id' => $machineId, ':date_time' => time(), ':host' => $host]);
                    }
                }
        
                // Supprimer les hôtes qui ne sont plus dans les données reçues
                foreach ($existingHosts as $existingHost) {
                    if (!in_array($existingHost, $receivedHosts)) {
                        // Supprimer l'hôte de la base de données
                        $deleteStmt = $db->prepare("DELETE FROM network_host WHERE machine_id = :machine_id AND host = :host");
                        $deleteStmt->execute([':machine_id' => $machineId, ':host' => $existingHost]);
                    }
                }
            }
        }

        if (isset($data['tcp_port'])) {
            $ipAddress = $data['system_info'][0]['ip_address']; // Récupérer l'adresse IP de system_info
        
            // Récupérer les ports déjà présents dans la base de données pour cette adresse IP
            $existingPortsStmt = $db->prepare("SELECT port FROM tcp_port WHERE machine_id = :machine_id AND ip_address = :ip_address");
            $existingPortsStmt->execute([':machine_id' => $machineId, ':ip_address' => $ipAddress]);
            $existingPorts = $existingPortsStmt->fetchAll(PDO::FETCH_COLUMN);
        
            foreach ($data['tcp_port'][0]['scan']['192.168.1.1']['tcp'] as $portNumber => $portInfo) {
                // Récupérer les informations sur le port TCP
                $state = $portInfo['state'];
                $reason = $portInfo['reason'];
                $name = $portInfo['name'];
                $product = $portInfo['product'];
                $version = $portInfo['version'];
                $extrainfo = $portInfo['extrainfo'];
                $conf = $portInfo['conf'];
                $cpe = $portInfo['cpe'];
        
                // Vérifier si le port existe déjà dans la base de données
                if (!in_array($portNumber, $existingPorts)) {
                    // Insérer les données dans la table tcp_port
                    $stmt = $db->prepare("INSERT INTO tcp_port (machine_id, ip_address, port, state_tcp, reason, name_tcp, product, version_tcp, extrainfo, conf, cpe) 
                        VALUES (:machine_id, :ip_address, :port, :state_tcp, :reason, :name_tcp, :product, :version_tcp, :extrainfo, :conf, :cpe)");
                    $stmt->execute([
                        ':machine_id' => $machineId,
                        ':ip_address' => $ipAddress,
                        ':port' => $portNumber,
                        ':state_tcp' => $state,
                        ':reason' => $reason,
                        ':name_tcp' => $name,
                        ':product' => $product,
                        ':version_tcp' => $version,
                        ':extrainfo' => $extrainfo,
                        ':conf' => $conf,
                        ':cpe' => $cpe
                    ]);
                }
            }
        
            // Supprimer les ports qui ne sont plus dans les données JSON
            foreach ($existingPorts as $existingPort) {
                if (!array_key_exists($existingPort, $data['tcp_port'][0]['scan']['192.168.1.1']['tcp'])) {
                    // Supprimer le port de la base de données
                    $deleteStmt = $db->prepare("DELETE FROM tcp_port WHERE machine_id = :machine_id AND ip_address = :ip_address AND port = :port");
                    $deleteStmt->execute([':machine_id' => $machineId, ':ip_address' => $ipAddress, ':port' => $existingPort]);
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
