<?php
$server = "smtp.ionos.fr"; // Serveur SMTP de Ionos
$port = 587; // Port du serveur SMTP

// Essai de connexion au serveur
$connection = @fsockopen($server, $port, $errno, $errstr, 10);

if ($connection) {
    echo "Connexion réussie au serveur SMTP $server sur le port $port";
    fclose($connection);
} else {
    echo "Échec de la connexion : $errstr ($errno)";
}
?>
