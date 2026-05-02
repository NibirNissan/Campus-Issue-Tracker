<?php
session_start();
require_once 'includes/db.php';

// Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['complaint_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

// Search and Filter logic
$where_clauses = [];
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where_clauses[] = "(c.title LIKE ? OR c.description LIKE ? OR u.name LIKE ?)";
    $search_term = "%" . $_GET['search'] . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (isset($_GET['dept']) && !empty($_GET['dept'])) {
    $where_clauses[] = "c.department = ?";
    $params[] = $_GET['dept'];
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Fetch complaints
$query = "SELECT c.*, u.name as user_name FROM complaints c JOIN users u ON c.user_id = u.id $where_sql ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$complaints = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h2>Admin Dashboard</h2>
        <p class="text-muted">Manage all campus complaints.</p>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card p-3 mb-4 bg-light">
    <form method="GET" action="" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Title, student name..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Department</label>
            <select name="dept" class="form-select form-select-sm">
                <option value="">All Departments</option>
                <option value="Lab" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'Lab') ? 'selected' : ''; ?>>Lab</option>
                <option value="Class" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'Class') ? 'selected' : ''; ?>>Class</option>
                <option value="Hostel" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'Hostel') ? 'selected' : ''; ?>>Hostel</option>
                <option value="Library" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'Library') ? 'selected' : ''; ?>>Library</option>
                <option value="Other" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary px-4">Apply Filters</button>
            <a href="admin_dashboard.php" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card p-4">
            <h5 class="mb-4">All Submitted Issues</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Title</th>
                            <th>Dept</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($complaints as $c): ?>
                            <tr>
                                <td>#<?php echo $c['id']; ?></td>
                                <td>
                                    <div class="small fw-bold"><?php echo htmlspecialchars($c['user_name']); ?></div>
                                    <div class="small text-muted">ID: <?php echo $c['user_id']; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($c['title']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $c['department']; ?></span></td>
                                <td>
                                    <form method="POST" action="" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="complaint_id" value="<?php echo $c['id']; ?>">
                                        <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                            <option value="Pending" <?php echo $c['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="In Progress" <?php echo $c['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="Done" <?php echo $c['status'] == 'Done' ? 'selected' : ''; ?>>Done</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td class="text-muted small"><?php echo date('M d, Y', strtotime($c['created_at'])); ?></td>
                                <td>
                                    <a href="view_complaint.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
