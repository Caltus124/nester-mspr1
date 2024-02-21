<?php
session_start();

// Vérifiez si l'utilisateur est déjà connecté, redirigez-le vers la page d'accueil
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbFile = './database/nester.db';
    $database = new SQLite3($dbFile);

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Utilisez une requête préparée pour empêcher les injections SQL
    $query = $database->prepare('SELECT * FROM user WHERE username = :username');
    $query->bindValue(':username', $username, SQLITE3_TEXT);

    $result = $query->execute();

    // Utilisez fetchArray avec SQLITE3_ASSOC pour obtenir un tableau associatif
    $user = $result->fetchArray(SQLITE3_ASSOC);
    // Utilisez hash_equals pour comparer les hachages de manière sécurisée
    if ($user && hash_equals($user['password'], hash('sha256', $password))) {
        // Enregistrez les informations de l'utilisateur dans la session

        session_start();
        $_SESSION['user'] = $user['username'];

        // Redirigez l'utilisateur vers la page d'accueil
        header("Location: home.php");
        exit();
    } else {
        $error_message = 'Nom d\'utilisateur ou mot de passe incorrect.';
    }

    $database->close();
}




?>
<!DOCTYPE html>
<html>
<head>
    <title>Nester | Connexion</title>
    <link rel="shortcut icon" href="images/info.png" type="image/x-icon">

</head>
<body>
    <form action="./index.php" method="post" autocomplete="off">
        <h2>Login</h2><br>
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required autocomplete="off"><br>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br>
        <?php
        if (isset($error_message)) {
            echo '<p style="color: red; margin-bottom: 30px;">' . $error_message . '</p>';
        }
        ?>
        <input type="submit" value="Connexion">
        <?php
        // Vérifier si le paramètre error est défini et égal à 2 (erreur d'identifiant ou de mot de passe incorrect)
        if (isset($_GET['error']) && $_GET['error'] == 1) {
            echo '<p style="color: red;">Vous vous n\'êtes pas administrateur</p>';
        }
        if (isset($_GET['error']) && $_GET['error'] == 2) {
            echo '<p style="color: red;">Identifiant ou mot de passe incorrect</p>';
        }
        ?>
    </form>
</body>
<style>
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

h2 {
    text-align: center;
    color: #333;
    margin: 30px,
}


/* Style de base pour le corps de la page */
body {
    font-family: Arial, sans-serif;
    background-color: #eef5fe;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Style du formulaire */
form {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    width: 300px;
}

/* Style des libellés et des champs de saisie */
label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}

input[type="text"],
input[type="password"] {
    outline: none;
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 16px;
}


/* Style du bouton de soumission */
input[type="submit"] {
    background-color: #007BFF;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 3px;
    font-size: 16px;
    cursor: pointer;
    margin-bottom: 15px;
}

/* Style du lien "S'inscrire" */
p a {
    color: #007BFF;
    text-decoration: none;
}

/* Style du lien "S'inscrire" au survol */
p a:hover {
    text-decoration: underline;
}

</style>
</html>