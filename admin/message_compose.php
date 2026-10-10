<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

$error = "";
$success = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_msg'])) {
    $user_id = $_POST['user_id'];
    $title = $_POST['title'];
    $message = $_POST['message'];

    if (!empty($user_id) && !empty($title) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $title, $message])) {
            $success = "Message sent successfully to the investor.";
        } else {
            $error = "Failed to send message. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}

// Pre-fill logic for Replies or direct messaging from User list
$selected_user = $_GET['reply_to'] ?? '';
$subject_line = $_GET['subject'] ?? '';

// Fetch all users for the dropdown
$users_stmt = $pdo->query("SELECT id, fullname, email FROM users ORDER BY fullname ASC");
$users = $users_stmt->fetchAll();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            
            <div class="mb-4">
                <a href="user_messages.php" class="text-decoration-none small text-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Messages
                </a>
                <h4 class="fw-bold mt-2">Compose Message</h4>
            </div>

            <?php if($success): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><?= $success ?></div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4"><?= $error ?></div>
            <?php endif; ?>

            <div class="ledger-container p-4">
                <form method="POST">
                    <div class="mb-4">
                        <label class="stat-label text-primary mb-2">Select Recipient</label>
                        <select name="user_id" class="form-select border-color rounded-3" required>
                            <option value="">-- Choose Investor --</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= ($selected_user == $u['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['fullname']) ?> (<?= $u['email'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="stat-label text-primary mb-2">Message Title</label>
                        <input type="text" name="title" class="form-control border-color rounded-3" 
                               value="<?= htmlspecialchars($subject_line) ?>" placeholder="e.g., Update on your KYC Status" required>
                    </div>

                    <div class="mb-4">
                        <label class="stat-label text-primary mb-2">Message Body</label>
                        <textarea name="message" class="form-control border-color rounded-3" rows="6" 
                                  placeholder="Type your message here..." required></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="send_msg" class="btn btn-primary btn-lg rounded-pill fw-bold">
                            <i class="fa-solid fa-paper-plane me-2"></i> Dispatch Message
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-4 p-3 bg-light rounded-4 small text-secondary">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i> 
                Investors will receive this as a notification in their dashboard instantly. You can bill your users directly from here.
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>