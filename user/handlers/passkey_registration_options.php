<?php
header('Content-Type: application/json');
session_start();
include('../config/config.php'); include('../config/config.php');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'UNAUTHORIZED_SESSION']);
    exit;
}

// Get the JSON input from the fetch request
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'INVALID_PAYLOAD']);
    exit;
}

try {
    /**
     * NOTE: In a full production environment, you would use a library 
     * like "web-authn-lib" to verify the signature and challenge.
     * Below is the logic to store the credential after verification.
     */
    
    $credentialId = $input['id'];
    $attestationObject = $input['response']['attestationObject'];
    $clientDataJSON = $input['response']['clientDataJSON'];
    
    // In 2026, we prioritize security; we check if this device is already linked
    $check = $pdo->prepare("SELECT id FROM user_passkeys WHERE user_id = ? AND credential_id = ?");
    $check->execute([$user_id, $credentialId]);
    
    if ($check->rowCount() > 0) {
        echo json_encode(['success' => false, 'error' => 'DEVICE_ALREADY_LINKED']);
        exit;
    }

    // Prepare data for the vault
    $deviceType = $_SERVER['HTTP_USER_AGENT']; // Capture device info for user reference
    $publicKey = $attestationObject; // Simplified: usually extracted from CBOR
    
    $pdo->beginTransaction();

    // 1. Insert into user_passkeys table
    $stmt = $pdo->prepare("
        INSERT INTO user_passkeys (
            user_id, 
            credential_id, 
            public_key, 
            device_type, 
            created_at
        ) VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $user_id,
        $credentialId,
        $publicKey,
        $deviceType
    ]);

    // 2. Update the main users table to enable WebAuthn status
    $updateUser = $pdo->prepare("UPDATE users SET webauthn_id = ?, two_factor_enabled = 1 WHERE id = ?");
    $updateUser->execute([$credentialId, $user_id]);

    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'BIOMETRIC_VAULT_ESTABLISHED'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false, 
        'error' => 'ENCRYPTION_FAILURE', 
        'details' => $e->getMessage()
    ]);
}