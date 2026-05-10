<?php
// views/admin/manage_profile.php
// Trang admin quản lý người dùng

require_once 'config/db.php';

// Chỉ cho admin
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['is_admin']) ||
    (int)$_SESSION['is_admin'] !== 1
) {
    header('Location: index.php?page=home');
    exit();
}

$db  = new Database();
$pdo = $db->connect();

$flashMessage = '';
$flashType    = 'success';

$loggedInAdminId = (int)$_SESSION['user_id'];

/* ===== Helper: xác định user bị ban qua full_name prefix ===== */
function isBannedUserRow(array $row): bool {
    if (!isset($row['full_name']) || $row['full_name'] === null) return false;
    return str_starts_with($row['full_name'], '[BANNED]');
}

function stripBannedPrefix(?string $name): ?string {
    if ($name === null) return null;
    $prefix = '[BANNED] ';
    if (str_starts_with($name, $prefix)) {
        return substr($name, strlen($prefix));
    }
    return $name;
}

/* ===== Xử lý GET action: delete / ban / unban ===== */
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $userId = (int)$_GET['id'];

    if ($userId === $loggedInAdminId && in_array($action, ['delete', 'ban'], true)) {
        $flashMessage = 'Bạn không thể tự xoá hoặc khoá tài khoản của chính mình.';
        $flashType    = 'danger';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            $flashMessage = 'Không tìm thấy người dùng.';
            $flashType    = 'danger';
        } else {
            if ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $flashMessage = 'Đã xoá người dùng thành công.';
                $flashType    = 'success';

            } elseif ($action === 'ban') {
                if (isBannedUserRow($userRow)) {
                    $flashMessage = 'Người dùng này đã bị khoá từ trước.';
                    $flashType    = 'info';
                } else {
                    // Ban: đổi password random + thêm prefix [BANNED] vào full_name
                    $randomPassword = bin2hex(random_bytes(16));
                    $hash           = password_hash($randomPassword, PASSWORD_BCRYPT);
                    $newName        = '[BANNED] ' . ($userRow['full_name'] ?? $userRow['username']);

                    $stmt = $pdo->prepare("UPDATE users SET password = ?, full_name = ? WHERE id = ?");
                    $stmt->execute([$hash, $newName, $userId]);

                    $flashMessage = 'Đã khoá tài khoản người dùng.';
                    $flashType    = 'warning';
                }

            } elseif ($action === 'unban') {
                if (!isBannedUserRow($userRow)) {
                    $flashMessage = 'Người dùng này hiện không bị khoá.';
                    $flashType    = 'info';
                } else {
                    $cleanName = stripBannedPrefix($userRow['full_name']);
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
                    $stmt->execute([$cleanName, $userId]);

                    $flashMessage = 'Đã mở khoá tài khoản. Hãy đặt lại mật khẩu nếu cần.';
                    $flashType    = 'success';
                }
            }
        }
    }
}

/* ===== Xử lý POST: edit user ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $editId    = (int)($_POST['user_id'] ?? 0);
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $is_admin  = isset($_POST['is_admin']) ? 1 : 0;
    $new_pass  = $_POST['new_password'] ?? '';

    if ($editId <= 0 || $username === '') {
        $flashMessage = 'Dữ liệu người dùng không hợp lệ.';
        $flashType    = 'danger';
    } else {
        // Check trùng username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
        $stmt->execute([$username, $editId]);
        if ($stmt->fetch()) {
            $flashMessage = 'Tên đăng nhập đã tồn tại trong hệ thống.';
            $flashType    = 'danger';
        } else {
            if ($new_pass !== '') {
                $hash   = password_hash($new_pass, PASSWORD_BCRYPT);
                $stmt   = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, is_admin = ?, password = ? WHERE id = ?");
                $params = [$username, $full_name === '' ? null : $full_name, $is_admin, $hash, $editId];
            } else {
                $stmt   = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, is_admin = ? WHERE id = ?");
                $params = [$username, $full_name === '' ? null : $full_name, $is_admin, $editId];
            }

            $stmt->execute($params);

            // Nếu là chính admin đang đăng nhập
            if ($editId === $loggedInAdminId) {
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = $is_admin;
            }

            $flashMessage = 'Cập nhật thông tin người dùng thành công.';
            $flashType    = 'success';
        }
    }
}

/* ===== Lấy danh sách users (Pagination) ===== */
require_once 'models/UserModel.php';
$userModel = new UserModel();

