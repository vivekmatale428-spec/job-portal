<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';

$status_filter = in_array($_GET['status'] ?? '', ['pending','approved','rejected','closed']) ? $_GET['status'] : '';

// Bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['selected_ids'])) {
    require_csrf_post();
    $action = $_POST['bulk_action'];
    $ids = array_map('intval', $_POST['selected_ids']);
    $ids_str = implode(',', $ids);
    
    if ($action === 'delete') {
        mysqli_query($conn, "DELETE FROM jobs WHERE id IN ($ids_str)");
        set_flash('success', count($ids) . ' job(s) deleted successfully.');
    } elseif ($action === 'approve') {
        mysqli_query($conn, "UPDATE jobs SET status = 'approved' WHERE id IN ($ids_str)");
        set_flash('success', count($ids) . ' job(s) approved successfully.');
    } elseif ($action === 'reject') {
        mysqli_query($conn, "UPDATE jobs SET status = 'rejected' WHERE id IN ($ids_str)");
        set_flash('success', count($ids) . ' job(s) rejected successfully.');
    }
    header('Location: ' . BASE_URL . 'admin/manage_jobs.php' . ($status_filter ? '?status=' . $status_filter : ''));
    exit;
}

// Delete Job
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM jobs WHERE id = $id");
    set_flash('success', 'Job deleted successfully.');
    header('Location: ' . BASE_URL . 'admin/manage_jobs.php' . ($status_filter ? '?status=' . $status_filter : ''));
    exit;
}

// Approve / Reject / Featured
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE jobs SET status = 'approved' WHERE id = $id");
    set_flash('success', 'Job approved successfully.');
    header('Location: ' . BASE_URL . 'admin/manage_jobs.php' . ($status_filter ? '?status=' . $status_filter : ''));
    exit;
}
if (isset($_GET['featured'])) {
    $id = (int)$_GET['featured'];
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT featured FROM jobs WHERE id = $id"));
    if ($row) {
        $feat = $row['featured'] ? 0 : 1;
        mysqli_query($conn, "UPDATE jobs SET featured = $feat WHERE id = $id");
        set_flash('success', 'Featured status updated.');
    }
    header('Location: ' . BASE_URL . 'admin/manage_jobs.php' . ($status_filter ? '?status=' . $status_filter : ''));
    exit;
}
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    mysqli_query($conn, "UPDATE jobs SET status = 'rejected' WHERE id = $id");
    set_flash('success', 'Job rejected.');
    header('Location: ' . BASE_URL . 'admin/manage_jobs.php' . ($status_filter ? '?status=' . $status_filter : ''));
    exit;
}

$where = $status_filter ? "WHERE j.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'" : "";
$jobs = mysqli_query($conn, "SELECT j.*, e.company_name FROM jobs j JOIN employers e ON j.employer_id = e.id $where ORDER BY j.created_at DESC");

$page_title = 'Manage Jobs';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Manage Jobs</h1>
    <p class="section-subtitle">Approve, reject, or remove job postings</p>

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
        <a href="manage_jobs.php" class="btn btn-outline-primary <?php echo !$status_filter ? 'active' : ''; ?>">All</a>
        <a href="manage_jobs.php?status=pending" class="btn btn-outline-warning <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="manage_jobs.php?status=approved" class="btn btn-outline-success <?php echo $status_filter === 'approved' ? 'active' : ''; ?>">Approved</a>
        <form method="get" class="d-flex ms-auto" style="max-width:300px;">
            <?php if ($status_filter): ?><input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>"><?php endif; ?>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search jobs..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-outline-primary btn-sm ms-1"><i class="bi bi-search"></i></button>
            <?php if ($search): ?>
                <a href="manage_jobs.php<?php echo $status_filter ? '?status=' . $status_filter : ''; ?>" class="btn btn-outline-secondary btn-sm ms-1"><i class="bi bi-x"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <form method="post" action="" id="bulkForm">
        <?php echo csrf_field(); ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <select name="bulk_action" class="form-select form-select-sm" style="width:auto;">
                        <option value="">Bulk Actions</option>
                        <option value="approve">Approve Selected</option>
                        <option value="reject">Reject Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Apply action to selected items?');">Apply</button>
                    <span class="ms-auto text-muted small" id="selectedCount">0 selected</span>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" onclick="toggleAll(this)"></th>
                            <th>Job</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($jobs)): ?>
                            <tr>
                                <td><input type="checkbox" name="selected_ids[]" value="<?php echo (int)$row['id']; ?>" class="row-checkbox" onchange="updateSelectedCount()"></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                <td><?php echo htmlspecialchars(str_replace('_', ' ', $row['job_type'])); ?></td>
                                <td><span class="badge-status badge-<?php echo $row['status']; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <a href="manage_jobs.php?approve=<?php echo (int)$row['id']; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-sm btn-success me-1">Approve</a>
                                        <a href="manage_jobs.php?reject=<?php echo (int)$row['id']; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-sm btn-danger me-1" onclick="return confirm('Reject?');">Reject</a>
                                    <?php endif; ?>
                                    <a href="manage_jobs.php?featured=<?php echo (int)$row['id']; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-sm btn-outline-warning me-1" title="Toggle Featured"><?php echo $row['featured'] ? '★' : '☆'; ?></a>
                                    <a href="edit_job.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-info me-1">Edit</a>
                                    <a href="manage_jobs.php?delete=<?php echo (int)$row['id']; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-sm btn-outline-danger me-1" onclick="return confirm('Delete this job? This will also delete all applications.');">Delete</a>
                                    <a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    function toggleAll(checkbox) {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checkbox.checked);
        updateSelectedCount();
    }
    function updateSelectedCount() {
        const count = document.querySelectorAll('.row-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count + ' selected';
    }
    document.getElementById('bulkForm').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.row-checkbox:checked').length;
        if (checked === 0) {
            e.preventDefault();
            alert('Please select at least one item.');
            return false;
        }
    });
    </script>
    
    <?php if ($pagination['total_pages'] > 1): ?>
        <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($pagination['has_prev']): ?>
                    <li class="page-item"><a class="page-link" href="?p=<?php echo $pagination['prev_page']; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Previous</a></li>
                <?php endif; ?>
                <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="?p=<?php echo $i; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($pagination['has_next']): ?>
                    <li class="page-item"><a class="page-link" href="?p=<?php echo $pagination['next_page']; ?><?php echo $status_filter ? '&status=' . $status_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <p class="text-center text-muted small">Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total); ?> of <?php echo $total; ?> jobs</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
