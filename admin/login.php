<?php require_once('../config/config.php');
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        
        $update = $pdo->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $update->execute([$admin['id']]);

        header("Location: dashboard.php");
    } else {
        $error = "The details you entered do not match our records.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= SITE_NAME ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --login-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-main: #1a1d21;
        }

        [data-bs-theme="dark"] {
            --login-bg: #0f1113;
            --card-bg: #16191c;
            --text-main: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--login-bg);
            font-size: 0.85rem;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        h4 { font-family: 'Outfit', sans-serif; font-weight: 700; color: var(--text-main); }

        .login-card {
            background-color: var(--card-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        }

        .form-label { font-weight: 500; font-size: 0.75rem; color: var(--bs-secondary); text-transform: uppercase; letter-spacing: 0.5px; }

        .form-control {
            font-size: 0.85rem;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            background-color: transparent;
        }

        .btn-primary {
            background-color: #1a1d21;
            border: none;
            font-weight: 600;
            padding: 0.6rem;
            border-radius: 8px;
            font-size: 0.85rem;
        }
        
        [data-bs-theme="dark"] .btn-primary {
            background-color: #f8f9fa;
            color: #1a1d21;
        }

        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            border: 1px solid var(--bs-border-color);
            background: var(--card-bg);
            color: var(--text-main);
        }
    </style>
</head>
<body>

<button class="theme-toggle" id="themeBtn">
    <i class="fa-solid fa-moon"></i>
</button>

<div class="login-card">
    <div class="text-center mb-4">
        <h4 class="mb-1"><?= SITE_NAME ?></h4>
        <p class="text-secondary small">Administrative Control Panel</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger border-0 py-2 small mb-3 text-center">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="name@company.com" required>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between">
                <label class="form-label">Password</label>
                <a href="#" class="text-decoration-none small" style="font-size: 0.7rem;">Forgot?</a>
            </div>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            Sign In
        </button>
        
        <p class="text-center text-secondary m-0" style="font-size: 0.75rem;">
            Authorized personnel only.
        </p>
    </form>
</div>

<script>
    const themeBtn = document.getElementById('themeBtn');
    const html = document.documentElement;

    themeBtn.addEventListener('click', () => {
        if (html.getAttribute('data-bs-theme') === 'dark') {
            html.setAttribute('data-bs-theme', 'light');
            themeBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
        } else {
            html.setAttribute('data-bs-theme', 'dark');
            themeBtn.innerHTML = '<i class="fa-solid fa-sun"></i>';
        }
    });
</script>
</body>
</html>