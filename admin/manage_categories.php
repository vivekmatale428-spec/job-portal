<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['delete'];
        mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
        set_flash('success', 'Category deleted successfully.');
    } elseif (isset($_POST['add'])) {
        $name = trim($_POST['name'] ?? '');
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        if ($name) {
            mysqli_query($conn, "INSERT INTO categories (name, slug) VALUES ('" . mysqli_real_escape_string($conn, $name) . "', '" . mysqli_real_escape_string($conn, $slug) . "')");
            set_flash('success', 'Category added successfully.');
        }
    } elseif (isset($_POST['edit'])) {
        $id = (int)$_POST['edit'];
        $name = trim($_POST['name'] ?? '');
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        if ($name) {
            mysqli_query($conn, "UPDATE categories SET name='" . mysqli_real_escape_string($conn, $name) . "', slug='" . mysqli_real_escape_string($conn, $slug) . "' WHERE id = $id");
            set_flash('success', 'Category updated successfully.');
        }
    }
    header('Location: ' . BASE_URL . 'admin/manage_categories.php');
    exit;
}

$categories = mysqli_query($conn, "SELECT c.*, (SELECT COUNT(*) FROM jobs WHERE category_id = c.id) AS job_count FROM categories c ORDER BY c.name");

$page_title = 'Manage Categories';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Manage Categories</h1>
    <p class="section-subtitle">Add, edit or remove job categories</p>

    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 id="formTitle">Add Category</h5>
                    <form method="post" action="" id="categoryForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="edit" id="editId" value="">
                        <div class="mb-3">
                            <input type="text" name="name" id="categoryName" class="form-control" placeholder="Category name" required>
                        </div>
                        <button type="submit" name="add" value="1" id="submitBtn" class="btn btn-primary">Add</button>
                        <button type="button" id="cancelBtn" class="btn btn-outline-secondary d-none" onclick="resetForm()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Jobs</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($c['name']); ?></td>
                                        <td><code><?php echo htmlspecialchars($c['slug']); ?></code></td>
                                        <td><?php echo (int)$c['job_count']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info me-1" onclick="editCategory(<?php echo (int)$c['id']; ?>, '<?php echo htmlspecialchars(addslashes($c['name'])); ?>')">Edit</button>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                                <input type="hidden" name="delete" value="<?php echo (int)$c['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function editCategory(id, name) {
    document.getElementById('formTitle').textContent = 'Edit Category';
    document.getElementById('categoryName').value = name;
    document.getElementById('editId').value = id;
    document.getElementById('submitBtn').name = 'edit';
    document.getElementById('submitBtn').value = id;
    document.getElementById('submitBtn').textContent = 'Update';
    document.getElementById('cancelBtn').classList.remove('d-none');
    document.getElementById('categoryName').focus();
}
function resetForm() {
    document.getElementById('formTitle').textContent = 'Add Category';
    document.getElementById('categoryName').value = '';
    document.getElementById('editId').value = '';
    document.getElementById('submitBtn').name = 'add';
    document.getElementById('submitBtn').value = '1';
    document.getElementById('submitBtn').textContent = 'Add';
    document.getElementById('cancelBtn').classList.add('d-none');
}
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
