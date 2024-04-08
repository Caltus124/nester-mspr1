<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete_user'])) {
        $userIdToDelete = $_POST['delete_user'];

        // Éviter de supprimer un utilisateur avec l'ID 1 (si nécessaire)
        if ($userIdToDelete != 1) {
            // Préparer et exécuter la requête DELETE
            $deleteQuery = "DELETE FROM user WHERE id = :userIdToDelete";
            $deleteStmt = $database->prepare($deleteQuery);
            $deleteStmt->bindParam(':userIdToDelete', $userIdToDelete, SQLITE3_INTEGER);
            $deleteStmt->execute();
            
            // Vous pouvez ajouter un message de succès ici si nécessaire
            $successMessage = 'Utilisateur supprimé avec succès.';
        } else {
            // Message d'erreur si l'utilisateur avec ID 1 est sélectionné
            $errorMessage = 'Impossible de supprimer cet utilisateur.';
        }
    }   

    // Assurez-vous que les champs nécessaires sont présents
    if (isset($_POST['nom'], $_POST['prenom'], $_POST['nom_utilisateur'], $_POST['type_utilisateur'], $_POST['email'], $_POST['mot_de_passe'])) {
        // Récupérer les valeurs du formulaire
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $nom_utilisateur = $_POST['nom_utilisateur'];
        $type_utilisateur = $_POST['type_utilisateur'];
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        // Hasher le mot de passe (utilisez une méthode plus sécurisée si possible, par exemple password_hash)
        $mot_de_passe_hash = hash('sha256', $mot_de_passe);

        // Connexion à la base de données SQLite (à adapter selon votre configuration)
        $dbFile = './database/nester.db';
        $database = new SQLite3($dbFile);

        // Préparer la requête d'insertion
        $insertQuery = $database->prepare('INSERT INTO user (nom, prenom, username, status, email, password) VALUES (:nom, :prenom, :username, :status, :email, :password)');
        
        // Définir les valeurs
        $insertQuery->bindValue(':nom', $nom, SQLITE3_TEXT);
        $insertQuery->bindValue(':prenom', $prenom, SQLITE3_TEXT);
        $insertQuery->bindValue(':username', $nom_utilisateur, SQLITE3_TEXT);
        $insertQuery->bindValue(':status', $type_utilisateur, SQLITE3_TEXT);
        $insertQuery->bindValue(':email', $email, SQLITE3_TEXT);
        $insertQuery->bindValue(':password', $mot_de_passe_hash, SQLITE3_TEXT);

        // Exécuter la requête d'insertion
        $result = $insertQuery->execute();

        if ($result) {
            $successMessage = 'successfully created user.';
        } else {
            $errorMessage = 'Error during user creation.';
        }

        // Fermer la connexion à la base de données
        $database->close();
    } else {
        $errorMessage = 'All form fields are mandatory.';
    }
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
        padding-top: 50px;
        margin-left: 270px;
    }

    .container {
        max-width: 80%;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 70px;
    }

    h1 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    table {
        margin-top: 30px;
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f5f5f5;
    }

    tr:hover {
        background-color: #ddd;
    }

    .actions {
        display: flex;
        gap: 10px;
    }

    .actions a {
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .modifier-btn {
        background-color: #052757;
        color: #fff;
    }

    .modifier-btn:hover {
        background-color: #084191;
    }

    .supprimer-btn {
        background-color: #dc3545;
        color: #fff;
        margin: 15px;
        padding: 5px;
    }

    .supprimer-btn:hover {
        background-color: #c82333;
    }
    select {
        width: 100%; /* Largeur du select */
        padding: 8px; /* Espacement interne */
        font-size: 16px; /* Taille de la police */
        border: 1px solid #ddd; /* Bordure */
        border-radius: 4px; /* Coins arrondis */
        background-color: #fff; /* Couleur de fond */
        color: #333; /* Couleur du texte */
        cursor: pointer; /* Curseur de la souris */
    }

    /* Style des options du select */
    select option {
        background-color: #fff; /* Couleur de fond */
        color: #333; /* Couleur du texte */
        font-size: 16px; /* Taille de la police */
    }

    .create-user-form h2 {
        font-size: 20px;
        color: #333;
        text-align: center;
        margin-bottom: 20px;
    }

    .create-user-form label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    .create-user-form input[type="text"],
    .create-user-form input[type="email"],
    .create-user-form input[type="password"],
    .create-user-form select {
        outline: none;
        width: 100%;
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    #btn {
        background-color: #052757;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        font-size: 18px;
        cursor: pointer;
    }

    #bts:hover {
        background-color: #084191;
    }
    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .green {
        background-color: green;
    }

    .red {
        background-color: red;
    }
    .success-message,
    .error-message {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    .success-message {
        background-color: #7ebd5e; /* Soft green */
        color: #fff;
    }

    .error-message {
        background-color: #e57373; /* Soft red */
        color: #fff;
    }
    #deleteButton {
        background-color: red;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        cursor: pointer;
    }

    #deleteButton:hover {
        background-color: darkred;
    }


