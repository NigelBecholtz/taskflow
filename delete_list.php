<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$list_id = $data['id'] ?? null;

if (!$list_id) {
    echo json_encode(['success' => false, 'message' => 'List ID is required']);
    exit;
}

try {
    // Debug logging
    error_log("Attempting to delete list. List ID: $list_id, User ID: {$_SESSION['user_id']}");
    
    // Begin een transactie
    $pdo->beginTransaction();

    // Verwijder de lijst (taken worden automatisch verwijderd door CASCADE)
    $stmt = $pdo->prepare("DELETE FROM lists WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$list_id, $_SESSION['user_id']]);
    
    if (!$result) {
        throw new PDOException("Failed to delete list");
    }

    // Commit de transactie
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'List deleted successfully']);
} catch (PDOException $e) {
    // Rollback bij een fout
    $pdo->rollBack();
    error_log("Error deleting list: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 