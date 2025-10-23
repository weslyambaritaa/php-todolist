<?php
require_once (__DIR__ . '/../config.php');

class TodoModel
{
    private $conn;

    public function __construct()
    {
        // Koneksi database Anda tetap sama
        $this->conn = pg_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASSWORD);
        if (!$this->conn) {
            die('Koneksi database gagal');
        }
    }

    public function getAllTodos($filter = 'all', $search = '')
    {
        // Query dasar dengan pengurutan berdasarkan display_order
        $query = 'SELECT * FROM todo';
        $params = [];
        $whereClauses = [];

        // Logika untuk Filter
        if ($filter === 'finished') {
            $whereClauses[] = 'is_finished = true';
        } elseif ($filter === 'unfinished') {
            $whereClauses[] = 'is_finished = false';
        }

        // Logika untuk Pencarian
        if (!empty($search)) {
            // Menggunakan ILIKE untuk pencarian case-insensitive di PostgreSQL
            $whereClauses[] = '(title ILIKE $1 OR description ILIKE $1)';
            $params[] = '%' . $search . '%';
        }
        
        if (!empty($whereClauses)) {
            $query .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        $query .= ' ORDER BY display_order ASC';

        $result = pg_query_params($this->conn, $query, $params);
        $todos = [];
        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                $todos[] = $row;
            }
        }
        return $todos;
    }

    public function getTodoById($id)
    {
        $query = 'SELECT * FROM todo WHERE id = $1';
        $result = pg_query_params($this->conn, $query, [$id]);
        return pg_fetch_assoc($result);
    }

    public function createTodo($title, $description)
    {
        $query = 'INSERT INTO todo (title, description, created_at, updated_at) VALUES ($1, $2, NOW(), NOW())';
        $result = pg_query_params($this->conn, $query, [$title, $description]);
        return $result !== false;
    }

    public function updateTodo($id, $title, $description, $is_finished)
    {
        // FIX: Ubah '0'/'1' menjadi string 'false'/'true' agar pasti dibaca oleh PostgreSQL
        $is_finished_sql = ($is_finished == '1') ? 'true' : 'false';

        $query = 'UPDATE todo SET title=$1, description=$2, is_finished=$3, updated_at=NOW() WHERE id=$4';
        // Kirim nilai string 'true'/'false' yang sudah dikonversi
        $result = pg_query_params($this->conn, $query, [$title, $description, $is_finished_sql, $id]);
        return $result !== false;
    }

    public function deleteTodo($id)
    {
        $query = 'DELETE FROM todo WHERE id=$1';
        $result = pg_query_params($this->conn, $query, [$id]);
        return $result !== false;
    }

    public function isTitleExists($title, $id = null)
    {
        $query = 'SELECT id FROM todo WHERE title = $1';
        $params = [$title];
        if ($id) {
            $query .= ' AND id != $2';
            $params[] = $id;
        }
        $result = pg_query_params($this->conn, $query, $params);
        return pg_num_rows($result) > 0;
    }

    public function updateOrder($todoIds)
    {
        pg_query($this->conn, 'BEGIN'); // Mulai transaksi
        foreach ($todoIds as $index => $id) {
            $order = $index + 1;
            $query = 'UPDATE todo SET display_order=$1, updated_at=NOW() WHERE id=$2';
            $result = pg_query_params($this->conn, $query, [$order, $id]);
            if (!$result) {
                pg_query($this->conn, 'ROLLBACK'); // Batalkan jika ada error
                return false;
            }
        }
        pg_query($this->conn, 'COMMIT'); // Simpan jika semua berhasil
        return true;
    }
}