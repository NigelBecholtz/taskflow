<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['error' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("
        UPDATE todos 
        SET task = :task, 
            description = :description, 
            status = :status,
            priority = :priority
        WHERE id = :id 
        AND user_id = :user_id
    ");
    $success = $stmt->execute([
        'task' => $data['task'],
        'description' => $data['description'],
        'status' => $data['status'],
        'priority' => $data['priority'],
        'id' => $data['id'],
        'user_id' => $_SESSION['user_id']
    ]);

    echo json_encode([
        'success' => $success,
        'task' => $data['task'],
        'priority' => $data['priority'],
        'status' => $data['status']
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 