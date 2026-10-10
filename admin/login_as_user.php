<?php
session_start();
include('../config/config.php'); // Ensure PDO is connected

// 1. Security Check: Only a logged-in Admin can do this
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access. Admin privileges required.");
}

// 2. Validate User ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users_list.php?error=Missing User ID");
    exit();
}

$user_id = $_GET['id'];

try {
    // 3. Fetch User Details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        // 4. Set User Session Variables
        // Match these keys exactly to what your user/dashboard.php expects
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['fullname'];
        
        // Optional: Flag this session so the user dashboard knows it's an admin impersonating
        $_SESSION['is_impersonating'] = true;

        // 5. Redirect to the User Dashboard
        // Since we are in /admin/, we go up one level then into /user/
        header("Location: ../user/dashboard.php"); 
        exit();
    } else {
        header("Location: users_list.php?error=User not found");
        exit();
    }

} catch (PDOException $e) {
    die("System Error: " . $e->getMessage());
}