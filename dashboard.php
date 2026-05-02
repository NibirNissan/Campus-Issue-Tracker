<?php
session_start();
require_once 'includes/db.php';

// Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$complaints = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
        <p class="text-muted">Track and manage your campus issues.</p>
    </div>
    <div class="col-auto">
        <a href="submit_complaint.php" class="btn btn-primary">Submit New Complaint</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card p-4">
            <h5 class="mb-4">My Complaints</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Submitted On</th>
                            <th>Last Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($complaints)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No complaints submitted yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($complaints as $c): ?>
                                <tr>
                                    <td>#<?php echo $c['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($c['title']); ?></strong></td>
                                    <td><span class="badge bg-secondary"><?php echo $c['department']; ?></span></td>
                                    <td>
                                        <?php if($c['status'] == 'Pending'): ?>
                                            <span class="status-badge bg-warning text-dark">Pending</span>
                                        <?php elseif($c['status'] == 'In Progress'): ?>
                                            <span class="status-badge bg-info text-dark">In Progress</span>
                                        <?php else: ?>
                                            <span class="status-badge bg-success">Done</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?php echo date('M d, Y', strtotime($c['created_at'])); ?></td>
                                    <td class="text-muted small"><?php echo date('M d, Y', strtotime($c['updated_at'])); ?></td>
                                    <td>
                                        <a href="view_complaint.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
