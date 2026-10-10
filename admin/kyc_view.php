<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

if (!isset($_GET['id'])) { header("Location: kyc_manage.php"); exit(); }

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT k.*, u.fullname, u.email FROM kyc k JOIN users u ON k.user_id = u.id WHERE k.id = ?");
$stmt->execute([$id]);
$kyc = $stmt->fetch();

if(!$kyc) { header("Location: kyc_manage.php"); exit(); }

// Split the string into an array (assuming comma-separated paths or JSON)
$images = explode(',', $kyc['id_images']);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="kyc_manage.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
                <div class="d-flex gap-2">
                    <a href="kyc_manage.php?approve=<?= $kyc['id'] ?>" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm">Approve Document</a>
                    <a href="kyc_manage.php?reject=<?= $kyc['id'] ?>" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">Reject</a>
                </div>
            </div>

            <div class="row g-4">
                <!-- User Profile Summary -->
                <div class="col-md-4">
                    <div class="ledger-container p-4">
                        <h6 class="stat-label text-primary mb-4">Submission Summary</h6>
                        
                        <div class="mb-4">
                            <label class="small text-secondary fw-bold">Full Name</label>
                            <div class="h6"><?= htmlspecialchars($kyc['fullname']) ?></div>
                        </div>

                        <div class="mb-4">
                            <label class="small text-secondary fw-bold">Email Address</label>
                            <div class="h6"><?= $kyc['email'] ?></div>
                        </div>

                        <hr class="my-4 border-light">

                        <div class="mb-4">
                            <label class="small text-secondary fw-bold">Document Type</label>
                            <div class="h6"><?= strtoupper($kyc['document_type']) ?></div>
                        </div>

                        <div class="mb-4">
                            <label class="small text-secondary fw-bold">Current Status</label>
                            <div>
                                <?php if($kyc['status'] == 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Under Review</span>
                                <?php elseif($kyc['status'] == 'verified'): ?>
                                    <span class="badge bg-success">Verified Account</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Images Gallery -->
                <div class="col-md-8">
                    <div class="ledger-container p-4">
                        <h6 class="stat-label text-primary mb-4">Identity Documents</h6>
                        <div class="row g-4">
                            <?php foreach($images as $img): $img = trim($img); ?>
                                <?php if(!empty($img)): ?>
                                <div class="col-sm-6">
                                    <div class="card border rounded-4 shadow-sm overflow-hidden h-100">
                                        <a href="../uploads/kyc/<?= $img ?>" target="_blank">
                                            <img src="../uploads/kyc/<?= $img ?>" class="img-fluid w-100" style="height: 300px; object-fit: contain; background: #f8f9fa;" alt="ID Preview">
                                        </a>
                                        <div class="card-footer bg-white text-center border-0 py-3">
                                            <a href="../uploads/kyc/<?= $img ?>" download class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fa-solid fa-download me-1"></i> Full Resolution
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>