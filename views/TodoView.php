<!DOCTYPE html>
<html>
<head>
    <title>PHP - Aplikasi Todolist</title>
    <link href="assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .list-group-item { cursor: grab; }
        .list-group-item:active { cursor: grabbing; }
        .ghost-class { opacity: 0.5; background: #c8ebfb; }
    </style>
</head>
<body>
<div class="container-fluid p-5">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Todo List</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTodo">Tambah Data</button>
            </div>
            <hr />

            <div class="d-flex justify-content-between mb-3">
                <div class="btn-group">
                    <a href="?filter=all&search=<?= htmlspecialchars($search) ?>" class="btn btn-outline-secondary <?= $filter == 'all' ? 'active' : '' ?>">Semua</a>
                    <a href="?filter=finished&search=<?= htmlspecialchars($search) ?>" class="btn btn-outline-secondary <?= $filter == 'finished' ? 'active' : '' ?>">Selesai</a>
                    <a href="?filter=unfinished&search=<?= htmlspecialchars($search) ?>" class="btn btn-outline-secondary <?= $filter == 'unfinished' ? 'active' : '' ?>">Belum Selesai</a>
                </div>
                <form class="d-flex" method="GET">
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                    <input class="form-control me-2" type="search" name="search" placeholder="Cari todo..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-success" type="submit">Cari</button>
                </form>
            </div>

            <ul class="list-group" id="todo-list">
                <?php if (!empty($todos)): ?>
                    <?php foreach ($todos as $todo): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $todo['id'] ?>">
                            <div class="d-flex align-items-center">
                                <span class="<?= $todo['is_finished'] === 't' ? 'text-decoration-line-through text-muted' : '' ?>">
                                    <?= htmlspecialchars($todo['title']) ?>
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-info" onclick="showDetailModal(<?= $todo['id'] ?>)">Detail</button>
                                <button class="btn btn-sm btn-warning" onclick="showModalEditTodo(
                                    <?= $todo['id'] ?>, 
                                    <?= htmlspecialchars(json_encode($todo['title']), ENT_QUOTES, 'UTF-8') ?>, 
                                    <?= htmlspecialchars(json_encode($todo['description']), ENT_QUOTES, 'UTF-8') ?>, 
                                    <?= $todo['is_finished'] === 't' ? '1' : '0' ?>
                                )">Ubah</button>
                                <button class="btn btn-sm btn-danger" onclick="showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>')">Hapus</button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-center text-muted">Belum ada data tersedia!</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="addTodo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputTitle" class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" id="inputTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" id="inputDescription" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTodo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=update" method="POST">
                <input name="id" type="hidden" id="inputEditTodoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputEditTitle" class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" id="inputEditTitle" required>
                    </div>
                     <div class="mb-3">
                        <label for="inputEditDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" id="inputEditDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="selectEditStatus" class="form-label">Status</label>
                        <select class="form-select" name="is_finished" id="selectEditStatus">
                            <option value="0">Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteTodo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Kamu akan menghapus todo <strong class="text-danger" id="deleteTodoTitle"></strong>. Apakah kamu yakin?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="btnDeleteTodo" class="btn btn-danger">Ya, Tetap Hapus</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTodoTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Deskripsi:</h6>
                <p id="detailTodoDescription"></p>
                <hr>
                <small class="text-muted">Dibuat: <span id="detailTodoCreated"></span></small><br>
                <small class="text-muted">Diperbarui: <span id="detailTodoUpdated"></span></small>
            </div>
        </div>
    </div>
</div>


<script src="assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    const todoListEl = document.getElementById('todo-list');
    if (todoListEl) {
        new Sortable(todoListEl, {
            animation: 150,
            ghostClass: 'ghost-class',
            onEnd: function (evt) {
                const todoIds = Array.from(todoListEl.children).map(item => item.dataset.id);
                fetch('?page=reorder', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', },
                    body: 'todoIds[]=' + todoIds.join('&todoIds[]=')
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) { console.error('Gagal menyimpan urutan.'); }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }

    function showDetailModal(todoId) {
        fetch(`?page=detail&id=${todoId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('detailTodoTitle').innerText = data.title;
                document.getElementById('detailTodoDescription').innerText = data.description || 'Tidak ada deskripsi.';
                document.getElementById('detailTodoCreated').innerText = new Date(data.created_at).toLocaleString('id-ID');
                document.getElementById('detailTodoUpdated').innerText = new Date(data.updated_at).toLocaleString('id-ID');
                var detailModal = new bootstrap.Modal(document.getElementById('detailTodo'));
                detailModal.show();
            });
    }

    function showModalEditTodo(todoId, title, description, is_finished) {
        document.getElementById("inputEditTodoId").value = todoId;
        document.getElementById("inputEditTitle").value = title;
        document.getElementById("inputEditDescription").value = description;
        document.getElementById("selectEditStatus").value = is_finished;
        var myModal = new bootstrap.Modal(document.getElementById("editTodo"));
        myModal.show();
    }

    function showModalDeleteTodo(todoId, title) {
        document.getElementById("deleteTodoTitle").innerText = title;
        document.getElementById("btnDeleteTodo").setAttribute("href", `?page=delete&id=${todoId}`);
        var myModal = new bootstrap.Modal(document.getElementById("deleteTodo"));
        myModal.show();
    }
</script>
</body>
</html>