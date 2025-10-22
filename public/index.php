<?php
// Pastikan session_start() ada jika Anda ingin menggunakan notifikasi error via session
// session_start(); 

require_once (__DIR__ . '/../controllers/TodoController.php');

$page = isset($_GET['page']) ? $_GET['page'] : 'index';
$todoController = new TodoController();

switch ($page) {
    case 'create':
        $todoController->create();
        break;
    case 'update':
        $todoController->update();
        break;
    case 'delete':
        $todoController->delete();
        break;
    // Tambahkan case baru untuk detail dan reorder
    case 'detail':
        $todoController->detail();
        break;
    case 'reorder':
        $todoController->reorder();
        break;
    default:
        $todoController->index();
        break;
}