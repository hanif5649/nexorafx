<?php session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

$users = $pdo->query("SELECT id, fullname, email FROM users ORDER BY fullname ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate'])) {
    $user_id = $_POST['user_id'];
    $count = (int)$_POST['tx_count'];
    $min_amt = $_POST['min_amount'];
    $max_amt = $_POST['max_amount'];

    $types = ['deposit', 'profit', 'investment', 'bonus'];

$desc_options = [
    'deposit' => [
        "External wallet funding via Blockchain",
        "Direct deposit clearance - Ref: " . bin2hex(random_bytes(4)),
        "Bank wire transfer confirmation",
        "Crypto payment gateway credit"
    ],
    'profit' => [
        "Daily ROI distribution for active plan",
        "Portfolio growth dividend",
        "Automated trade profit settlement",
        "Market fluctuation adjustment (Credit)"
    ],
    'investment' => [
        "Capital allocation for new trade position",
        "Fixed plan subscription fee",
        "Asset purchase - Order #" . rand(1000, 9999),
        "Stake participation - Tier 1"
    ],
    'bonus' => [
        "Welcome incentive for new account",
        "Referral commission payout",
        "Promotional loyalty reward",
        "Account anniversary bonus"
    ],
    'penalty' => [
        "Overdue maintenance fee",
        "Tax deduction on high-volume trade",
        "Correction of previous balance error",
        "Account inactivity surcharge"
    ]
];

for ($i = 0; $i < $count; $i++) {
    $amount = rand($min_amt * 100, $max_amt * 100) / 100;
    $type = $types[array_rand($types)];
    
    // Pick a random realistic description based on the type
    $possible_descs = $desc_options[$type];
    $desc = $possible_descs[array_rand($possible_descs)];
    
    // Random date within the last 30 days
    $timestamp = date('Y-m-d H:i:s', strtotime('-' . rand(1, 43200) . ' minutes'));

    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $amount, $type, $desc, $timestamp]);
}
    echo "<script>alert('Generated $count transactions!'); window.location='transactions.php';</script>";
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="ledger-container p-4 p-md-5 shadow-lg border-0">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary d-inline-block p-3 rounded-circle mb-3">
                        <i class="fa-solid fa-wand-magic-sparkles fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">History Generator</h4>
                    <p class="text-secondary small">Automatically populate a user's transaction history</p>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Target User</label>
                        <select name="user_id" class="form-select border-color select2" required>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= $u['fullname'] ?> (<?= $u['email'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="small fw-bold mb-2">Number of Logs</label>
                            <input type="number" name="tx_count" class="form-control border-color" value="10" max="50" required>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-2">Type Mix</label>
                            <select class="form-select border-color" disabled>
                                <option>Random Mix (Auto)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="small fw-bold mb-2">Min Amount ($)</label>
                            <input type="number" name="min_amount" class="form-control border-color" value="50">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-2">Max Amount ($)</label>
                            <input type="number" name="max_amount" class="form-control border-color" value="5000">
                        </div>
                    </div>

                    <button type="submit" name="generate" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow">
                        Generate History Now
                    </button>
                    <a href="transactions.php" class="btn btn-link w-100 text-secondary mt-2 small">Back to Ledger</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>