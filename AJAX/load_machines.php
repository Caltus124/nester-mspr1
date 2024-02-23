<?php
// Chemin vers la base de données SQLite
$db_path = '../database/nester.db';

try {
    // Connexion à la base de données SQLite
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données de la table machine
    $query = $db->query('SELECT * FROM system_info');
    $machinesHtml = '';

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $agentId = $row['id']; // Supposons que l'ID de l'agent soit dans la colonne 'id'
        $status = $row['status_machine'];
        $dotColorClass = ($status == 'disable') ? 'red-dot' : 'green-dot';
        $machinesHtml .= "<a href='home.php?page=info_agent&ids=$agentId' class='agent-link'>";
        $machinesHtml .= "<div class='machine-container'>";
        $machinesHtml .= "<div class='machine-info'><div class='text'>{$row['machine_name']}</div></div>";
        $machinesHtml .= "<div class='machine-info'><div class='text'>{$row['ip_address']}</div></div>";        
        // Ajouter l'image du logo de l'OS en fonction du type d'OS
        $os_logo_path = getOsLogoPath($row['os_info']);
        if ($os_logo_path) {
            $machinesHtml .= "<div class='machine-info'><img src='{$os_logo_path}' alt='Logo OS' class='machine-image'></div>";
        }
        $machinesHtml .= "<div class='machine-info'><div class='text $dotColorClass'>.</div></div>";        
        $machinesHtml .= "</div>";
        $machinesHtml .= "</a>";
    }
    
    

    echo $machinesHtml;
} catch (PDOException $e) {
    // En cas d'erreur, affichage du message d'erreur
    echo 'Erreur : ' . $e->getMessage();
}

// Fonction pour obtenir le chemin d'accès au logo de l'OS en fonction du type d'OS
function getOsLogoPath($type_os) {
    switch ($type_os) {
        case 'Windows':
            return './images/windows_logo.png'; // Chemin vers le logo de Windows
        case 'Linux':
            return './images/linux_logo.png'; // Chemin vers le logo de Linux
        case 'Darwin':
            return './images/macos_logo.png'; // Chemin vers le logo de MacOS
        default:
            return ''; // Si le type d'OS n'est pas reconnu, retourner une chaîne vide
    }
}
?>

