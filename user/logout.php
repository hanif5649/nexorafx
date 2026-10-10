<?php
/**
 * Logout Protocol - AuchiCity Ecosystem
 * Securely terminates sessions and purges authentication tokens.
 */

session_start();

// 1. Unset all session variables
$_SESSION = array();

// 2. Kill the session cookie itself
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 3. Clear any "Remember Me" or specific Auth cookies you might have set
if (isset($_COOKIE['user_auth_token'])) {
    setcookie('user_auth_token', '', time() - 3600, '/');
}

// 4. Destroy the session on the server
session_destroy();

// 5. Visual "Clearing Workspace" Delay (Optional but adds a premium feel)
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminating Session...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'golf-green': '#2ecc71',
                        'deep-dark': '#0a0f0d',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-deep-dark flex items-center justify-center min-h-screen overflow-hidden">

    <div class="text-center">
        <!-- Animated Shield/Lock Icon -->
        <div class="relative w-24 h-24 mx-auto mb-8">
            <div class="absolute inset-0 bg-golf-green/20 rounded-full animate-ping"></div>
            <div class="relative w-24 h-24 bg-deep-dark border-2 border-golf-green/50 rounded-full flex items-center justify-center shadow-[0_0_50px_-12px_rgba(46,204,113,0.3)]">
                <i class="fa-solid fa-shield-halved text-3xl text-golf-green"></i>
            </div>
        </div>

        <h2 class="text-white font-black text-xs uppercase tracking-[0.5em] mb-2 font-['Syne']">Securing Session</h2>
        <div class="flex items-center justify-center gap-1">
            <div class="w-1.5 h-1.5 bg-golf-green rounded-full animate-bounce [animation-delay:-0.3s]"></div>
            <div class="w-1.5 h-1.5 bg-golf-green rounded-full animate-bounce [animation-delay:-0.15s]"></div>
            <div class="w-1.5 h-1.5 bg-golf-green rounded-full animate-bounce"></div>
        </div>

        <p class="text-gray-600 text-[9px] font-bold uppercase tracking-widest mt-8">Safely redirecting to gateway...</p>
    </div>

    <script>
        // Redirect after a brief moment to allow session cleanup to settle
        setTimeout(() => {
            window.location.href = 'login.php?logout=success';
        }, 1500);
    </script>
</body>
</html>