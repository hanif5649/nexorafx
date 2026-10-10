<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

/**
 * LOGIC: ADD PROFIT IN BITS
 */
if (isset($_POST['add_bit_profit'])) {
    $inv_id = $_POST['inv_id'];
    $user_id = $_POST['user_id'];
    $bit_amount = $_POST['bit_amount'];
    $remark = $_POST['admin_remark'];

    try {
        $pdo->beginTransaction();

        // 1. Update investment accrued returns and remark
        $stmt = $pdo->prepare("UPDATE investments SET returns_accrued = returns_accrued + ?, admin_remark = ? WHERE id = ?");
        $stmt->execute([$bit_amount, $remark, $inv_id]);

        // 2. Log to Transaction Table (for user transparency)
        $tx_stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'profit', ?, 'Investment growth credited')");
        $tx_stmt->execute([$user_id, $bit_amount]);

        $pdo->commit();
        echo "<script>alert('Profit injected successfully'); window.location='active_investments.php?tab=plans';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("System Error: " . $e->getMessage());
    }
}

/**
 * LOGIC: FINAL COMPLETION (Transfer all accrued to user profit balance)
 */
if (isset($_GET['complete_inv'])) {
    $inv_id = $_GET['complete_inv'];

    try {
        $pdo->beginTransaction();

        // 1. Get current accrued amount and user_id
        $inv = $pdo->prepare("SELECT user_id, returns_accrued FROM investments WHERE id = ?");
        $inv->execute([$inv_id]);
        $data = $inv->fetch();

        if ($data && $data['returns_accrued'] > 0) {
            // 2. Add accrued amount to user's profit_balance
            $up_user = $pdo->prepare("UPDATE users SET profit_balance = profit_balance + ? WHERE id = ?");
            $up_user->execute([$data['returns_accrued'], $data['user_id']]);
        }

        // 3. Mark investment as completed
        $up_inv = $pdo->prepare("UPDATE investments SET status = 'completed' WHERE id = ?");
        $up_inv->execute([$inv_id]);

        $pdo->commit();
        echo "<script>window.location='active_investments.php?tab=plans';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Completion Error: " . $e->getMessage());
    }
}

$tab = $_GET['tab'] ?? 'plans';

