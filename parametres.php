<?php
session_start();

// Include your database connection code here
$pdo = new PDO('sqlite:./database/nester.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];

$message = ""; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user inputs from the form
    $username = $_SESSION['user']; // Get the username from the session
    $oldPassword = $_POST["ancien_mot_de_passe"];
    $newPassword = $_POST["nouveau_mot_de_passe"];
    $confirmPassword = $_POST["confirmer_mot_de_passe"];

    // Validate input (you may want to add more validation)
    if ($newPassword != $confirmPassword) {
        $message = "Les nouveaux mots de passe ne correspondent pas.";
        // You might want to redirect the user or handle this error appropriately
    } else {
        // Hash the provided old password with SHA-256
        $hashedOldPassword = hash('sha256', $oldPassword);

        // Retrieve the hashed password from the database for the specified user
        $query = "SELECT password FROM user WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the old password matches the one retrieved from the database
        if ($hashedOldPassword === $userData['password']) {
            // Hash the new password using SHA-256
            $hashedNewPassword = hash('sha256', $newPassword);

            // Update the password in the database
            $updateQuery = "UPDATE user SET password = :newPassword WHERE username = :username";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':newPassword', $hashedNewPassword, PDO::PARAM_STR);
            $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            $updateStmt->execute();

            $message = "Mot de passe mis à jour avec succès.";
            // You might want to redirect the user to a success page or handle this success appropriately
        } else {
            $message = "Ancien mot de passe incorrect.";
            // You might want to redirect the user or handle this error appropriately
        }
    }
}



// Fonction pour démarrer l'écouteur
function startListener() {
    $_SESSION['listener_status'] = 'active';
    exec('nohup php ecouteur.php > log.txt 2>&1 &');
}

// Fonction pour arrêter l'écouteur
function stopListener() {
    unset($_SESSION['listener_status']);
    // Vous pouvez ajouter ici du code pour terminer le processus de l'écouteur
}

// Vérifier si l'écouteur est actif
$listenerActive = isset($_SESSION['listener_status']);

// Vérifier si le bouton de démarrage a été cliqué
if (isset($_GET['start_listener'])) {
    startListener();
}

// Vérifier si le bouton d'arrêt a été cliqué
if (isset($_GET['stop_listener'])) {
    stopListener();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Mot de Passe</title>
    <style>
        /* Style général pour le formulaire */
        form {
            max-width: 60%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            margin-top: 50px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="text"] {
            display: none;
        }

        input[type="submit"], button{
            display: block;
            padding: 10px;
            background-color: #4070f4;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #0056b3;
        }

        .result-message {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            font-weight: bold;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        .form-container {
            margin-left: 270px;
        }

        .container {
            max-width: 60%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 70px;
        }
        .btn-red{
            display: block;
            padding: 10px;
            background-color: red;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .btn-red:hover{
            background-color: #EA3F1A;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Modifier le Mot de Passe</h1>

    <form action="home.php?page=parametres" method="post">
    <label for="ancien_mot_de_passe">Ancien mot de passe :</label>
    <input type="password" name="ancien_mot_de_passe" id="ancien_mot_de_passe" required>
    <br>
    <label for="nouveau_mot_de_passe">Nouveau mot de passe :</label>
    <input type="password" name="nouveau_mot_de_passe" id="nouveau_mot_de_passe" required>
    <br>
    <label for="confirmer_mot_de_passe">Confirmer le nouveau mot de passe :</label>
    <input type="password" name="confirmer_mot_de_passe" id="confirmer_mot_de_passe" required>
    <br>
    <?php if (!empty($message)) : ?>
        <p class="message <?php echo (strpos($message, "Mot de passe mis à jour avec succès") !== false) ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    <input type="submit" value="Modifier le mot de passe">
</form>

<h1>Gestion de l'écouteur</h1>
<form action="start_listener.php" method="get">
    <input type="hidden" name="start_listener" value="true">
    <input type="submit" value="Restart écouteur">
</form>

<?php
if (isset($_SESSION['user'])) {
    // Connexion à la base de données SQLite (à adapter selon votre configuration)
    $dbFile = './database/nester.db';
    $database = new SQLite3($dbFile);

    // Récupérer le statut de l'utilisateur depuis la base de données
    $username = $_SESSION['user'];
    $query = $database->prepare('SELECT status FROM user WHERE username = :username');
    $query->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $query->execute();
    $userData = $result->fetchArray(SQLITE3_ASSOC);

    // Vérifier si la requête a réussi et si le statut est 'admin'
    if ($userData && $userData['status'] === 'admin') {
        // L'utilisateur est un administrateur, affichez le lien "Utilisateurs"
        echo '<h1 style="color: red;">Zone dangereuse</h1>';

        echo '<form action="create_user_db.php" method="get">';
        echo '<p>Base de données des users</p><br>';
        echo '<button type="submit" class="btn-red">Supprimer</button>';
        echo '</form><br>';
        
        echo '<form action="create_scan_db.php" method="get">';
        echo '<p>Base de données des scans</p><br>';
        echo '<button type="submit" class="btn-red">Supprimer</button>';
        echo '</form><br>';

        echo '<form action="check_db.php" method="get">';
        echo '<p>Vérifier la base de donnée</p><br>';
        echo '<button type="submit" class="btn-red">Vérifier</button>';
        echo '</form><br>';
    }

    // Fermer la connexion à la base de données
    $database->close();
}
?>

</div>

</body>
</html>
