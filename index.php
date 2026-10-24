<?php
$root = '';
require __DIR__ . '/config.php';

$date = $_GET['date'] ?? '';
$now = date('Y-m-d H:i:s');

$sql = "SELECT s.*,
        s.capacity - (SELECT COUNT(*) FROM appointments a WHERE a.slot_id = s.id AND a.status IN ('pending', 'confirmed')) AS remaining
        FROM slots s
        WHERE CONCAT(s.slot_date, ' ', s.start_time) > ?";
$params = [$now];

if ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $sql .= " AND s.slot_date = ?";
    $params[] = $date;
} else {
    $date = '';
}

$sql .= " ORDER BY s.slot_date, s.start_time";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$slots = $stmt->fetchAll();

$title = 'Available Slots';
include __DIR__ . '/header.php';
?>
<h1>Available Appointment Slots</h1>

<form method="get" class="filters">
    <input type="date" name="date" value="<?= e($date) ?>">
    <button type="submit">Filter by date</button>
    <a class="btn gray" href="index.php">Show all</a>
</form>

<div class="table-wrap">
<table>
    <tr>
        <th>Date</th>
        <th>Start</th>
        <th>End</th>
        <th>Remaining</th>
        <th>Book</th>
    </tr>
    <?php if (empty($slots)): ?>
        <tr><td colspan="5">No available slots.</td></tr>
    <?php endif; ?>
    <?php foreach ($slots as $slot): ?>
        <tr>
            <td><?= e($slot['slot_date']) ?></td>
            <td><?= e(shortTime($slot['start_time'])) ?></td>
            <td><?= e(shortTime($slot['end_time'])) ?></td>
            <td><?= (int)$slot['remaining'] ?> / <?= (int)$slot['capacity'] ?></td>
            <td>
                <?php if ($slot['remaining'] <= 0): ?>
                    Full
                <?php elseif (isAdmin()): ?>
                    -
                <?php elseif (isLoggedIn()): ?>
                    <form method="post" action="book.php" class="inline-form">
                        <input type="hidden" name="slot_id" value="<?= (int)$slot['id'] ?>">
                        <input type="text" name="note" maxlength="255" placeholder="Note (optional)">
                        <button type="submit">Book</button>
                    </form>
                <?php else: ?>
                    <a href="login.php">Login to book</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/footer.php'; ?>
