<?php
require_once __DIR__ . '/includes/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: /index.php'); exit; }

$db       = get_db();
$accounts = $db->query(
    "SELECT a.*, u.full_name AS holder_name
     FROM accounts a
     LEFT JOIN users u ON a.user_id = u.id
     ORDER BY a.id"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts - Union Bank of India Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <div class="app-container">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        <main class="main-panel">
            <div class="page-header">
                <h2>Account Management</h2>
                <p>View and manage all bank accounts</p>
            </div>
            <div class="content-card">
                <div class="card-header">
                    <h3>All Accounts</h3>
                    <div>
                        <input type="text" id="searchBox" placeholder="Search accounts..."
                               style="padding:8px 12px;border:1px solid #dde3ed;border-radius:6px;font-size:13px;">
                        <button onclick="searchAPI()" class="btn-primary" style="padding:8px 16px;font-size:12px;">Search via API</button>
                    </div>
                </div>
                <div id="apiResult" style="display:none;padding:10px 20px;"></div>
                <table class="data-table">
                    <thead>
                        <tr><th>Account #</th><th>Holder</th><th>Type</th><th>Balance</th><th>Branch</th><th>IFSC</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($accounts as $acc): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($acc['account_number']); ?></code></td>
                        <td><?php echo htmlspecialchars($acc['account_holder']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars(str_replace('_', ' ', $acc['account_type']))); ?></td>
                        <td class="amount">&#8377;<?php echo number_format((float)$acc['balance'], 2); ?></td>
                        <td><?php echo htmlspecialchars($acc['branch_code']); ?></td>
                        <td><code><?php echo htmlspecialchars($acc['ifsc_code']); ?></code></td>
                        <td><span class="badge badge-<?php echo htmlspecialchars($acc['status']); ?>"><?php echo ucfirst(htmlspecialchars($acc['status'])); ?></span></td>
                        <td><a href="#" onclick="fetchAccountAPI(<?php echo (int)$acc['id']; ?>)" class="btn-link">View Details</a></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script>
    function fetchAccountAPI(id) {
        fetch('/api/accounts.php?api=get_user&acc_no=' + id)
            .then(r => r.json())
            .then(d => {
                var el = document.getElementById('apiResult');
                el.style.display = 'block';
                el.innerHTML = '<pre style="background:#f0f2f5;padding:10px;border-radius:6px;font-size:11px;overflow:auto;">'
                             + JSON.stringify(d, null, 2) + '</pre>';
            });
    }
    function searchAPI() {
        var q = document.getElementById('searchBox').value;
        fetch('/api/accounts.php?api=search&q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(d => {
                var el = document.getElementById('apiResult');
                el.style.display = 'block';
                el.innerHTML = '<pre style="background:#f0f2f5;padding:10px;border-radius:6px;font-size:11px;overflow:auto;">'
                             + JSON.stringify(d, null, 2) + '</pre>';
            });
    }
    </script>
</body>
</html>
