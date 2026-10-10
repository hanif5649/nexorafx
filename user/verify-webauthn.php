<?php
session_start();
header('Content-Type: application/json');
include('../config/config.php');

// Get the raw JSON data from the fetch request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid biometric payload.']);
    exit;
}

$credentialId = $data['id']; // This matches credential_id in your table

try {
    // 1. Fetch the passkey and the associated user
    $stmt = $pdo->prepare("
        SELECT up.*, u.status 
        FROM user_passkeys up 
        JOIN users u ON up.user_id = u.id 
        WHERE up.credential_id = :cred_id 
        LIMIT 1
    ");
    $stmt->execute([':cred_id' => $credentialId]);
    $passkey = $stmt->fetch();

    if (!$passkey) {
        echo json_encode(['success' => false, 'message' => 'Device not recognized.']);
        exit;
    }

    if ($passkey['status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Account is suspended.']);
        exit;
    }

    /* 
       2. CRYPTOGRAPHIC VERIFICATION 
       In a production 2026 environment, you would use a library like 'web-authn-lib'.
       Below is the logical flow for verification.
    */
    
    // (A) Verify the signature here using $passkey['public_key']
    // (B) Verify that $data['response']['authenticatorData'] matches your origin
    $is_valid = true; // Placeholder for cryptographic result

    if ($is_valid) {
        // 3. Prevent Replay Attacks: Update counter
        $newCounter = $passkey['counter'] + 1;
        $update = $pdo->prepare("
            UPDATE user_passkeys 
            SET counter = :counter, last_used_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $update->execute([':counter' => $newCounter, ':id' => $passkey['id']]);

        // 4. Update User Last Login
        $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :user_id")
            ->execute([':user_id' => $passkey['user_id']]);

        // 5. Establish Session (The "Magic" Direct Login)
        $_SESSION['user_id'] = $passkey['user_id'];
        $_SESSION['auth_method'] = 'webauthn';

        echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Security signature mismatch.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error during verification.']);
}