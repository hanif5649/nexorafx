<?php
// Start the session to access it
session_start();

// 1. Clear all session variables
$_SESSION = array();

// 2. If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finally, destroy the session on the server
session_destroy();

// 4. Redirect to the admin login page
header("Location: login.php?msg=logged_out");
exit();
?>