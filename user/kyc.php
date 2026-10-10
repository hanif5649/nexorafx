<?php 
include('layout/header.php'); 

$user_id = $_SESSION['user_id'] ?? null; 
$message = "";

// --- KYC SUBMISSION HANDLER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_kyc'])) {
    $doc_type = htmlspecialchars($_POST['document_type']);
    
    // Check if user already has a pending or verified KYC
    $check = $pdo->prepare("SELECT status FROM kyc WHERE user_id = ? AND status IN ('pending', 'verified')");
    $check->execute([$user_id]);
    
    if ($check->rowCount() > 0) {
        $message = "You already have an active or pending verification request.";
    } else {
        $target_dir = "uploads/kyc/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        if (!empty($_FILES["id_image"]["name"])) {
            $file_ext = strtolower(pathinfo($_FILES["id_image"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            
            if (in_array($file_ext, $allowed)) {
                $file_name = "KYC_" . time() . "_" . $user_id . "." . $file_ext;
                $target_file = $target_dir . $file_name;
                
                if (move_uploaded_file($_FILES["id_image"]["tmp_name"], $target_file)) {
                    $stmt = $pdo->prepare("INSERT INTO kyc (user_id, document_type, id_images, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
                    if ($stmt->execute([$user_id, $doc_type, $target_file])) {
                        $message = "Documents uploaded. Verification is now in progress.";
                    }
                } else {
                    $message = "File upload failed. Check folder permissions.";
                }
            } else {
                $message = "Format not supported. Use JPG, PNG or PDF.";
            }
        }
    }
}

// Fetch current KYC status
$stmt = $pdo->prepare("SELECT * FROM kyc WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$kyc_data = $stmt->fetch();
?>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="max-w-6xl mx-auto pb-24 px-6">

    <!-- Header Section -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-white/5 pb-8">
        <div>
            <h2 class="text-3xl font-black font-['Syne'] text-white uppercase tracking-tighter">Identity <span class="text-golf-green">Vault</span></h2>
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.4em] mt-2">Compliance & Security Verification</p>
        </div>
        
        <?php if($kyc_data): ?>
            <?php 
                $statusClass = match($kyc_data['status']) {
                    'verified' => 'text-golf-green bg-golf-green/10 border-golf-green/20',
                    'pending'  => 'text-yellow-500 bg-yellow-500/10 border-yellow-500/20',
                    'rejected' => 'text-red-500 bg-red-500/10 border-red-500/20',
                };
            ?>
            <div class="flex items-center gap-4 bg-surface border border-white/5 px-6 py-3 rounded-2xl">
                <p class="text-[9px] text-gray-500 font-black uppercase tracking-widest">Current Status:</p>
                <span class="px-4 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest <?= $statusClass ?>">
                    <?= $kyc_data['status'] ?>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <?= $message ? "<div class='bg-white/5 border border-white/10 p-4 rounded-xl mb-8 text-center text-xs font-black uppercase tracking-widest text-golf-green'>$message</div>" : "" ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Requirements Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-surface border border-white/5 p-8 rounded-[2.5rem]">
                <p class="text-xs font-black text-golf-green uppercase tracking-widest mb-6">Verification Guide</p>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="text-golf-green font-black font-mono text-xl">01</div>
                        <div>
                            <p class="text-[10px] text-white font-black uppercase tracking-widest mb-1">Clear Capture</p>
                            <p class="text-[11px] text-gray-500 leading-relaxed">Ensure all four corners of the ID are visible. Avoid glare or blurry photos.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="text-golf-green font-black font-mono text-xl">02</div>
                        <div>
                            <p class="text-[10px] text-white font-black uppercase tracking-widest mb-1">Valid Documents</p>
                            <p class="text-[11px] text-gray-500 leading-relaxed">We accept International Passports, Driver's Licenses, or National ID Cards.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="text-golf-green font-black font-mono text-xl">03</div>
                        <div>
                            <p class="text-[10px] text-white font-black uppercase tracking-widest mb-1">Data Match</p>
                            <p class="text-[11px] text-gray-500 leading-relaxed">The name on your document must match your account profile name.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Box -->
            <div class="bg-deep-dark border border-white/5 p-6 rounded-2xl text-center">
                <p class="text-[9px] text-gray-500 font-black uppercase tracking-widest mb-2">Need Assistance?</p>
                <a href="#" class="text-[11px] text-white font-bold hover:text-golf-green transition-colors underline underline-offset-4">Open Live Chat Support</a>
            </div>
        </div>

        <!-- Application Section -->
        <div class="lg:col-span-8">
            <?php if(!$kyc_data || $kyc_data['status'] == 'rejected'): ?>
                <div class="bg-surface border border-white/5 rounded-[2.5rem] p-8 md:p-12 shadow-3xl">
                    <h3 class="text-xl font-black text-white uppercase tracking-tighter mb-8">Submit <span class="text-golf-green">Documentation</span></h3>
                    
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-8" x-data="{ fileName: '' }">
                        <div>
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Document Category</label>
                            <select name="document_type" required class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-6 py-4 text-white font-bold outline-none text-xs uppercase tracking-widest appearance-none focus:border-golf-green/50">
                                <option value="">Select Document Type</option>
                                
                                <option value="Passport">International Passport</option>
                                <option value="Drivers License">Driver's License</option>
                                <option value="Voters Card">Permanent Voter's Card</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Upload ID (Front & Back merged or Page 1)</label>
                            <div class="relative group mt-2">
                                <input type="file" name="id_image" required @change="fileName = $event.target.files[0].name"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                <div class="bg-deep-dark border-2 border-dashed border-white/10 rounded-3xl py-16 text-center group-hover:border-golf-green/40 transition-all">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-golf-green mb-4 opacity-40"></i>
                                    <p class="text-white font-black text-[11px] uppercase tracking-widest" x-text="fileName ? fileName : 'Click to Browse Files'"></p>
                                    <p class="text-gray-600 text-[9px] mt-2 uppercase tracking-widest font-bold">Max size: 5MB (JPG, PNG, PDF)</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="submit_kyc" class="w-full bg-white text-deep-dark font-black py-5 rounded-2xl uppercase text-[11px] tracking-[0.4em] hover:bg-golf-green transition-all shadow-xl">
                            Request Verification
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Status Display for Pending/Verified -->
                <div class="bg-surface border border-white/5 rounded-[2.5rem] p-12 text-center shadow-3xl">
                    <div class="w-20 h-20 mx-auto mb-8 rounded-full flex items-center justify-center <?= $kyc_data['status'] == 'verified' ? 'bg-golf-green/20 text-golf-green' : 'bg-yellow-500/20 text-yellow-500' ?>">
                        <i class="fa-solid <?= $kyc_data['status'] == 'verified' ? 'fa-user-check' : 'fa-hourglass-half' ?> text-3xl"></i>
                    </div>
                    
                    <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-4">
                        <?= $kyc_data['status'] == 'verified' ? 'Identity Verified' : 'Review in Progress' ?>
                    </h3>
                    
                    <p class="text-gray-500 text-xs leading-relaxed max-w-sm mx-auto uppercase tracking-widest font-bold">
                        <?= $kyc_data['status'] == 'verified' 
                            ? 'Your account is fully compliant. You now have unrestricted access to all ecosystem features.' 
                            : 'We are currently reviewing your documents. You will receive a notification once the audit is complete.' ?>
                    </p>

                    <div class="mt-10 pt-10 border-t border-white/5">
                        <p class="text-[9px] text-gray-600 font-black uppercase tracking-widest">Submitted on: <?= date('F d, Y', strtotime($kyc_data['created_at'])) ?></p>
                        <p class="text-[9px] text-gray-600 font-black uppercase tracking-widest mt-2">ID Reference: #<?= str_pad($kyc_data['id'], 6, "0", STR_PAD_LEFT) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>