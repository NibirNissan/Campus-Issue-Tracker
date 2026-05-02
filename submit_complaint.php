<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $department = $_POST['department'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];
    
    // File upload logic
    $image_path = null;
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    if (!empty($title) && !empty($department) && !empty($description)) {
        $stmt = $pdo->prepare("INSERT INTO complaints (user_id, title, department, description, image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $title, $department, $description, $image_path])) {
            $success = "Complaint submitted successfully!";
        } else {
            $error = "Failed to submit complaint.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <a href="dashboard.php" class="btn btn-link text-decoration-none p-0 text-muted">&larr; Back to Dashboard</a>
        <h2 class="mt-2">Submit New Complaint</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card p-4">
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?> 
                    <a href="dashboard.php" class="alert-link">View my complaints</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title / Subject</label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Short summary of the issue" required>
                </div>
                
                <div class="mb-3">
                    <label for="department" class="form-label">Department</label>
                    <select name="department" class="form-select" id="department" required>
                        <option value="" selected disabled>Select Department</option>
                        <option value="Lab">Lab</option>
                        <option value="Class">Class</option>
                        <option value="Hostel">Hostel</option>
                        <option value="Library">Library</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Detailed Description</label>
                    <textarea name="description" class="form-control" id="description" rows="5" placeholder="Please provide as much detail as possible..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label">Attach Image (Optional)</label>
                    <input type="file" name="image" class="form-control" id="image" accept="image/*">
                    <div class="form-text text-muted">Upload a photo proof of the issue (Max 2MB).</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <button type="submit" class="btn btn-primary px-5">Submit Complaint</button>
                    <a href="dashboard.php" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 bg-light">
            <h5>Tips for reporting</h5>
            <ul class="small mt-3">
                <li>Be specific about the location.</li>
                <li>Include the room number if applicable.</li>
                <li>Describe the problem clearly.</li>
                <li>Attach a clear photo for faster resolution.</li>
            </ul>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
