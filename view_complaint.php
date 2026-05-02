<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch complaint with user name
$stmt = $pdo->prepare("SELECT c.*, u.name as student_name FROM complaints c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) {
    die("Complaint not found.");
}

// Access control: only owner or admin can view
if ($_SESSION['user_role'] != 'admin' && $c['user_id'] != $_SESSION['user_id']) {
    die("Unauthorized access.");
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <a href="<?php echo $_SESSION['user_role'] == 'admin' ? 'admin_dashboard.php' : 'dashboard.php'; ?>" class="btn btn-link text-decoration-none p-0 text-muted">&larr; Back to Dashboard</a>
        <h2 class="mt-2">Complaint Details #<?php echo $c['id']; ?></h2>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <span class="badge bg-primary mb-2"><?php echo $c['department']; ?> Department</span>
                    <h3 class="mb-0"><?php echo htmlspecialchars($c['title']); ?></h3>
                </div>
                <div>
                    <?php if($c['status'] == 'Pending'): ?>
                        <span class="status-badge bg-warning text-dark fs-6">Pending Review</span>
                    <?php elseif($c['status'] == 'In Progress'): ?>
                        <span class="status-badge bg-info text-dark fs-6">Work in Progress</span>
                    <?php else: ?>
                        <span class="status-badge bg-success fs-6">Resolved</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="p-3 bg-light rounded mb-4">
                <p class="mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars($c['description']); ?></p>
            </div>

            <?php if($c['image']): ?>
                <div class="mb-4">
                    <h6>Attached Proof:</h6>
                    <img src="<?php echo $c['image']; ?>" class="img-fluid rounded border" style="max-height: 400px;" alt="Complaint proof">
                </div>
            <?php endif; ?>

            <hr>

            <div class="row text-muted small">
                <div class="col-sm-4">
                    <strong>Submitted By:</strong><br><?php echo htmlspecialchars($c['student_name']); ?>
                </div>
                <div class="col-sm-4">
                    <strong>Date Submitted:</strong><br><?php echo date('F d, Y h:i A', strtotime($c['created_at'])); ?>
                </div>
                <div class="col-sm-4">
                    <strong>Last Updated:</strong><br><?php echo date('F d, Y h:i A', strtotime($c['updated_at'])); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card p-4">
            <h5>Status Timeline</h5>
            <div class="position-relative mt-4">
                <!-- Simple Timeline -->
                <div class="border-start border-2 ps-4 pb-4 position-relative">
                    <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -7px; top: 5px;"></div>
                    <div class="small fw-bold">Submitted</div>
                    <div class="small text-muted"><?php echo date('M d, Y', strtotime($c['created_at'])); ?></div>
                    <div class="small mt-1">Complaint registered in the system.</div>
                </div>

                <?php if($c['status'] != 'Pending'): ?>
                <div class="border-start border-2 ps-4 pb-4 position-relative">
                    <div class="position-absolute bg-info rounded-circle" style="width: 12px; height: 12px; left: -7px; top: 5px;"></div>
                    <div class="small fw-bold">Processing</div>
                    <div class="small text-muted"><?php echo date('M d, Y', strtotime($c['updated_at'])); ?></div>
                    <div class="small mt-1">Assignee has acknowledged the issue.</div>
                </div>
                <?php endif; ?>

                <?php if($c['status'] == 'Done'): ?>
                <div class="border-start border-2 ps-4 position-relative">
                    <div class="position-absolute bg-success rounded-circle" style="width: 12px; height: 12px; left: -7px; top: 5px;"></div>
                    <div class="small fw-bold">Resolved</div>
                    <div class="small text-muted"><?php echo date('M d, Y', strtotime($c['updated_at'])); ?></div>
                    <div class="small mt-1">The issue has been fixed and closed.</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