// Fetch Running Plans
// Modified Query: Fetch both Running and Completed Plans
$plans_stmt = $pdo->query("SELECT i.*, u.fullname, u.email, p.plan_name 
                           FROM investments i 
                           JOIN users u ON i.user_id = u.id 
                           JOIN inv_plans p ON i.plan_id = p.id 
                           WHERE i.status IN ('running', 'completed') 
                           ORDER BY CASE WHEN i.status = 'running' THEN 1 ELSE 2 END, i.start_date DESC");
$running_plans = $plans_stmt->fetchAll();

// Fetch Open Assets
$assets_stmt = $pdo->query("SELECT ai.*, u.fullname, ta.asset_name, ta.symbol 
                            FROM asset_investments ai 
                            JOIN users u ON ai.user_id = u.id 
                            JOIN tradable_assets ta ON ai.asset_id = ta.id 
                            WHERE ai.status = 'open' ORDER BY ai.created_at DESC");
$open_assets = $assets_stmt->fetchAll();
?>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold mb-0">Active Portfolio Monitor</h5>
            <p class="text-secondary small">Real-time control over running investments and trade positions.</p>
        </div>
        
        <div class="btn-group p-1 bg-secondary bg-opacity-10 rounded-pill">
            <a href="?tab=plans" class="btn btn-sm rounded-pill px-4 <?= $tab == 'plans' ? 'btn-primary shadow-sm' : 'text-secondary' ?>">
                Plan ROI
            </a>
            <a href="?tab=assets" class="btn btn-sm rounded-pill px-4 <?= $tab == 'assets' ? 'btn-primary shadow-sm' : 'text-secondary' ?>">
                Asset Trades
            </a>
        </div>
    </div>

   <?php if($tab == 'plans'): ?>
    <div class="row g-4">
      <?php foreach($running_plans as $inv): 
        $is_completed = ($inv['status'] === 'completed');
        $start = strtotime($inv['start_date']);
        $end = strtotime($inv['end_date']);
        $diff_seconds = $end - $start;
        $total_days = ($diff_seconds > 0) ? $diff_seconds / 86400 : 0;
        $days_passed = (time() - $start) / 86400;

        if ($is_completed) {
            $percent = 100;
        } else {
            $percent = ($total_days > 0) ? min(100, max(0, ($days_passed / $total_days) * 100)) : 100;
        }
      ?>
        <div class="col-xl-6">
            <!-- Added opacity-75 for completed cards to visually push them back -->
            <div class="ledger-container p-4 border rounded-4 shadow-sm h-100 <?= $is_completed ? 'bg-dark opacity-75' : 'bg-secondary' ?>">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($inv['plan_name']) ?></span>
                            <?php if($is_completed): ?>
                                <span class="badge bg-success"><i class="fa-solid fa-check-double me-1"></i> Completed</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Running</span>
                            <?php endif; ?>
                        </div>
                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($inv['fullname']) ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($inv['email'] ?? '') ?></small>
                    </div>
                    <div class="text-end">
                        <div class="stat-label">Capital Staked</div>
                        <div class="h5 fw-bold mb-0">$<?= number_format($inv['amount'], 2) ?></div>
                    </div>
                </div>

                <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                    <div class="col-6">
                        <div class="stat-label">Total Accrued</div>
                        <div class="h5 fw-bold text-success mb-0">$<?= number_format($inv['returns_accrued'], 2) ?></div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="stat-label">Maturity</div>
                        <div class="fw-bold"><?= date('M d, Y', strtotime($inv['end_date'])) ?></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Lifecycle Progress</span>
                        <span><?= round($percent) ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar <?= $is_completed ? 'bg-success' : 'progress-bar-striped progress-bar-animated' ?>" 
                             style="width: <?= $percent ?>%"></div>
                    </div>
                </div>

                <?php if(!$is_completed): ?>
                    <form method="POST" class="mt-4 pt-3 border-top">
                        <input type="hidden" name="inv_id" value="<?= $inv['id'] ?>">
                        <input type="hidden" name="user_id" value="<?= $inv['user_id'] ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="small fw-bold">Add Profit ($)</label>
                                <input type="number" step="0.01" name="bit_amount" class="form-control form-control-sm" placeholder="0.00" required>
                            </div>
                            <div class="col-md-8">
                                <label class="small fw-bold">Remark / Internal Note</label>
                                <input type="text" name="admin_remark" class="form-control form-control-sm" value="<?= htmlspecialchars($inv['admin_remark'] ?? '') ?>" placeholder="Update status...">
                            </div>
                            <div class="col-12 d-flex gap-2 mt-2">
                                <button type="submit" name="add_bit_profit" class="btn btn-dark btn-sm rounded-pill flex-grow-1">
                                    <i class="fa-solid fa-plus me-1"></i> Inject Profit
                                </button>
                                <a href="?complete_inv=<?= $inv['id'] ?>" class="btn btn-success btn-sm rounded-pill flex-grow-1" onclick="return confirm('Complete this investment?')">
                                    <i class="fa-solid fa-flag-checkered me-1"></i> Finalize
                                </a>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="mt-4 pt-3 border-top text-center">
                        <span class="text-muted small"><i class="fa-solid fa-lock me-1"></i> This investment is finalized and locked.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php else: ?>
        <div class="ledger-container p-0 overflow-hidden border rounded-4 bg-white shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Investor</th>
                            <th>Asset / Symbol</th>
                            <th>Units / Price</th>
                            <th>Total Cost</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($open_assets as $asset): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?= htmlspecialchars($asset['fullname']) ?></div>
                                <span class="badge bg-<?= $asset['trade_type'] == 'buy' ? 'success' : 'danger' ?>-subtle text-<?= $asset['trade_type'] == 'buy' ? 'success' : 'danger' ?> small px-2">
                                    <?= strtoupper($asset['trade_type']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($asset['asset_name']) ?></div>
                                <code class="text-primary"><?= $asset['symbol'] ?></code>
                            </td>
                            <td>
                                <div class="fw-bold"><?= (float)$asset['units'] ?> Units</div>
                                <div class="small text-muted">@ $<?= number_format($asset['purchase_price'], 4) ?></div>
                            </td>
                            <td class="fw-bold text-dark">$<?= number_format($asset['total_cost'], 2) ?></td>
                            <td class="text-end pe-4">
                                <a href="?close_asset=<?= $asset['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Close this trade?')">Close</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .stat-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #6c757d; letter-spacing: 0.5px; }
    .ledger-container { border: 1px solid rgba(0,0,0,0.05) !important; transition: transform 0.2s; }
    .ledger-container:hover { transform: translateY(-3px); }
</style>

<?php include('layout/footer.php'); ?>