<?php   
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nester | Home</title>
    <link rel="stylesheet" href="style.css" />
    <!-- Boxicons CSS -->
    <link flex href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBAXAv3bvd5Wir8iW-LJNyVG8ZlLgpylFg"></script>
    <link rel="shortcut icon" href="images/info.png" type="image/x-icon">


    <script src="script.js" defer></script>
  </head>
  <body>
    <nav class="sidebar locked">
      <div class="logo_items flex">
        <span class="nav_image">
          <img src="images/info.png" />
        </span>
        <span class="logo_name">NESTER</span>
        <i class="bx bx-x" id="sidebar-close"></i>
      </div>

      <div class="menu_container">
        <div class="menu_items">
          <ul class="menu_item">
            <div class="menu_title flex">
              <span class="title">Dasboard</span>
              <span class="line"></span>
            </div>
            <li class="item">
              <a href="home.php?page=agents" class="link flex">
                <i class='bx bx-map-alt' ></i>
                <span>Agents</span>
              </a>
              </li>
            <!-- <li class="item">
              <a href="home.php?page=statistiques" class="link flex">
                <i class='bx bx-stats' ></i>
                <span>Statistiques</span>
              </a>
              </li>
            </li> -->
          </ul>

          <ul class="menu_item">
            <div class="menu_title flex">
              <span class="title">Edition</span>
              <span class="line"></span>
            </div>
            <li class="item">
              <a href="home.php?page=deploy" class="link flex">
                <i class="bx bx-grid-alt"></i>
                <span>Deploy</span>
              </a>
            </li>
            <li class="item">
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
                        echo '<li class="item">';
                        echo '<a href="home.php?page=user" class="link flex">';
                        echo '<i class="bx bx-folder"></i>';
                        echo '<span>Utilisateurs</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    // Fermer la connexion à la base de données
                    $database->close();
                }
                ?>
          </ul>

          <ul class="menu_item">
            <div class="menu_title flex">
              <span class="title">Auther</span>
              <span class="line"></span>
            </div>
            <li class="item">
              <a href="home.php?page=parametres" class="link flex">
                <i class="bx bx-cog"></i>
                <span>Settings</span>
              </a>
            </li>
            <li class="item">
              <a href="logout.php" class="link flex">
                <i class='bx bx-exit'></i>
                <span>Log out</span>
              </a>
            </li>
          </ul>
        </div>
        <div class="sidebar_profile flex">
          <span class="nav_image">
            <img src="images/user.png" />
          </span>
          <div class="data_text">
            <span class="name"><?php echo $user; ?></span>
          </div>
        </div>
      </div>
    </nav>


    <?php
      // Récupérer le paramètre "page" de l'URL
      $page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

      // Chemin vers le répertoire contenant les pages PHP
      $pagesDirectory = dirname(__FILE__) . '/';

      // Vérifier si le paramètre "id" est présent dans l'URL
      $id = isset($_GET['id']) ? $_GET['id'] : null;

      // Si un paramètre "id" est présent dans l'URL, inclure la page details_ticket.php
      if ($id !== null && file_exists($pagesDirectory . 'information.php')) {
          include($pagesDirectory . 'information.php');
      } else {
          // Si "id" n'est pas présent ou si la page details_ticket.php n'existe pas,
          // vérifier si la page demandée existe
          if (file_exists($pagesDirectory . $page . '.php')) {
              // Inclure la page demandée
              include($pagesDirectory . $page . '.php');
          } else {
              // Page non trouvée, afficher une page d'erreur ou rediriger vers une page par défaut
              echo "Page non trouvée";
          }
      }
    ?>




  </body>
<style>
/* Import Google font - Poppins */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

