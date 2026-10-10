<?php
require_once('../config/config.php');

$message = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
        $status = "danger";
    } else {
        // Hash the password using BCRYPT (Industry Standard)
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Check if an admin already exists to prevent duplicates
            $check = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
            $check->execute([$email]);

            if ($check->rowCount() > 0) {
                $message = "An administrator with this email already exists.";
                $status = "warning";
            } else {
                $stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $email, $hashedPassword])) {
                    $message = "Administrator account established successfully! Please delete this file for security.";
                    $status = "success";
                }
            }
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $status = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Setup | <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #0b0e14; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
        .setup-card { max-width: 450px; width: 100%; background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 2rem; }
        .form-control { background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 0.9rem; }
        .form-control:focus { background: rgba(0,0,0,0.3); color: #fff; border-color: #0d6efd; box-shadow: none; }
    </style>
</head>
<body>

<div class="setup-card shadow-lg">
    <div class="text-center mb-4">
        <div class="mb-2">
            <i class="fa-solid fa-screwdriver-wrench text-primary fa-2x"></i>
        </div>
        <h5 class="fw-bold">Initial Admin Setup</h5>
        <p class="text-secondary small">Create the primary super-user account</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $status ?> small py-2" role="alert">
            <i class="fa-solid <?= $status == 'success' ? 'fa-check-circle' : 'fa-triangle-exclamation' ?> me-2"></i>
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label small text-secondary">Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Master Admin" required>
        </div>
        <div class="mb-3">
            <label class="form-label small text-secondary">Work Email</label>
            <input type="email" name="email" class="form-control" placeholder="admin@domain.com" required>
        </div>
        <div class="mb-4">
            <label class="form-label small text-secondary">Secure Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 fw-bold">
            Create Admin Account
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="login.php" class="text-decoration-none small text-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Login
        </a>
    </div>
</div>

</body>
</html>