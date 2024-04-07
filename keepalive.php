<?php
// Connexion à la base de données
$db = new PDO('sqlite:database/nester.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupération de toutes les adresses IP de la table system_info
$query = $db->query("SELECT ip_address FROM system_info");
$ip_addresses = $query->fetchAll(PDO::FETCH_COLUMN);

// Fonction pour effectuer un keep alive sur une adresse IP
function keepAlive($ip, $db) {
    // Ping de l'adresse IP
    exec("ping -c 1 $ip", $output, $result);

    // Vérification du résultat
    if ($result == 0) {
        echo "L'adresse IP $ip est accessible.\n";
        // Mettre à jour le statut de la machine dans la base de données
        $stmt = $db->prepare("UPDATE system_info SET status_machine = 'enable' WHERE ip_address = :ip_address");
        $stmt->execute([':ip_address' => $ip]);
    } else {
        echo "L'adresse IP $ip est inaccessible.\n";
        // Mettre à jour le statut de la machine dans la base de données
        $stmt = $db->prepare("UPDATE system_info SET status_machine = 'disable' WHERE ip_address = :ip_address");
        $stmt->execute([':ip_address' => $ip]);
    }
}

// Boucle pour effectuer le keep alive sur chaque adresse IP toutes les 10 secondes
while (true) {
    foreach ($ip_addresses as $ip) {
        keepAlive($ip, $db);
    }
    sleep(5); // Attente de 10 secondes avant la prochaine itération
}
?>
