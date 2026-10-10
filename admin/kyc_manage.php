<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// --- ACTIONS ---

// Approve KYC
if (isset($_GET['approve'])) {
    $stmt = $pdo->prepare("UPDATE kyc SET status = 'verified' WHERE id = ?");
    $stmt->execute([$_GET['approve']]);
    echo "<script>window.location='kyc_manage.php?msg=approved';</script>";
    exit();
}

// Reject KYC
if (isset($_GET['reject'])) {
    $stmt = $pdo->prepare("UPDATE kyc SET status = 'rejected' WHERE id = ?");
    $stmt->execute([$_GET['reject']]);
    echo "<script>window.location='kyc_manage.php?msg=rejected';</script>";
    exit();
}

// Delete KYC
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM kyc WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    echo "<script>window.location='kyc_manage.php?msg=deleted';</script>";
    exit();
}

// Fetch all KYC records with User details
$stmt = $pdo->query("SELECT k.*, u.fullname, u.email FROM kyc k JOIN users u ON k.user_id = u.id ORDER BY k.created_at DESC");
$kycs = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Identity Verification (KYC)</h4>
            <p class="text-secondary small mb-0">Manage and verify investor government documents</p>
        </div>
        <span class="badge bg-primary rounded-pill px-3 py-2"><?= count($kycs) ?> Submissions</span>
    </div>

    <div class="ledger-container p-0 overflow-hidden shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 text-uppercase small fw-bold text-secondary">Investor</th>
                        <th class="border-0 text-uppercase small fw-bold text-secondary">Document</th>
                        <th class="border-0 text-uppercase small fw-bold text-secondary">Date</th>
                        <th class="border-0 text-uppercase small fw-bold text-secondary">Status</th>
                        <th class="text-end pe-4 border-0 text-uppercase small fw-bold text-secondary">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($kycs)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-secondary">No KYC submissions found in the ledger.</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach($kycs as $row): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold"><?= htmlspecialchars($row['fullname']) ?></div>
                            <div class="small text-muted"><?= $row['email'] ?></div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark border fw-normal">
                                <i class="fa-solid fa-id-card me-1 small"></i> <?= strtoupper($row['document_type']) ?>
                            </span>
                        </td>
                        <td class="small text-secondary"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if($row['status'] == 'pending'): ?>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3">Pending Review</span>
                            <?php elseif($row['status'] == 'verified'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3">Verified</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="kyc_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-white border-color">View Docs</a>
                                <button type="button" class="btn btn-sm btn-white border-color dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item text-success" href="?approve=<?= $row['id'] ?>"><i class="fa-solid fa-check me-2"></i> Approve</a></li>
                                    <li><a class="dropdown-item text-warning" href="?reject=<?= $row['id'] ?>"><i class="fa-solid fa-xmark me-2"></i> Reject</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Permanently delete this KYC record?')"><i class="fa-solid fa-trash me-2"></i> Delete</a></li>
                                </ul>
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