<?php
// Zet error reporting aan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors naar een bestand
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

error_log("Loading plan.php for user_id: " . $_SESSION['user_id']);

// Debug query voor taken
$debug_stmt = $pdo->prepare("
    SELECT t.*, l.name as list_name 
    FROM todos t 
    JOIN lists l ON t.list_id = l.id 
    WHERE t.user_id = ?
");
$debug_stmt->execute([$_SESSION['user_id']]);
$debug_todos = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);
error_log("Debug: Found " . count($debug_todos) . " tasks in database");
foreach ($debug_todos as $todo) {
    error_log("Debug: Task '{$todo['task']}' in list '{$todo['list_name']}' (list_id: {$todo['list_id']})");
}

// Debug query voor lijsten
$debug_stmt = $pdo->prepare("SELECT * FROM lists WHERE user_id = ?");
$debug_stmt->execute([$_SESSION['user_id']]);
$debug_lists = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);
error_log("Debug: Found " . count($debug_lists) . " lists");
foreach ($debug_lists as $list) {
    error_log("Debug: List '{$list['name']}' (ID: {$list['id']})");
}

// Taak toevoegen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $priority = $_POST['priority'];
    $category = $_POST['category'];
    $tags = $_POST['tags'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO todos (
                task, 
                description, 
                list_id,
                due_date,
                priority,
                category,
                tags,
                user_id
            ) VALUES (
                :task, 
                :description, 
                :list_id,
                :due_date,
                :priority,
                :category,
                :tags,
                :user_id
            )
        ");
        
        $stmt->execute([
            'task' => $task,
            'description' => $description,
            'list_id' => $status,
            'due_date' => $due_date,
            'priority' => $priority,
            'category' => $category,
            'tags' => $tags,
            'user_id' => $_SESSION['user_id']
        ]);
    } catch (PDOException $e) {
        echo "Error adding task: " . $e->getMessage();
    }
}

// Taak verwijderen
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// Taak bewerken
if (isset($_POST['edit_task'])) {
    $id = $_POST['id'];
    $task = $_POST['task'];
    $due_date = $_POST['due_date'];

    $stmt = $pdo->prepare("UPDATE todos SET task = :task, due_date = :due_date WHERE id = :id");
    $stmt->execute(['task' => $task, 'due_date' => $due_date, 'id' => $id]);
}

// Taak verplaatsen
if (isset($_POST['move_task'])) {
    $id = $_POST['id'];
    $new_status = $_POST['new_status'];

    $stmt = $pdo->prepare("UPDATE todos SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $new_status, 'id' => $id]);
}

// Update de query voor het ophalen van lijsten
$stmt = $pdo->prepare("
    SELECT * FROM lists 
    WHERE user_id = :user_id 
    ORDER BY position ASC
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update de query voor het ophalen van taken
$stmt = $pdo->prepare("
    SELECT t.*, l.name as list_name, l.color as list_color 
    FROM todos t 
    LEFT JOIN lists l ON t.list_id = l.id 
    WHERE t.user_id = :user_id 
    ORDER BY t.position ASC
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load the view
require_once 'views/plan_view.php';
?>