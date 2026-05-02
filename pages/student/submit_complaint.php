<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireStudent();

$errors = [];
$title = '';
$department = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $department = $_POST['department'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $user_id = $_SESSION['user_id'];
    $image = null;

    // Validation
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($department)) {
        $errors[] = 'Department is required.';
    }
    if (empty($description)) {
        $errors[] = 'Description is required.';
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = 'Only JPG, PNG, GIF, and WebP images are allowed.';
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors[] = 'Image size must be less than 5MB.';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'complaint_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/../../uploads/' . $image;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $errors[] = 'Failed to upload image. Please try again.';
                $image = null;
            }
        }
    }

    // Insert complaint
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, title, department, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $title, $department, $description, $image);

        if ($stmt->execute()) {
            $complaint_id = $stmt->insert_id;

            // Add initial timeline entry
            $timeline_stmt = $conn->prepare("INSERT INTO complaint_timeline (complaint_id, changed_by, old_status, new_status, remarks) VALUES (?, ?, NULL, 'Pending', 'Complaint submitted')");
            $timeline_stmt->bind_param("ii", $complaint_id, $user_id);
            $timeline_stmt->execute();
            $timeline_stmt->close();

            setFlash('success', 'Complaint submitted successfully!');
            header("Location: " . base_url("/pages/student/view_complaint.php?id=" . $complaint_id));
            exit();
        } else {
            $errors[] = 'Failed to submit complaint. Please try again.';
        }
        $stmt->close();
    }
}

$pageTitle = 'Submit Complaint';
include __DIR__ . '/../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Submit New Complaint</h4>
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

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Complaint Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?php echo htmlspecialchars($title); ?>"
                               placeholder="Enter a brief title for your complaint" required>
                    </div>

                    <div class="mb-3">
                        <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-select" id="department" name="department" required>
                            <option value="">-- Select Department --</option>
                            <?php
                            $departments = ['Lab', 'Class', 'Hostel', 'Library', 'Other'];
                            foreach ($departments as $dept):
                            ?>
                                <option value="<?php echo $dept; ?>"
                                    <?php echo ($department === $dept) ? 'selected' : ''; ?>>
                                    <?php echo $dept; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5"
                                  placeholder="Describe the issue in detail..." required><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label">Attach Image (Optional)</label>
                        <input type="file" class="form-control" id="image" name="image"
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="form-text">Supported: JPG, PNG, GIF, WebP. Max size: 5MB.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Submit Complaint
                        </button>
                        <a href="<?php echo base_url("/pages/student/dashboard.php"); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
