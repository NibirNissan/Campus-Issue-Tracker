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

// Fetch complaint
$stmt = $conn->prepare("SELECT c.*, u.name as user_name FROM complaints c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
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

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');
    $admin_id = $_SESSION['user_id'];

    // Validation
    $valid_statuses = ['Pending', 'In Progress', 'Done'];
    if (!in_array($new_status, $valid_statuses)) {
        $errors[] = 'Invalid status selected.';
    }
    if ($new_status === $complaint['status']) {
        $errors[] = 'Please select a different status.';
    }

    if (empty($errors)) {
        $old_status = $complaint['status'];

        // Update complaint status
        $update_stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_status, $complaint_id);

        if ($update_stmt->execute()) {
            // Add timeline entry
            $timeline_stmt = $conn->prepare("INSERT INTO complaint_timeline (complaint_id, changed_by, old_status, new_status, remarks) VALUES (?, ?, ?, ?, ?)");
            $timeline_stmt->bind_param("iisss", $complaint_id, $admin_id, $old_status, $new_status, $remarks);
            $timeline_stmt->execute();
            $timeline_stmt->close();

            setFlash('success', 'Complaint status updated to "' . $new_status . '" successfully!');
            header("Location: " . base_url("/pages/admin/view_complaint.php?id=" . $complaint_id));
            exit();
        } else {
            $errors[] = 'Failed to update status. Please try again.';
        }
        $update_stmt->close();
    }
}

$pageTitle = 'Update Status';
include __DIR__ . '/../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-pencil-square me-2"></i>Update Complaint Status</h2>
            <a href="<?php echo base_url("/pages/admin/view_complaint.php?id=" . $complaint['id']); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>

        <!-- Complaint Summary -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5><?php echo htmlspecialchars($complaint['title']); ?></h5>
                <div class="row">
                    <div class="col-md-4">
                        <strong>By:</strong> <?php echo htmlspecialchars($complaint['user_name']); ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Department:</strong>
                        <span class="badge dept-<?php echo strtolower($complaint['department']); ?>">
                            <?php echo $complaint['department']; ?>
                        </span>
                    </div>
                    <div class="col-md-4">
                        <strong>Current Status:</strong>
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
            </div>
        </div>

        <!-- Update Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i>Change Status</h5>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" id="status" name="status" required>
                            <option value="">-- Select New Status --</option>
                            <option value="Pending" <?php echo $complaint['status'] === 'Pending' ? 'disabled' : ''; ?>>
                                Pending
                            </option>
                            <option value="In Progress" <?php echo $complaint['status'] === 'In Progress' ? 'disabled' : ''; ?>>
                                In Progress
                            </option>
                            <option value="Done" <?php echo $complaint['status'] === 'Done' ? 'disabled' : ''; ?>>
                                Done
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="form-label">Remarks (Optional)</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"
                                  placeholder="Add any notes about this status change..."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Update Status
                        </button>
                        <a href="<?php echo base_url("/pages/admin/complaints.php"); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