#map {
  position: absolute;
  top: 0;
  left: 270px; /* La largeur de votre barre de navigation */
  width: calc(100% - 270px); /* Prend la largeur restante */
  height: 100vh; /* Hauteur à 100 % de la vue */
  z-index: 0; /* Mettez la carte en arrière-plan */
  margin-left: 10px; /* Marge à gauche pour laisser de l'espace entre la carte et la barre de navigation */
}
body {
  min-height: 100vh;
  background: #eef5fe;
}
/* Pre css */
.flex {
  display: flex;
  align-items: center;
}
.nav_image {
  display: flex;
  min-width: 55px;
  justify-content: center;
}
.nav_image img {
  height: 35px;
  width: 35px;
  object-fit: cover;
}

/* Sidebar */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 270px;
  background: #fff;
  padding: 15px 10px;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.1);
  transition: all 0.4s ease;
}
.sidebar.close {
  width: calc(55px + 20px);
}
.logo_items {
  gap: 8px;
}
.logo_name {
  font-size: 22px;
  color: #333;
  font-weight: 500px;
  transition: all 0.3s ease;
}
.sidebar.close .logo_name,
.sidebar.close #lock-icon,
.sidebar.close #sidebar-close {
  opacity: 0;
  pointer-events: none;
}
#lock-icon,
#sidebar-close {
  padding: 10px;
  color: #4070f4;
  font-size: 25px;
  cursor: pointer;
  margin-left: -4px;
  transition: all 0.3s ease;
}
#sidebar-close {
  display: none;
  color: #333;
}
.menu_container {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  margin-top: 40px;
  overflow-y: auto;
  height: calc(100% - 82px);
}
.menu_container::-webkit-scrollbar {
  display: none;
}
.menu_title {
  position: relative;
  height: 50px;
  width: 55px;
}
.menu_title .title {
  margin-left: 15px;
  transition: all 0.3s ease;
}
.sidebar.close .title {
  opacity: 0;
}
.menu_title .line {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  height: 3px;
  width: 20px;
  border-radius: 25px;
  background: #aaa;
  transition: all 0.3s ease;
}
.menu_title .line {
  opacity: 0;
}
.sidebar.close .line {
  opacity: 1;
}
.item {
  list-style: none;
}
.link {
  text-decoration: none;
  border-radius: 8px;
  margin-bottom: 8px;
  color: #707070;
}
.link:hover {
  color: #fff;
  background-color: #4070f4;
}
.link span {
  white-space: nowrap;
}
.link i {
  height: 50px;
  min-width: 55px;
  display: flex;
  font-size: 22px;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.sidebar_profile {
  padding-top: 15px;
  margin-top: 15px;
  gap: 15px;
  border-top: 2px solid rgba(0, 0, 0, 0.1);
}
.sidebar_profile .name {
  font-size: 18px;
  color: #333;
}
.sidebar_profile .email {
  font-size: 15px;
  color: #333;
}

/* Navbar */
.navbar {
  max-width: 500px;
  width: 100%;
  position: fixed;
  top: 0;
  left: 60%;
  transform: translateX(-50%);
  background: #fff;
  padding: 10px 20px;
  border-radius: 0 0 8px 8px;
  justify-content: space-between;
}
#sidebar-open {
  font-size: 30px;
  color: #333;
  cursor: pointer;
  margin-right: 20px;
  display: none;
}
.search_box {
  height: 46px;
  max-width: 500px;
  width: 100%;
  border: 1px solid #aaa;
  outline: none;
  border-radius: 8px;
  padding: 0 15px;
  font-size: 18px;
  color: #333;
}
.navbar img {
  height: 40px;
  width: 40px;
  margin-left: 20px;
}

/* Responsive */
@media screen and (max-width: 1100px) {
  .navbar {
    left: 65%;
  }
}
@media screen and (max-width: 800px) {
  .sidebar {
    left: 0;
    z-index: 1000;
  }
  .sidebar.close {
    left: -100%;
  }
  #sidebar-close {
    display: block;
  }
  #lock-icon {
    display: none;
  }
  .navbar {
    left: 0;
    max-width: 100%;
    transform: translateX(0%);
  }
  #sidebar-open {
    display: block;
  }
}

</style>
</html>