<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';

// Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    set_flash('success', 'User deleted successfully.');
    header('Location: ' . BASE_URL . 'admin/manage_users.php');
    exit;
}

// Search
$search = trim($_GET['search'] ?? '');
$where = '';
if ($search) {
    $where = "WHERE full_name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' OR email LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}

// Pagination
$page = max(1, (int)($_GET['p'] ?? 1));
$per_page = ITEMS_PER_PAGE;
$total = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM users $where"));
$pagination = paginate($total, $per_page, $page);
$offset = $pagination['offset'];

$users = mysqli_query($conn, "SELECT id, full_name, email, phone, location, qualification, experience, created_at FROM users $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");

$page_title = 'Manage Users';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Manage Job Seekers</h1>
    <p class="section-subtitle">CRUD operations for job seekers</p>

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
        <a href="add_user.php" class="btn btn-primary">Add New User</a>
        <form method="get" class="d-flex ms-auto" style="max-width:300px;">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-outline-primary btn-sm ms-1"><i class="bi bi-search"></i></button>
            <?php if ($search): ?>
                <a href="manage_users.php" class="btn btn-outline-secondary btn-sm ms-1"><i class="bi bi-x"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Qualification</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($row['location'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($row['qualification'] ?? '—'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-info me-1">Edit</a>
                                    <a href="manage_users.php?delete=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user? This will also delete all their applications.');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php if ($pagination['total_pages'] > 1): ?>
        <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($pagination['has_prev']): ?>
                    <li class="page-item"><a class="page-link" href="?p=<?php echo $pagination['prev_page']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Previous</a></li>
                <?php endif; ?>
                <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="?p=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($pagination['has_next']): ?>
                    <li class="page-item"><a class="page-link" href="?p=<?php echo $pagination['next_page']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <p class="text-center text-muted small">Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total); ?> of <?php echo $total; ?> users</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
