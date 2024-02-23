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

    // Création de la table machine
    $db->exec('CREATE TABLE IF NOT EXISTS system_info (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip_address VARCHAR(15) NOT NULL,
        machine_name VARCHAR(50) NOT NULL,
        os_info VARCHAR(50) NOT NULL,
        status_machine VARCHAR(50) NOT NULL,
        date_time VARCHAR(50) NOT NULL
    )');

    // Insertion de données de test dans la table machine
    $db->exec("INSERT INTO system_info (ip_address, machine_name, os_info, status_machine, date_time) VALUES ('127.0.0.1', 'Nester', 'Linux', 'enable', '1708592885')");
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
        "192.168.1.100",
        "192.168.1.11",
        "192.168.1.117",
        "192.168.1.137",
        "192.168.1.17",
        "192.168.1.18",
        "192.168.1.21",
        "192.168.1.53",
        "192.168.1.54",
        "192.168.1.90"
    ];


    foreach ($hosts as $host) {
        $db->exec("INSERT INTO network_host (machine_id, date_time, host) VALUES (1, 1708675417, '$host')");
    }




    // Création de la table wan_latency
    $db->exec('CREATE TABLE IF NOT EXISTS wan_latency (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        latency FLOAT
    )');

    header("Location: home.php?page=parametres&?msg=table-supprime");
    exit();

} catch(PDOException $e) {
    // En cas d'erreur, affichage du message d'erreur
    echo 'Erreur : ' . $e->getMessage();
}
?>
