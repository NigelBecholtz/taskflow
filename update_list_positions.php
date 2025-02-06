<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['error' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        UPDATE lists 
        SET position = :position 
        WHERE id = :id 
        AND user_id = :user_id
    ");

    foreach ($data['positions'] as $item) {
        $stmt->execute([
            'position' => $item['position'],
            'id' => $item['id'],
            'user_id' => $_SESSION['user_id']
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
} 