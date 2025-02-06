<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$list_id = $data['list_id'] ?? null;

if (!$list_id) {
    echo json_encode(['success' => false, 'message' => 'List ID is required']);
    exit;
}

try {
    // Begin een transactie
    $pdo->beginTransaction();

    // Verwijder eerst alle taken die bij deze lijst horen
    $stmt = $pdo->prepare("DELETE FROM todos WHERE list_id = ? AND user_id = ?");
    $result = $stmt->execute([$list_id, $_SESSION['user_id']]);
    
    if (!$result) {
        throw new PDOException("Failed to delete tasks");
    }

    // Commit de transactie
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Tasks deleted successfully']);
} catch (PDOException $e) {
    // Rollback bij een fout
    $pdo->rollBack();
    error_log("Error deleting tasks: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 