<?php
// Databaseverbinding
$host = 'database-5017150372.webspace-host.com';
$db = 'dbs13783528'; // vervang dit door je database naam
$user = 'dbu2231100'; // vervang dit door je database gebruiker
$pass = 'QK1HETX@g!'; // vervang dit door je database wachtwoord

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Verbinding mislukt: " . $e->getMessage();
}
?>
