<?php
session_start();
include 'config.php';

// Verwerken van het registratieformulier
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Check if username already exists
        $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $check_stmt->execute(['username' => $username]);
        if ($check_stmt->rowCount() > 0) {
            throw new Exception("Deze gebruikersnaam bestaat al.");
        }

        // Voeg de gebruiker toe aan de database
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        if (!$stmt->execute(['username' => $username, 'password' => $password])) {
            throw new Exception("Er is een fout opgetreden bij het aanmaken van je account.");
        }

        // Get the new user's ID
        $user_id = $pdo->lastInsertId();
        
        // Default lists to create
        $default_lists = [
            'Backlog',
            'To-do',
            'In Progress',
            'Done'
        ];
        
        // Create default lists for the new user
        $list_position = 0;
        $list_stmt = $pdo->prepare("INSERT INTO lists (user_id, name, position) VALUES (:user_id, :name, :position)");
        
        foreach ($default_lists as $list_name) {
            if (!$list_stmt->execute([
                'user_id' => $user_id,
                'name' => $list_name,
                'position' => $list_position
            ])) {
                throw new Exception("Er is een fout opgetreden bij het aanmaken van de standaard lijsten.");
            }
            $list_position++;
        }

        // Commit transaction
        $pdo->commit();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header("Location: plan.php");
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

include 'views/register_view.php';
?>
