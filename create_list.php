<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Haal eerst de hoogste positie op
    $stmt = $pdo->prepare("SELECT MAX(position) as max_pos FROM lists");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $newPosition = ($result['max_pos'] ?? 0) + 1;

    // Voeg de nieuwe lijst toe met de berekende positie
    $stmt = $pdo->prepare("INSERT INTO lists (name, color, position, user_id) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([
        $data['name'],
        $data['color'],
        $newPosition,
        $_SESSION['user_id']
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create list']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 