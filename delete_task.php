<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['error' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Controleer eerst of de taak van de gebruiker is
    $stmt = $pdo->prepare("
        DELETE FROM todos 
        WHERE id = :id AND user_id = :user_id
    ");
    $success = $stmt->execute([
        'id' => $data['id'],
        'user_id' => $_SESSION['user_id']
    ]);
    
    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 