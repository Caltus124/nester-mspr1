<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];

// Nom du fichier de la base de données SQLite
$dbFile = './database/nester.db';

// Connexion à la base de données SQLite
$database = new SQLite3($dbFile);

$dropQuery = 'DROP TABLE IF EXISTS user';
$database->exec($dropQuery);

// Création de la table "user"
$query = 'CREATE TABLE IF NOT EXISTS user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    nom TEXT NOT NULL, 
    prenom TEXT NOT NULL, 
    status TEXT NOT NULL
)';

$database->exec($query);

$adminUsername = 'admin';
$adminPassword = "8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918";
$adminEmail = 'admin@nester.com';
$adminNom = 'Admin'; 
$adminPrenom = 'Super'; 
$adminStatus = 'admin';

$insertQuery = $database->prepare('INSERT INTO user (username, password, email, nom, prenom, status) VALUES (:username, :password, :email, :nom, :prenom, :status)');
$insertQuery->bindValue(':username', $adminUsername, SQLITE3_TEXT);
$insertQuery->bindValue(':password', $adminPassword, SQLITE3_TEXT);
$insertQuery->bindValue(':email', $adminEmail, SQLITE3_TEXT);
$insertQuery->bindValue(':nom', $adminNom, SQLITE3_TEXT); 
$insertQuery->bindValue(':prenom', $adminPrenom, SQLITE3_TEXT);
$insertQuery->bindValue(':status', $adminStatus, SQLITE3_TEXT);
$insertQuery->execute();

$database->close();

// Destroy the session
session_destroy();

// Redirect to index.html
header("Location: index.php");
exit();

?>
