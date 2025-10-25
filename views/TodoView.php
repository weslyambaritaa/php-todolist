<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP - Aplikasi Todolist Modern</title>
    
    <link href="assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    </head>
<body class="bg-body-tertiary">

<div class="container">
    <div class="col-lg-10 col-xl-9 mx-auto">

        <div class="d-flex justify-content-end pt-4 pe-2">
            <div class="dropdown">
                <button class="btn btn-link nav-link p-0" type="button" id="themeSwitcher" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-sun-fill" data-theme-icon="bi-sun-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="themeSwitcher">
                    <li><button class="dropdown-item d-flex align-items-center" type="button" data-bs-theme-value="light"><i class="bi bi-sun-fill me-2 opacity-75"></i> Light</button></li>
                    <li><button class="dropdown-item d-flex align-items-center" type="button" data-bs-theme-value="dark"><i class="bi bi-moon-stars-fill me-2 opacity-75"></i> Dark</button></li>
                    <li><button class="dropdown-item d-flex align-items-center" type="button" data-bs-theme-value="auto"><i class="bi bi-circle-half me-2 opacity-75"></i> Auto</button></li>
                </ul>
            </div>
        </div>
        <div class="card shadow-lg border-0 rounded-4 my-4">
            <div class="card-body p-4 p-md-5">

                <?php
                // --- BLOK UNTUK MENAMPILKAN ALERT ---
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . 
                         htmlspecialchars($_SESSION['error_message']) . 
                         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' .
                         '</div>';
                    unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
                }
                
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . 
                         htmlspecialchars($_SESSION['success_message']) . 
                         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' .
                         '</div>';
                    unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
                }
                // --- AKHIR BLOK ALERT ---
                ?>

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <h1 class="h3 fw-bolder text-primary-emphasis mb-0">My Tasks</h1>
                    <button class="btn btn-primary fw-semibold shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#addTodo">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Tugas Baru
                    </button>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between mb-4 gap-3">
                    <div class="btn-group shadow-sm rounded-pill" role="group">
                        <a href="?filter=all&search=<?= htmlspecialchars($search) ?>" class="btn btn-outline-secondary <?= $filter == 'all' ? 'active' : '' ?>"><i class="bi bi-collection me-2"></i>Semua</a>
                        <a href="?filter=finished&search=<?= htmlspecialchars($search) ?>" class="btn btn-outline-secondary <?= $filter == 'finished' ? 'active' : '' ?>"><i class="bi bi-check-circle me-2"></i>Selesai</a>
                        <a href="?filter=unfinished&search=<?= htmlspecialchars($search) ?>" class="btn btn-outline-secondary <?= $filter == 'unfinished' ? 'active' : '' ?>"><i class="bi bi-hourglass-split me-2"></i>Belum</a>
                    </div>
                    <form class="d-flex" method="GET">
                        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                        <div class="input-group shadow-sm rounded-pill">
                            <input class="form-control border-0" type="search" name="search" placeholder="Cari todo..." value="<?= htmlspecialchars($search) ?>" style="border-top-left-radius: 50rem; border-bottom-left-radius: 50rem;">
                            <button class="btn btn-outline-success border-0" type="submit" style="border-top-right-radius: 50rem; border-bottom-right-radius: 50rem;"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>

                <ul class="list-group gap-3" id="todo-list">
                    <?php if (!empty($todos)): ?>
                        <?php foreach ($todos as $todo): ?>
                            <?php
                                $is_finished = ($todo['is_finished'] === 't'); // PostgreSQL 't' untuk true
                                $status_class = $is_finished ? 'text-decoration-line-through text-muted' : 'text-body-emphasis';
                                $status_value = $is_finished ? '1' : '0';
                            ?>
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 rounded-3 shadow-sm border-0" data-id="<?= $todo['id'] ?>">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-grip-vertical text-muted px-2" style="cursor: grab;"></i>
                                    <span class="fs-5 <?= $status_class ?>">
                                        <?= htmlspecialchars($todo['title']) ?>
                                    </span>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-link text-info link-opacity-75-hover" onclick="showDetailModal(<?= $todo['id'] ?>)" data-bs-toggle="tooltip" data-bs-title="Detail">
                                        <i class="bi bi-eye fs-5"></i>
                                    </button>
                                    <button class="btn btn-link text-warning link-opacity-75-hover" onclick="showModalEditTodo(
                                        <?= $todo['id'] ?>, 
                                        <?= htmlspecialchars(json_encode($todo['title']), ENT_QUOTES, 'UTF-8') ?>, 
                                        <?= htmlspecialchars(json_encode($todo['description']), ENT_QUOTES, 'UTF-8') ?>, 
                                        '<?= $status_value ?>'
                                    )" data-bs-toggle="tooltip" data-bs-title="Ubah">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>
                                    <button class="btn btn-link text-danger link-opacity-75-hover" onclick="showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>')" data-bs-toggle="tooltip" data-bs-title="Hapus">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted p-5 rounded-3 shadow-sm border-0">
                            <i class="bi bi-cloud-drizzle fs-1"></i>
                            <p class="mb-0 mt-3 fs-5">Belum ada tugas tersedia!</p>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 bg-primary-subtle text-primary-emphasis">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-dotted me-2"></i>Tambah Tugas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=create" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="inputTitle" class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control rounded-pill" id="inputTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control rounded-3" id="inputDescription" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-primary-subtle">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 bg-warning-subtle text-warning-emphasis">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Ubah Data Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=update" method="POST">
                <input name="id" type="hidden" id="inputEditTodoId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="inputEditTitle" class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control rounded-pill" id="inputEditTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputEditDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control rounded-3" id="inputEditDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="selectEditStatus" class="form-label">Status</label>
                        <select class="form-select rounded-pill" name="is_finished" id="selectEditStatus">
                            <option value="0">Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-warning-subtle">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 bg-danger-subtle text-danger-emphasis">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Data Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p>Kamu akan menghapus tugas <strong class="text-danger" id="deleteTodoTitle"></strong>. Apakah kamu yakin?</p>
            </div>
            <div class="modal-footer border-0 bg-danger-subtle">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                <a id="btnDeleteTodo" class="btn btn-danger rounded-pill">Ya, Tetap Hapus</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 bg-info-subtle text-info-emphasis">
                <h5 class="modal-title fw-bold" id="detailTodoTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <h6>Deskripsi:</h6>
                <p id="detailTodoDescription" class="text-muted"></p>
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
    // Inisialisasi Tooltip Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Logika SortableJS (Drag-and-Drop)
    const todoListEl = document.getElementById('todo-list');
    if (todoListEl) {
        new Sortable(todoListEl, {
            animation: 150,
            ghostClass: 'ghost-class', // Pastikan class ini didefinisikan di file CSS eksternal Anda
            handle: '.bi-grip-vertical',
            onEnd: function (evt) {
                const todoIds = Array.from(todoListEl.children)
                                    .map(item => item.dataset.id)
                                    .filter(id => id); 
                if (todoIds.length === 0) return;
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

    // --- Logika Modal ---
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

    // --- Logika Theme Switcher (Dark Mode) ---
    (() => {
        'use strict'
        const getStoredTheme = () => localStorage.getItem('theme')
        const setStoredTheme = theme => localStorage.setItem('theme', theme)
        const getPreferredTheme = () => {
            const storedTheme = getStoredTheme()
            if (storedTheme) {
                return storedTheme
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        }
        const setTheme = theme => {
            if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-bs-theme', 'dark')
            } else {
                document.documentElement.setAttribute('data-bs-theme', theme)
            }
        }
        setTheme(getPreferredTheme())
        const showActiveTheme = (theme, focus = false) => {
            const themeSwitcher = document.querySelector('#themeSwitcher')
            if (!themeSwitcher) {
                return
            }
            const themeSwitcherIcon = themeSwitcher.querySelector('i[data-theme-icon]')
            const activeThemeIcon = document.querySelector(`[data-bs-theme-value="${theme}"] i`).className
            
            themeSwitcherIcon.className = activeThemeIcon + ' fs-5';
            
            document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
                element.classList.remove('active')
            })
            const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
            if(btnToActive) {
                btnToActive.classList.add('active')
            }
            if (focus) {
                themeSwitcher.focus()
            }
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            const storedTheme = getStoredTheme()
            if (storedTheme !== 'light' && storedTheme !== 'dark') {
                setTheme(getPreferredTheme())
            }
        })
        window.addEventListener('DOMContentLoaded', () => {
            showActiveTheme(getPreferredTheme())
            document.querySelectorAll('[data-bs-theme-value]')
                .forEach(toggle => {
                    toggle.addEventListener('click', () => {
                        const theme = toggle.getAttribute('data-bs-theme-value')
                        setStoredTheme(theme)
                        setTheme(theme)
                        showActiveTheme(theme, true)
                    })
                })
        })
    })()
</script>
</body>
</html>