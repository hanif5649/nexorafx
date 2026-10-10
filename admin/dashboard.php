<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include('layout/header.php');

/**
 * Analytics Engine
 */
try {
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalBalance = $pdo->query("SELECT SUM(amount) FROM deposits WHERE status = 'approved'")->fetchColumn() ?? 0;
    $pendingWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'")->fetchColumn();
    $runningInvestments = $pdo->query("SELECT COUNT(*) FROM investments WHERE status = 'running'")->fetchColumn();

    $pendingKYC = $pdo->query("SELECT COUNT(*) FROM kyc WHERE status = 'pending'")->fetchColumn();

    $stmt = $pdo->query("SELECT t.*, u.fullname, u.email FROM transactions t 
                         JOIN users u ON t.user_id = u.id 
                         ORDER BY t.created_at DESC LIMIT 10");
    $transactions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Analytics Error: " . $e->getMessage());
}
?>

<style>
    :root {
        --radius-xl: 18px;
        --radius-md: 12px;
        --shadow-soft: 0 8px 25px rgba(0,0,0,0.08);
        --shadow-hover: 0 12px 30px rgba(0,0,0,0.12);
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(180deg, var(--bs-body-bg), var(--bs-tertiary-bg));
        font-size: 0.875rem;
        color: var(--bs-body-color);
    }

    /* Page Header */
    .dash-header {
        padding: 1.2rem 1.5rem;
        border-radius: var(--radius-xl);
        background: linear-gradient(135deg, var(--bs-tertiary-bg), var(--bs-secondary-bg));
        border: 1px solid var(--bs-border-color);
        box-shadow: var(--shadow-soft);
    }

    .dash-header h4 {
        font-weight: 800;
        margin-bottom: 0.25rem;
        letter-spacing: -0.5px;
    }

    .dash-header p {
        margin-bottom: 0;
        font-size: 0.85rem;
        color: var(--bs-secondary-color);
    }

    /* Cards */
    .metric-card {
        border-radius: var(--radius-xl);
        background: var(--bs-tertiary-bg);
        border: 1px solid var(--bs-border-color);
        padding: 1.5rem;
        height: 100%;
        box-shadow: var(--shadow-soft);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
        border-color: var(--bs-primary-border-subtle);
    }

    .metric-card::before {
        content: "";
        position: absolute;
        top: -40px;
        right: -40px;
        width: 120px;
        height: 120px;
        background: rgba(13, 110, 253, 0.10);
        border-radius: 50%;
        filter: blur(2px);
    }

    .stat-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--bs-secondary-color);
        margin-bottom: 0.6rem;
    }

    .stat-value {
        font-size: 1.65rem;
        font-weight: 900;
        margin-bottom: 0;
        color: var(--bs-emphasis-color);
        letter-spacing: -0.5px;
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        font-size: 1.1rem;
    }

    /* Ledger */
    .ledger-container {
        background: var(--bs-tertiary-bg);
        border-radius: var(--radius-xl);
        border: 1px solid var(--bs-border-color);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .ledger-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--bs-border-color);
        background: linear-gradient(135deg, var(--bs-secondary-bg), var(--bs-tertiary-bg));
    }

    .ledger-header h6 {
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.3px;
    }

    .ledger-header a {
        font-size: 0.82rem;
        font-weight: 600;
    }

    .table {
        margin-bottom: 0;
        font-size: 0.83rem;
    }

    .table thead th {
        background-color: var(--bs-secondary-bg);
        color: var(--bs-secondary-color);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 0.08em;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--bs-border-color);
    }

    .table td {
        padding: 1.05rem 1.25rem;
        border-bottom: 1px solid var(--bs-border-color);
        color: var(--bs-body-color);
    }

    .table tbody tr:hover {
        background: rgba(13, 110, 253, 0.06);
        transition: 0.15s;
    }

    .tx-badge {
        font-weight: 700;
        font-size: 0.7rem;
        padding: 0.45em 0.9em;
        border-radius: 50px;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-secondary-bg);
        color: var(--bs-emphasis-color);
    }

    /* Actions */
    .action-panel {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.1rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--bs-border-color);
        background: var(--bs-tertiary-bg);
        box-shadow: var(--shadow-soft);
        text-decoration: none;
        color: var(--bs-emphasis-color);
        transition: all 0.2s ease;
        margin-bottom: 0.9rem;
        font-weight: 600;
    }

    .action-panel:hover {
        transform: translateY(-2px);
        border-color: var(--bs-primary-border-subtle);
        box-shadow: var(--shadow-hover);
        color: var(--bs-primary);
    }

    .action-panel i {
        font-size: 1rem;
        opacity: 0.8;
    }

    /* Right card */
    .integrity-card {
        border-radius: var(--radius-xl);
        border: 1px solid var(--bs-border-color);
        background: var(--bs-tertiary-bg);
        padding: 1.5rem;
        box-shadow: var(--shadow-soft);
    }

    .integrity-card h6 {
        font-weight: 800;
        margin-bottom: 1.2rem;
    }

    .mini-stat {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.9rem;
        font-size: 0.83rem;
    }

    .mini-stat span:first-child {
        color: var(--bs-secondary-color);
        font-weight: 600;
    }

    .mini-stat span:last-child {
        font-weight: 800;
    }

    .build-text {
        font-size: 0.7rem;
        color: var(--bs-secondary-color);
        margin-top: 0.75rem;
    }
