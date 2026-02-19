<?php
require_once __DIR__ . '/config/db.php';

$q = trim($_GET['q'] ?? '');
$location = trim($_GET['location'] ?? '');
$category_slug = trim($_GET['category'] ?? '');
$job_type = trim($_GET['job_type'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

$where = ["j.status = 'approved'"];
$params = [];
$types = '';

if ($q !== '') {
    $where[] = "(j.title LIKE ? OR j.description LIKE ? OR j.requirements LIKE ?)";
    $term = "%$q%";
    $params = array_merge($params, [$term, $term, $term]);
    $types .= 'sss';
}
if ($location !== '') {
    $where[] = "j.location LIKE ?";
    $params[] = "%$location%";
    $types .= 's';
}
if ($category_slug !== '') {
    $where[] = "c.slug = ?";
    $params[] = $category_slug;
    $types .= 's';
}
if ($job_type !== '') {
    $where[] = "j.job_type = ?";
    $params[] = $job_type;
    $types .= 's';
}

$sql_where = implode(' AND ', $where);
$count_sql = "SELECT COUNT(*) FROM jobs j LEFT JOIN categories c ON j.category_id = c.id WHERE $sql_where";
if ($params) {
    $stmt = mysqli_prepare($conn, $count_sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $total = (int) mysqli_fetch_column(mysqli_stmt_get_result($stmt), 0);
} else {
    $total = (int) mysqli_fetch_column(mysqli_query($conn, $count_sql), 0);
}

$total_pages = $total ? (int) ceil($total / $per_page) : 1;
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;

$list_sql = "SELECT j.*, e.company_name, c.name AS category_name FROM jobs j 
    LEFT JOIN employers e ON j.employer_id = e.id 
    LEFT JOIN categories c ON j.category_id = c.id 
    WHERE $sql_where ORDER BY j.featured DESC, j.created_at DESC LIMIT $per_page OFFSET $offset";

if ($params) {
    $stmt = mysqli_prepare($conn, $list_sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $jobs = mysqli_stmt_get_result($stmt);
} else {
    $jobs = mysqli_query($conn, $list_sql);
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

$page_title = 'Browse Jobs';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Browse Jobs</h1>
    <p class="section-subtitle"><?php echo $total; ?> job<?php echo $total !== 1 ? 's' : ''; ?> found</p>

    <form method="get" action="" class="card p-3 mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="q" class="form-control" placeholder="Keyword" value="<?php echo htmlspecialchars($q); ?>">
            </div>
            <div class="col-md-2">
                <input type="text" name="location" class="form-control" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo htmlspecialchars($c['slug']); ?>" <?php echo $category_slug === $c['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="job_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="full_time" <?php echo $job_type === 'full_time' ? 'selected' : ''; ?>>Full Time</option>
                    <option value="part_time" <?php echo $job_type === 'part_time' ? 'selected' : ''; ?>>Part Time</option>
                    <option value="contract" <?php echo $job_type === 'contract' ? 'selected' : ''; ?>>Contract</option>
                    <option value="internship" <?php echo $job_type === 'internship' ? 'selected' : ''; ?>>Internship</option>
                    <option value="remote" <?php echo $job_type === 'remote' ? 'selected' : ''; ?>>Remote</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
            </div>
        </div>
    </form>

    <div class="row g-4">
        <?php 
        if ($jobs && mysqli_num_rows($jobs) > 0):
            while ($job = mysqli_fetch_assoc($jobs)): 
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-job h-100 <?php echo !empty($job['featured']) ? 'card-featured' : ''; ?>">
                    <div class="card-body">
                        <span class="job-type"><?php echo htmlspecialchars(str_replace('_', ' ', $job['job_type'])); ?></span>
                        <h5 class="card-title mt-2 mb-1"><a href="job_detail.php?id=<?php echo (int)$job['id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($job['title']); ?></a></h5>
                        <p class="company-name mb-2"><?php echo htmlspecialchars($job['company_name']); ?></p>
                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($job['location'] ?? '—'); ?> <?php if (!empty($job['category_name'])) echo ' • ' . htmlspecialchars($job['category_name']); ?></p>
                        <?php if ($job['salary_show'] && ($job['salary_min'] || $job['salary_max'])): ?>
                            <p class="salary small mb-0">₹<?php echo number_format($job['salary_min'] ?? 0); ?> - ₹<?php echo number_format($job['salary_max'] ?? 0); ?> / mo</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php 
            endwhile;
        else:
        ?>
            <div class="col-12">
                <div class="card p-5 text-center">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="mb-0 text-muted">No jobs match your criteria. Try adjusting filters.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $page - 1])); ?>">Previous</a></li>
                <?php endif; ?>
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>"><a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $i])); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $page + 1])); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