$page = isset($_GET['user_page']) ? (int)$_GET['user_page'] : 1;
$limit = 10;
$filters = [
    'search' => $_GET['search'] ?? '',
    'role' => $_GET['role'] ?? ''
];

$userData = $userModel->getPaginated($page, $limit, $filters);
$users = $userData['items'];
$totalPages = $userData['total_pages'];
$totalUsers = $userData['total'];
?>
<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4">
                <h2><i class="bi bi-people"></i> Quản lý người dùng</h2>
                <p class="text-muted">Xem, cập nhật thông tin, khoá hoặc xoá tài khoản thành viên trong hệ thống.</p>
            </div>

            <?php if ($flashMessage): ?>
                <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show">
                    <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($flashMessage) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:5%;">ID</th>
                                    <th style="width:20%;">Tên đăng nhập</th>
                                    <th style="width:25%;">Họ và tên</th>
                                    <th style="width:10%;">Vai trò</th>
                                    <th style="width:10%;">Trạng thái</th>
                                    <th class="pe-4 text-end" style="width:30%;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $u):
                                $isAdmin  = (int)$u['is_admin'] === 1;
                                $isBanned = isBannedUserRow($u);
                            ?>
                                <tr>
                                    <td class="ps-4"><?= (int)$u['id'] ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= htmlspecialchars($u['full_name'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($isAdmin): ?>
                                            <span class="badge bg-primary">Quản trị viên</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Khách hàng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isBanned): ?>
                                            <span class="badge bg-danger">Đã khoá</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group">
                                            <button
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUserModal<?= (int)$u['id'] ?>"
                                                title="Sửa">
                                                <i class="bi bi-pencil"></i> Sửa
                                            </button>

                                            <?php if ($isBanned): ?>
                                                <a href="index.php?page=manage_profile&action=unban&id=<?= (int)$u['id'] ?>"
                                                    class="btn btn-sm btn-warning"
                                                    onclick="return confirm('Mở khoá tài khoản này?');"
                                                    title="Mở khoá">
                                                    <i class="bi bi-unlock"></i> Mở khoá
                                                </a>
                                            <?php else: ?>
                                                <?php if ((int)$u['id'] !== $loggedInAdminId): ?>
                                                    <a href="index.php?page=manage_profile&action=ban&id=<?= (int)$u['id'] ?>"
                                                        class="btn btn-sm btn-outline-warning"
                                                        onclick="return confirm('Khoá tài khoản này? Người dùng sẽ không thể đăng nhập.');"
                                                        title="Khoá">
                                                        <i class="bi bi-lock"></i> Khoá
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if ((int)$u['id'] !== $loggedInAdminId): ?>
                                                <a href="index.php?page=manage_profile&action=delete&id=<?= (int)$u['id'] ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Xoá vĩnh viễn người dùng này?');"
                                                    title="Xoá">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Edit User -->
                                <div class="modal fade" id="editUserModal<?= (int)$u['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="post" action="index.php?page=manage_profile">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Chỉnh sửa người dùng #<?= (int)$u['id'] ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="update_user" value="1" />
                                                    <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>" />

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Tên đăng nhập</label>
                                                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($u['username']) ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Họ và tên</label>
                                                        <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars(stripBannedPrefix($u['full_name'] ?? '')) ?>">
                                                    </div>

                                                    <div class="mb-3 form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="is_admin_<?= (int)$u['id'] ?>" name="is_admin" <?= $isAdmin ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="is_admin_<?= (int)$u['id'] ?>">Quyền Quản trị viên</label>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Mật khẩu mới (tuỳ chọn)</label>
                                                        <input type="password" class="form-control" name="new_password" placeholder="Để trống nếu không muốn đổi">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="User pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=manage_profile&user_page=1">&laquo;&laquo;</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=manage_profile&user_page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=manage_profile&user_page=<?= $totalPages ?>">&raquo;&raquo;</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
</div>

<?php include 'views/layouts/admin_footer.php'; ?>
