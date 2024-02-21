<?php
session_start();

// Fonction pour démarrer l'écouteur
function startListener() {
    // Tuer l'ancien écouteur s'il existe
    exec("pkill -f 'php ecouteur.php'");

    // Démarrer le nouvel écouteur
    $_SESSION['listener_status'] = 'active';
    exec('nohup php ecouteur.php > log.txt 2>&1 &');

    // Rediriger vers la page principale
    header("Location: home.php?page=parametres");
    exit();
}

// Vérifier si le bouton de démarrage a été cliqué via la méthode GET
if ($_GET['start_listener'] === 'true') {
    startListener();
}
?>

