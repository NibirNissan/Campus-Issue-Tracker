<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireAdmin();

// Get overall stats
$total = $conn->query("SELECT COUNT(*) as c FROM complaints")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE status = 'Pending'")->fetch_assoc()['c'];
$in_progress = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE status = 'In Progress'")->fetch_assoc()['c'];
$done = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE status = 'Done'")->fetch_assoc()['c'];
$total_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE role = 'student'")->fetch_assoc()['c'];

// Get department-wise counts
$dept_stats = $conn->query("SELECT department, COUNT(*) as count FROM complaints GROUP BY department ORDER BY count DESC");

// Get recent complaints
$recent = $conn->query("SELECT c.*, u.name as user_name FROM complaints c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC LIMIT 10");

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h2>
<p class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>! Here's an overview of all complaints.</p>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-md col-6">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Complaints</div>
            </div>
        </div>
    </div>
    <div class="col-md col-6">
        <div class="card stat-card bg-warning text-dark">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $pending; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md col-6">
        <div class="card stat-card bg-info text-dark">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $in_progress; ?></div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
    </div>
    <div class="col-md col-6">
        <div class="card stat-card bg-success text-white">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $done; ?></div>
                <div class="stat-label">Resolved</div>
            </div>
        </div>
    </div>
    <div class="col-md col-6">
        <div class="card stat-card bg-dark text-white">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Students</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Department Stats -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>By Department</h5>
            </div>
            <div class="card-body">
                <?php if ($dept_stats->num_rows > 0): ?>
                    <?php while ($dept = $dept_stats->fetch_assoc()): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge dept-<?php echo strtolower($dept['department']); ?>">
                                <?php echo $dept['department']; ?>
                            </span>
                            <span class="fw-bold"><?php echo $dept['count']; ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">No complaints yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Complaints -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Complaints</h5>
                <a href="<?php echo base_url("/pages/admin/complaints.php"); ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if ($recent->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-complaints mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>By</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $recent->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
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
                                        <td>
                                            <a href="<?php echo base_url("/pages/admin/view_complaint.php?id=" . $row['id']); ?>"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="text-muted mt-2">No complaints submitted yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
