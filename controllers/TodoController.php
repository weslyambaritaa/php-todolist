<?php
require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        session_start(); // Mulai session untuk mengambil pesan notifikasi
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
                // INI BAGIAN PENTING: Atur pesan error di session
                $_SESSION['error_message'] = 'Judul todo "' . htmlspecialchars($title) . '" sudah ada! Gagal menambahkan.';
            } else {
                $todoModel->createTodo($title, $description);
                // (Opsional) Beri pesan sukses jika berhasil
                $_SESSION['success_message'] = 'Todo berhasil ditambahkan!';
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

            session_start(); // Mulai session untuk menyimpan pesan notifikasi
            
            // Validasi judul duplikat (opsional, tapi bagus untuk ada)
            if ($todoModel->isTitleExists($title, $id)) {
                // Handle error jika judul sudah ada
                $_SESSION['error_message'] = 'Judul todo "' . htmlspecialchars($title) . '" sudah ada! Gagal memperbarui.';
            } else {
                // Kirim semua data ke model
                $todoModel->updateTodo($id, $title, $description, $is_finished);
                $_SESSION['success_message'] = 'Todo berhasil diperbarui!';
            }
        }
        header('Location: index.php');
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            session_start(); // Mulai session untuk menyimpan pesan notifikasi
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            if ($todoModel->deleteTodo($id)) {
                $_SESSION['success_message'] = 'Todo berhasil dihapus.';
            } else {
                $_SESSION['error_message'] = 'Gagal menghapus todo.';
            }
        }
        header('Location: index.php');
    }

    // ... (method detail() dan reorder() tetap sama) ...
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