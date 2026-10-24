<?php
$root = '';
require __DIR__ . '/config.php';
requireLogin();

if (isAdmin()) {
    header("Location: admin/appointments.php");
    exit;
}

$stmt = $pdo->prepare("SELECT a.*, s.slot_date, s.start_time, s.end_time
                       FROM appointments a
                       JOIN slots s ON a.slot_id = s.id
                       WHERE a.user_id = ?
                       ORDER BY s.slot_date DESC, s.start_time DESC");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();

$title = 'My Appointments';
include __DIR__ . '/header.php';
?>
<h1>My Appointments</h1>

<div class="table-wrap">
<table>
    <tr>
        <th>Date</th>
        <th>Time</th>
        <th>Note</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php if (empty($appointments)): ?>
        <tr><td colspan="5">You have no appointments yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?= e($a['slot_date']) ?></td>
            <td><?= e(shortTime($a['start_time'])) ?> - <?= e(shortTime($a['end_time'])) ?></td>
            <td><?= e($a['note']) ?></td>
            <td><span class="status <?= e($a['status']) ?>"><?= e(ucfirst($a['status'])) ?></span></td>
            <td>
                <?php if (in_array($a['status'], ['pending', 'confirmed']) && !slotStarted($a['slot_date'], $a['start_time'])): ?>
                    <form method="post" action="cancel.php" class="inline-form" onsubmit="return confirm('Cancel this appointment?');">
                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                        <button type="submit" class="red">Cancel</button>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/footer.php'; ?>
