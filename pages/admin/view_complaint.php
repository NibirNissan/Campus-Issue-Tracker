<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireAdmin();

$complaint_id = intval($_GET['id'] ?? 0);

if ($complaint_id <= 0) {
    setFlash('danger', 'Invalid complaint ID.');
    header("Location: " . base_url("/pages/admin/complaints.php"));
    exit();
}

// Fetch complaint with user info
$stmt = $conn->prepare("SELECT c.*, u.name as user_name, u.email as user_email FROM complaints c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlash('danger', 'Complaint not found.');
    header("Location: " . base_url("/pages/admin/complaints.php"));
    exit();
}

$complaint = $result->fetch_assoc();
$stmt->close();

// Fetch timeline
$timeline_stmt = $conn->prepare("SELECT ct.*, u.name as changed_by_name FROM complaint_timeline ct JOIN users u ON ct.changed_by = u.id WHERE ct.complaint_id = ? ORDER BY ct.created_at ASC");
$timeline_stmt->bind_param("i", $complaint_id);
$timeline_stmt->execute();
$timeline = $timeline_stmt->get_result();

$pageTitle = 'View Complaint';
include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-eye me-2"></i>Complaint #<?php echo $complaint['id']; ?></h2>
    <div>
        <a href="<?php echo base_url("/pages/admin/update_status.php?id=" . $complaint['id']); ?>" class="btn btn-success me-2">
            <i class="bi bi-pencil-square me-1"></i>Update Status
        </a>
        <a href="<?php echo base_url("/pages/admin/complaints.php"); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Complaint Details -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><?php echo htmlspecialchars($complaint['title']); ?></h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong><i class="bi bi-person me-1"></i>Submitted By:</strong>
                        <?php echo htmlspecialchars($complaint['user_name']); ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars($complaint['user_email']); ?></small>
                    </div>
                    <div class="col-md-3">
                        <strong>Department:</strong>
                        <span class="badge dept-<?php echo strtolower($complaint['department']); ?>">
                            <?php echo $complaint['department']; ?>
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong>
                        <?php
                        $statusClass = match($complaint['status']) {
                            'Pending' => 'badge-pending',
                            'In Progress' => 'badge-in-progress',
                            'Done' => 'badge-done',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?php echo $statusClass; ?>">
                            <?php echo $complaint['status']; ?>
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Submitted:</strong>
                        <?php echo date('M d, Y h:i A', strtotime($complaint['created_at'])); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Last Updated:</strong>
                        <?php echo date('M d, Y h:i A', strtotime($complaint['updated_at'])); ?>
                    </div>
                </div>

                <hr>

                <h6>Description:</h6>
                <p><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>

                <?php if ($complaint['image']): ?>
                    <hr>
                    <h6>Attached Image:</h6>
                    <img src="<?php echo base_url("/uploads/" . htmlspecialchars($complaint['image'])); ?>"
                         alt="Complaint Image" class="complaint-image img-fluid"
                         data-bs-toggle="modal" data-bs-target="#imageModal">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Timeline</h5>
            </div>
            <div class="card-body">
                <?php if ($timeline->num_rows > 0): ?>
                    <div class="timeline">
                        <?php while ($entry = $timeline->fetch_assoc()):
                            $statusSlug = strtolower(str_replace(' ', '-', $entry['new_status']));
                        ?>
                            <div class="timeline-item status-<?php echo $statusSlug; ?>">
                                <div class="fw-bold"><?php echo $entry['new_status']; ?></div>
                                <?php if ($entry['old_status']): ?>
                                    <small class="text-muted">
                                        Changed from: <?php echo $entry['old_status']; ?>
                                    </small><br>
                                <?php endif; ?>
                                <?php if ($entry['remarks']): ?>
                                    <p class="text-muted mb-1 small"><?php echo htmlspecialchars($entry['remarks']); ?></p>
                                <?php endif; ?>
                                <small class="text-muted">
                                    By <?php echo htmlspecialchars($entry['changed_by_name']); ?><br>
                                    <?php echo date('M d, Y h:i A', strtotime($entry['created_at'])); ?>
                                </small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No timeline entries yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<?php if ($complaint['image']): ?>
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attached Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?php echo base_url("/uploads/" . htmlspecialchars($complaint['image'])); ?>"
                     alt="Complaint Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
