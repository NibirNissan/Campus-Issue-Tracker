<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireStudent();

$user_id = $_SESSION['user_id'];

// Get complaint counts
$total = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE user_id = $user_id")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE user_id = $user_id AND status = 'Pending'")->fetch_assoc()['c'];
$in_progress = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE user_id = $user_id AND status = 'In Progress'")->fetch_assoc()['c'];
$done = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE user_id = $user_id AND status = 'Done'")->fetch_assoc()['c'];

// Get recent complaints
$stmt = $conn->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_complaints = $stmt->get_result();

$pageTitle = 'Student Dashboard';
include __DIR__ . '/../../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Student Dashboard</h2>
<p class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</p>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-warning text-dark">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $pending; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-info text-dark">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $in_progress; ?></div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-success text-white">
            <div class="card-body text-center">
                <div class="stat-number"><?php echo $done; ?></div>
                <div class="stat-label">Done</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-6">
        <a href="<?php echo base_url("/pages/student/submit_complaint.php"); ?>" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-plus-circle me-2"></i>Submit New Complaint
        </a>
    </div>
    <div class="col-md-6">
        <a href="<?php echo base_url("/pages/student/my_complaints.php"); ?>" class="btn btn-outline-primary btn-lg w-100">
            <i class="bi bi-journal-text me-2"></i>View All My Complaints
        </a>
    </div>
</div>

<!-- Recent Complaints -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Complaints</h5>
    </div>
    <div class="card-body">
        <?php if ($recent_complaints->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-complaints">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recent_complaints->fetch_assoc()): ?>
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
                                <td>
                                    <a href="<?php echo base_url("/pages/student/view_complaint.php?id=" . $row['id']); ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
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
                <a href="<?php echo base_url("/pages/student/submit_complaint.php"); ?>" class="btn btn-primary">Submit Your First Complaint</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
