<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireAdmin();

// Filter and search parameters
$status_filter = $_GET['status'] ?? '';
$dept_filter = $_GET['department'] ?? '';
$search = trim($_GET['search'] ?? '');

// Build query
$where = "WHERE 1=1";
$params = [];
$types = "";

if (!empty($status_filter)) {
    $where .= " AND c.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if (!empty($dept_filter)) {
    $where .= " AND c.department = ?";
    $params[] = $dept_filter;
    $types .= "s";
}
if (!empty($search)) {
    $where .= " AND (c.title LIKE ? OR c.description LIKE ? OR u.name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$sql = "SELECT c.*, u.name as user_name, u.email as user_email FROM complaints c JOIN users u ON c.user_id = u.id $where ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$complaints = $stmt->get_result();

$pageTitle = 'All Complaints';
include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-list-task me-2"></i>All Complaints</h2>
    <span class="badge bg-primary fs-6"><?php echo $complaints->num_rows; ?> results</span>
</div>

<!-- Search & Filter Bar -->
<div class="filter-bar">
    <form method="GET" action="" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="search" class="form-label">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="search" name="search"
                       value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Title, description, or user...">
            </div>
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">All Statuses</option>
                <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?php echo $status_filter === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="Done" <?php echo $status_filter === 'Done' ? 'selected' : ''; ?>>Done</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="department" class="form-label">Department</label>
            <select class="form-select" id="department" name="department">
                <option value="">All Departments</option>
                <?php foreach (['Lab', 'Class', 'Hostel', 'Library', 'Other'] as $dept): ?>
                    <option value="<?php echo $dept; ?>" <?php echo $dept_filter === $dept ? 'selected' : ''; ?>>
                        <?php echo $dept; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary me-2">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="<?php echo base_url("/pages/admin/complaints.php"); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Complaints Table -->
<?php if ($complaints->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover table-complaints">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Submitted By</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $complaints->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($row['user_name']); ?>
                            <br><small class="text-muted"><?php echo htmlspecialchars($row['user_email']); ?></small>
                        </td>
                        <td>
                            <span class="badge dept-<?php echo strtolower($row['department']); ?>">
                                <?php echo $row['department']; ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusClass = match($row['status']) {
                                'Pending' => 'badge-pending',
                                'In Progress' => 'badge-in-progress',
                                'Done' => 'badge-done',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($row['updated_at'])); ?></td>
                        <td>
                            <a href="<?php echo base_url("/pages/admin/view_complaint.php?id=" . $row['id']); ?>"
                               class="btn btn-sm btn-outline-primary me-1" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?php echo base_url("/pages/admin/update_status.php?id=" . $row['id']); ?>"
                               class="btn btn-sm btn-outline-success" title="Update Status">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox display-4 text-muted"></i>
        <p class="text-muted mt-2">No complaints found matching your criteria.</p>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
