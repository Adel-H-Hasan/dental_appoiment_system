<?php
$root = '../';
require __DIR__ . '/../config.php';
requireAdmin();

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';

$sql = "SELECT a.*, u.name, u.email, s.slot_date, s.start_time, s.end_time
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN slots s ON a.slot_id = s.id
        WHERE 1 = 1";
$params = [];

if ($q !== '') {
    $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

if (in_array($status, ['pending', 'confirmed', 'rejected', 'cancelled'])) {
    $sql .= " AND a.status = ?";
    $params[] = $status;
} else {
    $status = '';
}

$sql .= " ORDER BY s.slot_date DESC, s.start_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

$title = 'Manage Appointments';
include __DIR__ . '/../header.php';
?>
<h1>Manage Appointments</h1>

<form method="get" class="filters">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search by name or email">
    <select name="status">
        <option value="">All statuses</option>
        <?php foreach (['pending', 'confirmed', 'rejected', 'cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Search</button>
    <a class="btn gray" href="appointments.php">Reset</a>
</form>

<div class="table-wrap">
<table>
    <tr>
        <th>Patient</th>
        <th>Email</th>
        <th>Date</th>
        <th>Time</th>
        <th>Note</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php if (empty($appointments)): ?>
        <tr><td colspan="7">No appointments found.</td></tr>
    <?php endif; ?>
    <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?= e($a['name']) ?></td>
            <td><?= e($a['email']) ?></td>
            <td><?= e($a['slot_date']) ?></td>
            <td><?= e(shortTime($a['start_time'])) ?> - <?= e(shortTime($a['end_time'])) ?></td>
            <td><?= e($a['note']) ?></td>
            <td><span class="status <?= e($a['status']) ?>"><?= e(ucfirst($a['status'])) ?></span></td>
            <td>
                <?php if ($a['status'] === 'pending'): ?>
                    <form method="post" action="appointment_action.php" class="inline-form">
                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                        <input type="hidden" name="action" value="confirm">
                        <button type="submit" class="green">Confirm</button>
                    </form>
                    <form method="post" action="appointment_action.php" class="inline-form">
                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="red">Reject</button>
                    </form>
                <?php endif; ?>
                <?php if (in_array($a['status'], ['pending', 'confirmed'])): ?>
                    <form method="post" action="appointment_action.php" class="inline-form" onsubmit="return confirm('Cancel this appointment?');">
                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="gray">Cancel</button>
                    </form>
                <?php endif; ?>
                <?php if (!in_array($a['status'], ['pending', 'confirmed'])): ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
