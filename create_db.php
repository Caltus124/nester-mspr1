<?php
// Nom du fichier de la base de données SQLite
$dbFile = './database/nester.db';

// Connexion à la base de données SQLite
$database = new SQLite3($dbFile);

// Création de la table "user"
$query = 'CREATE TABLE IF NOT EXISTS user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    status TEXT NOT NULL
)';
$database->exec($query);

// Exemple d'ajout d'un utilisateur administrateur
$adminUsername = 'admin';
$adminPassword = password_hash('admin', PASSWORD_DEFAULT); // Remplacez 'votre_mot_de_passe' par le mot de passe souhaité
$adminStatus = 'admin';

$insertQuery = $database->prepare('INSERT INTO user (username, password, status) VALUES (:username, :password, :status)');
$insertQuery->bindValue(':username', $adminUsername, SQLITE3_TEXT);
$insertQuery->bindValue(':password', $adminPassword, SQLITE3_TEXT);
$insertQuery->bindValue(':status', $adminStatus, SQLITE3_TEXT);
$insertQuery->execute();

// Fermeture de la connexion à la base de données
$database->close();

echo 'Base de données créée avec succès.';
?>
