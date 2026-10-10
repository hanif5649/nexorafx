<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// --- ACTIONS ---

// Delete Message
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    echo "<script>window.location='user_messages.php?msg=deleted';</script>";
    exit();
}

// Fetch all messages joined with user info
$stmt = $pdo->query("
    SELECT n.*, u.fullname, u.email 
    FROM notifications n 
    JOIN users u ON n.user_id = u.id 
    ORDER BY n.created_at DESC
");
$messages = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Communications Log</h4>
            <p class="text-secondary small mb-0">Track and manage notifications sent to investors</p>
        </div>
        <a href="message_compose.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-paper-plane me-2"></i> Send New Message
        </a>
    </div>

    <div class="ledger-container p-0 overflow-hidden shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 text-uppercase small fw-bold text-secondary">Recipient</th>
                        <th class="border-0 text-uppercase small fw-bold text-secondary">Subject</th>
                        <th class="border-0 text-uppercase small fw-bold text-secondary">Status</th>
                        <th class="border-0 text-uppercase small fw-bold text-secondary">Sent Date</th>
                        <th class="text-end pe-4 border-0 text-uppercase small fw-bold text-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($messages)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-secondary">No messages have been sent yet.</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach($messages as $msg): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold"><?= htmlspecialchars($msg['fullname']) ?></div>
                            <div class="small text-muted"><?= $msg['email'] ?></div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small"><?= htmlspecialchars($msg['title']) ?></div>
                            <div class="text-truncate text-secondary small" style="max-width: 250px;">
                                <?= htmlspecialchars($msg['message']) ?>
                            </div>
                        </td>
                        <td>
                            <?php if($msg['is_read']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3">Read</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3">Unread</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-secondary"><?= date('M d, Y @ H:i', strtotime($msg['created_at'])) ?></td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="message_compose.php?reply_to=<?= $msg['user_id'] ?>&subject=Re: <?= urlencode($msg['title']) ?>" class="btn btn-sm btn-white border-color">
                                    <i class="fa-solid fa-reply"></i>
                                </a>
                                <a href="?delete=<?= $msg['id'] ?>" class="btn btn-sm btn-white border-color text-danger" onclick="return confirm('Delete this message log?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>