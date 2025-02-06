<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['error' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Get current position of tasks in the target status
    $stmt = $pdo->prepare("
        SELECT id, position 
        FROM todos 
        WHERE status = :status 
        ORDER BY position ASC
    ");
    $stmt->execute(['status' => $data['status']]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Update positions of other tasks
    $stmt = $pdo->prepare("
        UPDATE todos 
        SET position = position + 1 
        WHERE status = :status 
        AND position >= :new_position
    ");
    $stmt->execute([
        'status' => $data['status'],
        'new_position' => $data['position']
    ]);

    // Update the dragged task
    $stmt = $pdo->prepare("
        UPDATE todos 
        SET status = :status, position = :position 
        WHERE id = :id
    ");
    $success = $stmt->execute([
        'status' => $data['status'],
        'position' => $data['position'],
        'id' => $data['taskId']
    ]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    // Rollback on error
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
} 