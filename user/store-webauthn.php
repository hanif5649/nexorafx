<?php
session_start();
header('Content-Type: application/json');
include('../config/config.php');

// Ensure user is actually logged in/registered and has a session ID
if (!isset($_SESSION['temp_user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please restart registration.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$userId = $_SESSION['temp_user_id'];

if ($data && isset($data['id'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO user_passkeys (user_id, credential_id, public_key, device_type) VALUES (:uid, :cid, :pk, :dev)");
        $stmt->execute([
            ':uid' => $userId,
            ':cid' => $data['id'],
            ':pk'  => $data['publicKey'], // Stored as string/base64
            ':dev' => $_SERVER['HTTP_USER_AGENT']
        ]);

        // Mark user as 2FA enabled in the main table
        $update = $pdo->prepare("UPDATE users SET two_factor_enabled = 1 WHERE id = :uid");
        $update->execute([':uid' => $userId]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Vault storage error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid biometric packet.']);
}