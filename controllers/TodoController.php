<?php
require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        $todoModel = new TodoModel();
        // Ambil nilai filter dan search dari URL, berikan nilai default jika tidak ada
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $todos = $todoModel->getAllTodos($filter, $search);
        include (__DIR__ . '/../views/TodoView.php');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $todoModel = new TodoModel();

            // Validasi judul duplikat
            if ($todoModel->isTitleExists($title)) {
                // Anda bisa menambahkan notifikasi error di sini menggunakan session
                // Contoh: $_SESSION['error_message'] = 'Judul todo sudah ada!';
            } else {
                $todoModel->createTodo($title, $description);
            }
        }
        header('Location: index.php');
    }

public function update()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        // Pastikan kita mengambil nilai 'is_finished' yang dikirim dari form
        $is_finished = $_POST['is_finished']; // Ini akan bernilai '0' atau '1'
        $todoModel = new TodoModel();
        
        // Validasi judul duplikat (opsional, tapi bagus untuk ada)
        if ($todoModel->isTitleExists($title, $id)) {
            // Handle error jika judul sudah ada
            // Contoh: $_SESSION['error_message'] = 'Judul todo sudah ada!';
        } else {
            // Kirim semua data ke model
            $todoModel->updateTodo($id, $title, $description, $is_finished);
        }
    }
    header('Location: index.php');
}

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $todoModel->deleteTodo($id);
        }
        header('Location: index.php');
    }

    // Method baru untuk detail
    public function detail()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $todo = $todoModel->getTodoById($id);
            // Kirim data sebagai JSON
            header('Content-Type: application/json');
            echo json_encode($todo);
            exit; // Penting untuk menghentikan eksekusi script setelah mengirim JSON
        }
    }

    // Method baru untuk re-ordering
    public function reorder()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todoIds'])) {
            $todoIds = $_POST['todoIds'];
            $todoModel = new TodoModel();
            if ($todoModel->updateOrder($todoIds)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan urutan']);
            }
            exit; // Penting
        }
    }
}