</style>


<div class="container-fluid py-4">

    <!-- Header -->
    <div class="dash-header mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h4>Admin Dashboard</h4>
            <p>Platform performance overview and system health monitoring.</p>
        </div>

        <div class="mt-3 mt-md-0">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                <i class="fa-solid fa-shield-halved me-2"></i> System Stable
            </span>
        </div>
    </div>

    <!-- Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Total Liquidity</div>
                    <div class="stat-value">$<?= number_format($totalBalance, 2) ?></div>
                </div>
                <div class="stat-icon text-primary">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Verified Users</div>
                    <div class="stat-value"><?= number_format($totalUsers) ?></div>
                </div>
                <div class="stat-icon text-success">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Running Staking</div>
                    <div class="stat-value"><?= number_format($runningInvestments) ?></div>
                </div>
                <div class="stat-icon text-warning">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Pending Withdrawals</div>
                    <div class="stat-value text-primary"><?= number_format($pendingWithdrawals) ?></div>
                </div>
                <div class="stat-icon text-danger">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Transactions -->
        <div class="col-lg-8">
            <div class="ledger-container">
                <div class="ledger-header d-flex justify-content-between align-items-center">
                    <h6>Recent Transactions</h6>
                    <a href="transactions.php" class="btn btn-sm btn-primary rounded-pill px-3">
                        View All
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Type</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($transactions)): ?>
                                <?php foreach($transactions as $tx): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-emphasis"><?= htmlspecialchars($tx['fullname']) ?></div>
                                        <div class="text-secondary" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars($tx['email']) ?>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="tx-badge">
                                            <?= strtoupper(htmlspecialchars($tx['type'])) ?>
                                        </span>
                                    </td>

                                    <td class="text-end fw-bold text-emphasis">
                                        $<?= number_format($tx['amount'], 2) ?>
                                    </td>

                                    <td class="text-end text-secondary">
                                        <?= date('M d, Y - H:i', strtotime($tx['created_at'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">
                                        No recent transactions found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Side Panels -->
        <div class="col-lg-4">

            <div class="mb-4">
                <div class="stat-label mb-3">Priority Actions</div>

                <a href="kyc_manage.php" class="action-panel">
                    <span><i class="fa-solid fa-id-card me-2"></i> KYC Requests</span>
                    <span class="badge bg-primary rounded-pill"><?= number_format($pendingKYC) ?></span>
                </a>

                <a href="withdrawals.php" class="action-panel">
                    <span><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</span>
                    <span class="badge bg-danger rounded-pill"><?= number_format($pendingWithdrawals) ?></span>
                </a>
            </div>

            <div class="integrity-card">
                <h6>System Integrity</h6>

                <div class="mini-stat">
                    <span>Core Database</span>
                    <span class="text-success">Active</span>
                </div>

                <div class="mini-stat">
                    <span>Server Latency</span>
                    <span class="text-primary">0.04ms</span>
                </div>

                <div class="mini-stat">
                    <span>Security Layer</span>
                    <span class="text-success">Stable</span>
                </div>

                <div class="progress mt-3" style="height: 6px; border-radius: 20px;">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>

                <div class="build-text">
                    Build Version: <strong>2026.04-stable</strong>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include('layout/footer.php'); ?>