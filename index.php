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

    $query = $database->prepare('SELECT * FROM user WHERE username = :username');
    $query->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $query->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Enregistrez les informations de l'utilisateur dans la session
        $_SESSION['user'] = $user;

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

    <h2>Login</h2>

    <?php
    if (isset($error_message)) {
        echo '<p style="color: red;">' . $error_message . '</p>';
    }
    ?>

    <form action="index.php" method="post">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>

</body>
</html>
