<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    exit(json_encode(['error' => 'Unauthorized']));
}

$taskId = $_GET['id'];

try {
    // Get task details
    $stmt = $pdo->prepare("SELECT * FROM todos WHERE id = :id");
    $stmt->execute(['id' => $taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get assigned users
    $stmt = $pdo->prepare("
        SELECT u.id, u.username 
        FROM users u 
        JOIN task_users tu ON u.id = tu.user_id 
        WHERE tu.task_id = :task_id
    ");
    $stmt->execute(['task_id' => $taskId]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $task['assigned_users'] = $users;
    
    echo json_encode($task);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 