</style>

<h1>Create a new user</h1>
<div class="container">

<div class="create-user-form">
        
        <form method="POST" action="home.php?page=user">
            <label for="nom">Laste name :</label>
            <input type="text" name="nom" id="nom" required><br><br>
            
            <label for="prenom">First name :</label>
            <input type="text" name="prenom" id="prenom" required><br><br>
            
            <label for="nom_utilisateur">Username :</label>
            <input type="text" name="nom_utilisateur" id="nom_utilisateur" required><br><br>
            
            <label for="type_utilisateur">Status :</label>
            <select name="type_utilisateur" id="type_utilisateur">
                <option value="admin">admin</option>
                <option value="modo">modo</option>
            </select><br><br>
            
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required><br><br>
            
            <label for="mot_de_passe">Password :</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required><br><br>
            <?php
            // Display success or error messages
            if (!empty($successMessage)) {
                echo '<div class="success-message">' . $successMessage . '</div>';
            }
            if (!empty($errorMessage)) {
                echo '<div class="error-message">' . $errorMessage . '</div>';
            }
            ?>
            <input id="btn" type="submit" value="Create">
        </form>
    </div>
</div>
<h1>Users list</h1>
<div class="container">
    
<form method="post" action="update_status.php">
    <table>
        <tr>
            <th>Id</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        // Connexion à la base de données SQLite (à adapter selon votre configuration)
        $dbFile = './database/nester.db';
        $database = new SQLite3($dbFile);

        // Requête SQL pour sélectionner tous les utilisateurs
        $sql = 'SELECT id, username, nom, email, status FROM user';
        $result = $database->query($sql);

        // Affichage des utilisateurs dans le tableau
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['username'] . '</td>';
            echo '<td>' . $row['nom'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td>';
            
            // Vérifier si l'ID est différent de 1 avant d'afficher le sélecteur
            if ($row['id'] != 1) {
                echo '<select name="new_status[' . $row['id'] . ']">';
                echo '<option value="admin" ' . ($row['status'] == 'admin' ? 'selected' : '') . '>admin</option>';
                echo '<option value="moderator" ' . ($row['status'] == 'moderator' ? 'selected' : '') . '>moderator</option>';
                echo '</select>';
            } else {
                // Si l'ID est 1, afficher simplement le statut sans sélecteur
                echo $row['status'];
            }
            
            echo '</td>';
            
            // Ajouter une colonne avec un bouton de suppression
            echo '<td>';
            if ($row['id'] != 1) {
                echo '<form method="post" action="home.php?page=user">';
                echo '<input type="hidden" name="delete_user" value="' . $row['id'] . '">';
                echo '<input type="submit" id="deleteButton" value="Delete">';
                echo '</form>';          
            }
            echo '</td>';
            
            echo '</tr>';
        }

        // Fermeture de la connexion à la base de données
        $database->close();
        ?>
    </table>
    <button id="btn" type="submit" style="margin-top: 30px;">Update status</button>
</form>

</div>
