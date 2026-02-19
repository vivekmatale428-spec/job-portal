<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';

// Delete Employer
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM employers WHERE id = $id");
    set_flash('success', 'Employer deleted successfully.');
    header('Location: ' . BASE_URL . 'admin/manage_employers.php');
    exit;
}

// Search
$search = trim($_GET['search'] ?? '');
$where = '';
if ($search) {
    $where = "WHERE company_name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' OR email LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}

// Pagination
$page = max(1, (int)($_GET['p'] ?? 1));
$per_page = ITEMS_PER_PAGE;
$total = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM employers $where"));
$pagination = paginate($total, $per_page, $page);
$offset = $pagination['offset'];

$employers = mysqli_query($conn, "SELECT id, company_name, email, phone, location, industry, created_at FROM employers $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");

$page_title = 'Manage Employers';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Manage Employers</h1>
    <p class="section-subtitle">CRUD operations for employers</p>

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
        <a href="add_employer.php" class="btn btn-primary">Add New Employer</a>
        <form method="get" class="d-flex ms-auto" style="max-width:300px;">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search employers..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-outline-primary btn-sm ms-1"><i class="bi bi-search"></i></button>
            <?php if ($search): ?>
                <a href="manage_employers.php" class="btn btn-outline-secondary btn-sm ms-1"><i class="bi bi-x"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Industry</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($employers)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($row['location'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($row['industry'] ?? '—'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="edit_employer.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-info me-1">Edit</a>
                                    <a href="manage_employers.php?delete=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this employer? This will also delete all their jobs and applications.');">Delete</a>
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
        <p class="text-center text-muted small">Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total); ?> of <?php echo $total; ?> employers</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
