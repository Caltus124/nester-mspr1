<?php
session_start();

// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Récupérez les informations de l'utilisateur depuis la session
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>

    <h2>Bienvenue, <?php echo $user['username']; ?>!</h2>
    <p>Statut: <?php echo $user['status']; ?></p>

    <a href="logout.php">Déconnexion</a>

</body>
</html>
