<?php
// // Databaseverbinding
// $host = '';
// $db = ''; // vervang dit door je database naam
// $user = ''; // vervang dit door je database gebruiker
// $pass = ''; // vervang dit door je database wachtwoord

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Verbinding mislukt: " . $e->getMessage();
}
?>
