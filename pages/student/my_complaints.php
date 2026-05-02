<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireStudent();

$user_id = $_SESSION['user_id'];

// Filter parameters
$status_filter = $_GET['status'] ?? '';
$dept_filter = $_GET['department'] ?? '';

// Build query
$where = "WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if (!empty($status_filter)) {
    $where .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if (!empty($dept_filter)) {
    $where .= " AND department = ?";
    $params[] = $dept_filter;
    $types .= "s";
}

$stmt = $conn->prepare("SELECT * FROM complaints $where ORDER BY created_at DESC");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$complaints = $stmt->get_result();

$pageTitle = 'My Complaints';
include __DIR__ . '/../../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-journal-text me-2"></i>My Complaints</h2>

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="GET" action="" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="status" class="form-label">Filter by Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">All Statuses</option>
                <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?php echo $status_filter === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="Done" <?php echo $status_filter === 'Done' ? 'selected' : ''; ?>>Done</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="department" class="form-label">Filter by Department</label>
            <select class="form-select" id="department" name="department">
                <option value="">All Departments</option>
                <?php foreach (['Lab', 'Class', 'Hostel', 'Library', 'Other'] as $dept): ?>
                    <option value="<?php echo $dept; ?>" <?php echo $dept_filter === $dept ? 'selected' : ''; ?>>
                        <?php echo $dept; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary me-2">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="<?php echo base_url("/pages/student/my_complaints.php"); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Complaints List -->
<?php if ($complaints->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover table-complaints">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $complaints->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
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
                            <a href="<?php echo base_url("/pages/student/view_complaint.php?id=" . $row['id']); ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View
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
        <p class="text-muted mt-2">No complaints found.</p>
        <a href="<?php echo base_url("/pages/student/submit_complaint.php"); ?>" class="btn btn-primary">Submit a Complaint</a>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
