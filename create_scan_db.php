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

    // Création de la table machine
    $db->exec('CREATE TABLE IF NOT EXISTS machine (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip VARCHAR(15) NOT NULL,
        nom VARCHAR(50) NOT NULL
    )');

    // Insertion de données de test dans la table machine
    $db->exec("INSERT INTO machine (ip, nom) VALUES ('192.168.1.1', 'Machine 1')");
    $db->exec("INSERT INTO machine (ip, nom) VALUES ('192.168.1.2', 'Machine 2')");
    // Ajoutez d'autres insertions pour remplir la table avec plus de données si nécessaire

    // Création de la table performances
    $db->exec('CREATE TABLE IF NOT EXISTS performances (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        machine_id INTEGER,
        cpu_usage FLOAT,
        ram_usage FLOAT,
        storage_usage FLOAT,
        FOREIGN KEY(machine_id) REFERENCES machine(id)
    )');

    // Insertion de données de test dans la table performances
    $db->exec("INSERT INTO performances (machine_id, cpu_usage, ram_usage, storage_usage) VALUES (1, 50.5, 60.2, 70.8)");
    $db->exec("INSERT INTO performances (machine_id, cpu_usage, ram_usage, storage_usage) VALUES (2, 40.3, 70.1, 80.5)");
    // Ajoutez d'autres insertions pour remplir la table avec plus de données si nécessaire

    // Création de la table test
    $db->exec('CREATE TABLE IF NOT EXISTS test (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        machine_id INTEGER,
        ping_result INTEGER,
        port_test_result INTEGER,
        scan_result INTEGER,
        FOREIGN KEY(machine_id) REFERENCES machine(id)
    )');

    // Insertion de données de test dans la table test
    $db->exec("INSERT INTO test (machine_id, ping_result, port_test_result, scan_result) VALUES (1, 1, 0, 1)");
    $db->exec("INSERT INTO test (machine_id, ping_result, port_test_result, scan_result) VALUES (2, 0, 1, 0)");
    // Ajoutez d'autres insertions pour remplir la table avec plus de données si nécessaire

    // Création de la table machine_list
    $db->exec('CREATE TABLE IF NOT EXISTS machine_list (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        machine_id INTEGER,
        connected_hosts INTEGER,
        FOREIGN KEY(machine_id) REFERENCES machine(id)
    )');

    // Insertion de données de test dans la table machine_list
    $db->exec("INSERT INTO machine_list (machine_id, connected_hosts) VALUES (1, 5)");
    $db->exec("INSERT INTO machine_list (machine_id, connected_hosts) VALUES (2, 3)");
    // Ajoutez d'autres insertions pour remplir la table avec plus de données si nécessaire

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
