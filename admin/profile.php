<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

$admin_id = $_SESSION['admin_id'];
$msg = "";
$error = "";

// Fetch Current Data
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle Profile & Password Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';

    // Verify current password first
    if (password_verify($current_pass, $admin['password'])) {
        try {
            // Update Name and Email
            $up = $pdo->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
            $up->execute([$name, $email, $admin_id]);

            // Update Password only if provided
            if (!empty($new_pass)) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $upPass = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $upPass->execute([$hashed_pass, $admin_id]);
            }

            $msg = "Account security and profile updated successfully!";
            // Refresh local data
            $admin['name'] = $name;
            $admin['email'] = $email;
        } catch (PDOException $e) {
            $error = "Update failed. Email might already be in use.";
        }
    } else {
        $error = "Current password verification failed.";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="mb-4">
                <h4 class="fw-bold mb-0">Admin Security Settings</h4>
                <p class="text-secondary small">Manage your credentials and login access</p>
            </div>

            <?php if($msg): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                    <i class="fa-solid fa-shield-check me-2"></i> <?= $msg ?>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="row g-4">
                    <!-- Identity Section -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 text-uppercase small text-primary">Identity Details</h6>
                                
                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">Full Administrative Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text  border-0"><i class="fa-solid fa-user-tie text-muted"></i></span>
                                        <input type="text" name="name" class="form-control border-0" 
                                               value="<?= htmlspecialchars($admin['name'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">Account Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-0" 
                                               value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 bg-light rounded-3">
                                    <div class="x-small text-uppercase text-secondary fw-bold mb-1">Account Metadata</div>
                                    <div class="small text-dark">
                                        Created: <?= date('M d, Y', strtotime($admin['created_at'])) ?><br>
                                        Last Login: <?= $admin['last_login'] ? date('M d, H:i', strtotime($admin['last_login'])) : 'First Session' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 text-uppercase small text-danger">Access Security</h6>
                                
                                <div class="mb-3">
                                    <label class="small fw-bold mb-2 text-danger">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                        <input type="password" name="new_password" class="form-control border-0 bg-light" 
                                               placeholder="Leave blank to keep current">
                                    </div>
                                    <div class="x-small text-muted mt-1">Minimum 8 characters recommended.</div>
                                </div>

                                <hr class="my-4 opacity-50">

                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">Verify Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-primary border-opacity-25"><i class="fa-solid fa-key text-primary"></i></span>
                                        <input type="password" name="current_password" class="form-control border-primary border-opacity-25" 
                                               placeholder="Required to save changes" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm mt-2">
                                    Save Security Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<style>
    .input-group-text { border-radius: 12px 0 0 12px; }
    .form-control { border-radius: 0 12px 12px 0; padding: 12px; }
    .form-control:focus { box-shadow: none; background: #fff !important; border: 1px solid #0d6efd !important; }
    .x-small { font-size: 0.7rem; letter-spacing: 0.5px; }
</style>

<?php include('layout/footer.php'); ?>