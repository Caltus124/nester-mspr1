<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
// Chemin vers la base de données SQLite
$db_path = './database/nester.db';

try {
    // Connexion à la base de données SQLite
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Suppression des tables si elles existent déjà
    $db->exec('DROP TABLE IF EXISTS machine');
    $db->exec('DROP TABLE IF EXISTS performances');
    $db->exec('DROP TABLE IF EXISTS test');
    $db->exec('DROP TABLE IF EXISTS machine_list');
    $db->exec('DROP TABLE IF EXISTS wan_latency');

    $db->exec('DROP TABLE IF EXISTS system_info');
    $db->exec('DROP TABLE IF EXISTS ping_result');
    $db->exec('DROP TABLE IF EXISTS network_host');
    $db->exec('DROP TABLE IF EXISTS tcp_port');


    // Création de la table machine
    $db->exec('CREATE TABLE IF NOT EXISTS system_info (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip_address VARCHAR(15) NOT NULL,
        machine_name VARCHAR(50) NOT NULL,
        os_info VARCHAR(50) NOT NULL,
        status_machine VARCHAR(50) NOT NULL,
        date_time VARCHAR(50) NOT NULL,
        mac_address VARCHAR(50) NOT NULL
    )');

    // Insertion de données de test dans la table machine
    $db->exec("INSERT INTO system_info (ip_address, machine_name, os_info, status_machine, date_time, mac_address) VALUES ('127.0.0.1', 'Nester', 'Linux', 'enable', '1708592885', '3a:74:e9:d2:a4:49')");
    // Ajoutez d'autres insertions pour remplir la table avec plus de données si nécessaire

    // Création de la table performances
    $db->exec('CREATE TABLE IF NOT EXISTS performances (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        machine_id INTEGER,
        cpu_usage FLOAT NOT NULL,
        ram_total INT NOT NULL,
        ram_used INT NOT NULL,
        ram_free INT NOT NULL,
        storage_total INT NOT NULL,
        storage_used INT NOT NULL,
        storage_free INT NOT NULL,
        date_time INT NOT NULL,
        FOREIGN KEY(machine_id) REFERENCES machine(id)
    )');

    // Insertion de données de test dans la table performances
    $db->exec("INSERT INTO performances (machine_id, cpu_usage, ram_total, ram_used, ram_free, storage_total, storage_used, storage_free, date_time) VALUES (1, 24.9, 8589934592, 3165732864, 87490560, 474404847616, 10122203136, 19832930304, 1708592885)");

    // Ajoutez d'autres insertions pour remplir la table avec plus de données si nécessaire

    // Créer la table ping_result
    $db->exec('CREATE TABLE IF NOT EXISTS ping_result (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        machine_id INTEGER,
        date_time INT NOT NULL,
        host VARCHAR(15) NOT NULL,
        result REAL NOT NULL,
        FOREIGN KEY(machine_id) REFERENCES machine(id)
    )');

    // Insérer les données dans la table ping_result
    $db->exec("INSERT INTO ping_result (date_time, host, result, machine_id) 
    VALUES (1708675417, '1.1.1.1', 0.0056362152099609375, 1)");

    // Ajoutez d'autres insertions pour remplir la table avec plus de données si nécessaire

    // Création de la table machine_list
    $db->exec('CREATE TABLE IF NOT EXISTS machine_list (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        machine_id INTEGER,
        connected_hosts INTEGER,
        FOREIGN KEY(machine_id) REFERENCES machine(id)
    )');

    // Créer la table network_host avec date_time
    $db->exec('CREATE TABLE IF NOT EXISTS network_host (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        machine_id INTEGER,
        date_time INTEGER NOT NULL,
        host VARCHAR(15) NOT NULL,
        FOREIGN KEY(machine_id) REFERENCES machine(id)
    )');

    // Insérer les données dans la table network_host
    $hosts = [
        "192.168.1.1",
        "192.168.1.10",
        "192.168.1.100"
    ];


    foreach ($hosts as $host) {
        $db->exec("INSERT INTO network_host (machine_id, date_time, host) VALUES (1, 1708675417, '$host')");
    }


    // Création de la table wan_latency
    $db->exec('CREATE TABLE IF NOT EXISTS wan_latency (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        latency FLOAT
    )');

    // Création de la table tcp_port
    $db->exec('CREATE TABLE IF NOT EXISTS tcp_port (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        machine_id INTEGER,
        ip_address VARCHAR(15) NOT NULL,
        port INTEGER NOT NULL,
        state_tcp VARCHAR(50) NOT NULL,
        reason VARCHAR(50) NOT NULL,
        name_tcp VARCHAR(50) NOT NULL,
        product VARCHAR(50) NOT NULL,
        version_tcp VARCHAR(50) NOT NULL,
        extrainfo VARCHAR(50) NOT NULL,
        conf INTEGER NOT NULL,
        cpe VARCHAR(50) NOT NULL
    )');

    // Insertion des données dans la table tcp_port
    $data_tcp_port = array(
        array('ip_address' => '192.168.1.1', 'port' => 20, 'state' => 'filtered', 'reason' => 'no-response', 'name' => 'ftp-data', 'product' => '', 'version' => '', 'extrainfo' => '', 'conf' => 3, 'cpe' => ''),
        array('ip_address' => '192.168.1.1', 'port' => 21, 'state' => 'filtered', 'reason' => 'no-response', 'name' => 'ftp', 'product' => '', 'version' => '', 'extrainfo' => '', 'conf' => 3, 'cpe' => ''),
        // Ajoutez d'autres données d'insertion ici
    );

    foreach ($data_tcp_port as $row) {
        $stmt = $db->prepare('INSERT INTO tcp_port (
            ip_address,
            machine_id,
            port,
            state_tcp,
            reason,
            name_tcp,
            product,
            version_tcp,
            extrainfo,
            conf,
            cpe
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array(
            $row['ip_address'],
            1,
            $row['port'],
            $row['state'],
            $row['reason'],
            $row['name'],
            $row['product'],
            $row['version'],
            $row['extrainfo'],
            $row['conf'],
            $row['cpe']
        ));
    }



    header("Location: home.php?page=parametres&?msg=table-supprime");
    exit();

} catch(PDOException $e) {
    // En cas d'erreur, affichage du message d'erreur
    echo 'Erreur : ' . $e->getMessage();
}
?>
