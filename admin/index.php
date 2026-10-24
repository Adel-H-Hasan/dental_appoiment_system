<?php
$root = '../';
require __DIR__ . '/../config.php';
requireAdmin();

$total = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
$confirmed = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'confirmed'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'rejected'")->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'cancelled'")->fetchColumn();
$users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

$title = 'Admin Dashboard';
include __DIR__ . '/../header.php';
?>
<h1>Admin Dashboard</h1>

<div class="stats">
    <div class="stat"><div class="number"><?= (int)$total ?></div>Total Appointments</div>
    <div class="stat"><div class="number"><?= (int)$pending ?></div>Pending</div>
    <div class="stat"><div class="number"><?= (int)$confirmed ?></div>Confirmed</div>
    <div class="stat"><div class="number"><?= (int)$users ?></div>Registered Users</div>
</div>

<div class="card">
    <h2>Appointments by Status</h2>
    <div class="chart-box">
        <canvas id="statusChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Confirmed', 'Rejected', 'Cancelled'],
            datasets: [{
                data: [<?= (int)$pending ?>, <?= (int)$confirmed ?>, <?= (int)$rejected ?>, <?= (int)$cancelled ?>],
                backgroundColor: ['#e69b00', '#2e8b57', '#c0392b', '#777777']
            }]
        }
    });
</script>
<?php include __DIR__ . '/../footer.php'; ?>
