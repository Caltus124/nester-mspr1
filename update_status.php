<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connexion à la base de données SQLite (à adapter selon votre configuration)
    $dbFile = './database/nester.db';
    $database = new SQLite3($dbFile);

    // Récupération des nouveaux statuts depuis le formulaire
    $newStatus = $_POST['new_status'];

    // Parcourir et mettre à jour la base de données
    foreach ($newStatus as $userId => $status) {
        $userId = (int)$userId;
        $status = $database->escapeString($status);

        // Requête SQL pour mettre à jour le statut de l'utilisateur
        $updateSql = "UPDATE user SET status = '$status' WHERE id = $userId";
        $database->exec($updateSql);
    }

    // Fermeture de la connexion à la base de données
    $database->close();
    
    // Redirection vers la page d'origine ou une autre page
    header('Location: home.php?page=user');
    exit();
}
?>
