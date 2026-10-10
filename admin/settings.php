<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// If table is empty, initialize default values to avoid NULL errors
if (!$settings) {
    $pdo->query("INSERT INTO site_settings (id, site_name) VALUES (1, 'My Platform')");
    $settings = [
        'site_name' => 'My Platform',
        'email' => '',
        'phone' => '',
        'address' => '',
        'logo_url' => ''
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_name = $_POST['site_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $logo_url = $settings['logo_url'] ?? '';

    // Handle Logo Upload
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = "../assets/img/";
        
        // Ensure directory exists
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }

        $file_ext = pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
        $file_name = "logo_" . time() . "." . $file_ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $logo_url = $file_name;
            // Delete old logo file if it exists to save space
            if (!empty($settings['logo_url']) && file_exists($target_dir . $settings['logo_url'])) {
                unlink($target_dir . $settings['logo_url']);
            }
        }
    }

    $update = $pdo->prepare("UPDATE site_settings SET site_name = ?, email = ?, phone = ?, address = ?, logo_url = ? WHERE id = 1");
    $update->execute([$site_name, $email, $phone, $address, $logo_url]);
    
    echo "<script>window.location='settings.php?msg=updated';</script>";
    exit();
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">System Configuration</h4>
                    <p class="text-secondary small">Global identity and contact parameters</p>
                </div>
                <?php if(isset($_GET['msg'])): ?>
                    <span class="badge bg-success-subtle text-success p-2 px-3 rounded-pill">
                        <i class="fa-solid fa-check-circle me-1"></i> Settings Saved
                    </span>
                <?php endif; ?>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <!-- Branding Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-dark py-3">
                        <h6 class="text-white mb-0 small fw-bold text-uppercase">Branding & Logo</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center border-end">
                                <label class="small fw-bold d-block mb-3">Current Logo</label>
                                <div class="bg-light rounded-3 p-3 mb-3 d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <?php if(!empty($settings['logo_url'])): ?>
                                        <img src="../assets/img/<?= htmlspecialchars($settings['logo_url']) ?>" class="img-fluid" style="max-height: 80px;" alt="Logo">
                                    <?php else: ?>
                                        <span class="text-muted x-small">No Logo Set</span>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="logo" class="form-control form-control-sm" accept="image/*">
                            </div>
                            <div class="col-md-8 ps-md-4">
                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">Platform Name</label>
                                    <input type="text" name="site_name" class="form-control border-color" 
                                           value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                                </div>
                                <p class="x-small text-muted mb-0">
                                    <i class="fa-solid fa-circle-info me-1"></i> This name appears in browser tabs and system-wide notifications.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 small fw-bold text-uppercase text-primary">Contact Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2">Support Email</label>
                                <input type="email" name="email" class="form-control border-color" 
                                       value="<?= htmlspecialchars($settings['email'] ?? '') ?>" placeholder="support@domain.com">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2">Support Phone</label>
                                <input type="text" name="phone" class="form-control border-color" 
                                       value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" placeholder="+1 234 567 890">
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold mb-2">Office Address</label>
                                <textarea name="address" class="form-control border-color" rows="3" 
                                          placeholder="Enter physical address..."><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid shadow-sm">
                    <button type="submit" class="btn btn-primary py-3 rounded-pill fw-bold">
                        Update Global Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .border-color { border-color: #eee !important; border-radius: 10px; }
    .border-color:focus { border-color: #0d6efd !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05); }
    .x-small { font-size: 0.7rem; }
    .italic { font-style: italic; }
</style>

<?php include('layout/footer.php'); ?>