<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include('layout/header.php');

$msg = $_GET['msg'] ?? null;

// --- ACTION HANDLERS ---

// 1. Single User Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: users_list.php?msg=User deleted successfully");
    exit();
}

// 2. Bulk Deletion
if (isset($_POST['bulk_delete']) && !empty($_POST['user_ids'])) {
    $ids = array_map('intval', $_POST['user_ids']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    header("Location: users_list.php?msg=Selected users deleted successfully");
    exit();
}

// --- DATA RETRIEVAL ---

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM users";
$params = [];

if ($search) {
    $sql .= " WHERE fullname LIKE ? OR email LIKE ? OR trader_account LIKE ? OR username LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}

$stmt = $pdo->prepare($sql . " ORDER BY created_at DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<style>
    :root {
        --radius-xl: 18px;
        --radius-md: 12px;
        --shadow-soft: 0 10px 25px rgba(0,0,0,0.08);
        --shadow-hover: 0 14px 35px rgba(0,0,0,0.12);
    }

    .page-header {
        padding: 1.4rem 1.5rem;
        border-radius: var(--radius-xl);
        background: linear-gradient(135deg, var(--bs-tertiary-bg), var(--bs-secondary-bg));
        border: 1px solid var(--bs-border-color);
        box-shadow: var(--shadow-soft);
    }

    .page-header h4 {
        font-weight: 900;
        margin-bottom: 0.25rem;
        letter-spacing: -0.5px;
    }

    .page-header p {
        margin-bottom: 0;
        color: var(--bs-secondary-color);
        font-size: 0.85rem;
    }

    .toolbar-card {
        padding: 1.1rem 1.2rem;
        border-radius: var(--radius-xl);
        background: var(--bs-tertiary-bg);
        border: 1px solid var(--bs-border-color);
        box-shadow: var(--shadow-soft);
    }

    .ledger-container {
        border-radius: var(--radius-xl);
        background: var(--bs-tertiary-bg);
        border: 1px solid var(--bs-border-color);
        overflow: hidden;
        box-shadow: var(--shadow-soft);
    }

    .table thead th {
        background: var(--bs-secondary-bg);
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 900;
        padding: 1rem 1.25rem;
        color: var(--bs-secondary-color);
        border-bottom: 1px solid var(--bs-border-color);
    }

    .table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--bs-border-color);
        font-size: 0.84rem;
    }

    .table tbody tr:hover {
        background: rgba(13, 110, 253, 0.06);
        transition: 0.15s;
    }

    .user-rank {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        padding: 0.35em 0.75em;
        border-radius: 50px;
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        color: var(--bs-secondary-color);
    }

    .status-pill {
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.08em;
        padding: 0.45em 0.9em;
        border-radius: 50px;
        border: 1px solid transparent;
        display: inline-block;
    }

    .status-active {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border-color: rgba(25, 135, 84, 0.25);
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.12);
        color: #ffc107;
        border-color: rgba(255, 193, 7, 0.25);
    }

    .action-btn {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-secondary-bg);
        text-decoration: none;
        transition: 0.2s;
        box-shadow: 0 6px 14px rgba(0,0,0,0.08);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        border-color: var(--bs-primary-border-subtle);
    }

    .action-btn i {
        font-size: 0.9rem;
    }

    .search-input {
        border-radius: 50px;
        padding-left: 2.6rem;
        font-weight: 600;
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.7;
    }

</style>


<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="page-header mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h4>Investor Ledger</h4>
            <p>Managing <strong><?= count($users) ?></strong> registered accounts.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="user_add.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="fa-solid fa-user-plus me-2"></i> Add New User
            </a>
        </div>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="toolbar-card mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-lg-6">
                <form method="GET" action="users_list.php" class="position-relative">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search"
                        class="form-control search-input bg-body border shadow-none"
                        placeholder="Search by name, email, username, account ID..."
                        value="<?= htmlspecialchars($search) ?>">
                </form>
            </div>

            <div class="col-lg-6 text-lg-end">
                <button form="bulkDeleteForm" type="submit" name="bulk_delete"
                    class="btn btn-outline-danger rounded-pill px-4 fw-bold"
                    onclick="return confirm('This will permanently delete selected accounts. Proceed?')">
                    <i class="fa-solid fa-trash-can me-2"></i> Bulk Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <form method="POST" action="users_list.php<?= $search ? '?search=' . urlencode($search) : '' ?>" id="bulkDeleteForm">
        <div class="ledger-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>User Profile</th>
                            <th>Status</th>
                            <th>Portfolio</th>
                            <th>Account ID</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    <i class="fa-solid fa-user-slash me-2"></i>
                                    No users found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($users as $user): 
                                $total_liquidity = ($user['account_balance'] ?? 0) 
                                                + ($user['investment_balance'] ?? 0) 
                                                + ($user['profit_balance'] ?? 0) 
                                                + ($user['trading_balance'] ?? 0);

                                $statusClass = ($user['status'] == 'active') ? 'status-active' : 'status-pending';
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="user_ids[]" value="<?= $user['id'] ?>" class="form-check-input user-checkbox">
                                </td>

                                <td>
                                    <div class="fw-bold text-emphasis"><?= htmlspecialchars($user['fullname']) ?></div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                        <span class="text-secondary small"><?= htmlspecialchars($user['email']) ?></span>

                                        <?php if(!empty($user['rank'])): ?>
                                            <span class="user-rank"><?= strtoupper(htmlspecialchars($user['rank'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="status-pill <?= $statusClass ?>">
                                        <?= strtoupper(htmlspecialchars($user['status'])) ?>
                                    </span>
                                </td>

                                <td class="fw-bold text-emphasis">
                                    <small class="text-secondary me-1"><?= htmlspecialchars($user['currency'] ?? '$') ?></small>
                                    <?= number_format($total_liquidity, 2) ?>
                                </td>

                                <td>
                                    <code class="fw-bold text-primary">
                                        <?= !empty($user['trader_account']) ? htmlspecialchars($user['trader_account']) : 'UNASSIGNED' ?>
                                    </code>
                                </td>

                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="login_as_user.php?id=<?= $user['id'] ?>" target="_blank"
                                           class="action-btn text-info" title="Login as User">
                                            <i class="fa-solid fa-right-to-bracket"></i>
                                        </a>

                                        <a href="user_edit.php?id=<?= $user['id'] ?>"
                                           class="action-btn text-primary" title="Edit User">
                                            <i class="fa-solid fa-user-gear"></i>
                                        </a>

                                        <a href="users_list.php?delete_id=<?= $user['id'] ?>"
                                           class="action-btn text-danger"
                                           onclick="return confirm('Are you sure you want to delete this user?')"
                                           title="Delete User">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </form>

</div>

<script>
    // Toggle all checkboxes
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>

<?php include('layout/footer.php'); ?>