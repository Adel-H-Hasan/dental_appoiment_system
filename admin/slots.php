<?php
$root = '../';
require __DIR__ . '/../config.php';
requireAdmin();

function validateSlot($date, $start, $end, $capacity)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        return "Please enter a valid date.";
    }

    $s = DateTime::createFromFormat('H:i', $start);
    $e = DateTime::createFromFormat('H:i', $end);
    if (!$s || !$e) {
        return "Please enter valid start and end times.";
    }

    if ($end <= $start) {
        return "End time must be after start time.";
    }

    if ($capacity < 1 || $capacity > 50) {
        return "Capacity must be between 1 and 50.";
    }

    if (strtotime($date . ' ' . $start) <= time()) {
        return "The slot must start in the future.";
    }

    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $date = trim($_POST['slot_date'] ?? '');
    $start = trim($_POST['start_time'] ?? '');
    $end = trim($_POST['end_time'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);

    if ($action === 'add') {
        $error = validateSlot($date, $start, $end, $capacity);
        if ($error !== '') {
            setFlash('error', $error);
        } else {
            $stmt = $pdo->prepare("INSERT INTO slots (slot_date, start_time, end_time, capacity) VALUES (?, ?, ?, ?)");
            $stmt->execute([$date, $start, $end, $capacity]);
            setFlash('success', 'Slot added.');
        }
        header("Location: slots.php");
        exit;
    }

    if ($action === 'update') {
        $error = validateSlot($date, $start, $end, $capacity);
        if ($error === '' && $capacity < activeCount($pdo, $id)) {
            $error = "Capacity cannot be lower than the number of active appointments.";
        }
        if ($error !== '') {
            setFlash('error', $error);
            header("Location: slots.php?edit=" . $id);
        } else {
            $stmt = $pdo->prepare("UPDATE slots SET slot_date = ?, start_time = ?, end_time = ?, capacity = ? WHERE id = ?");
            $stmt->execute([$date, $start, $end, $capacity, $id]);
            setFlash('success', 'Slot updated.');
            header("Location: slots.php");
        }
        exit;
    }

    if ($action === 'delete') {
        if (activeCount($pdo, $id) > 0) {
            setFlash('error', 'This slot has pending or confirmed appointments and cannot be deleted.');
        } else {
            $stmt = $pdo->prepare("DELETE FROM slots WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Slot deleted.');
        }
        header("Location: slots.php");
        exit;
    }

    header("Location: slots.php");
    exit;
}

$editSlot = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM slots WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editSlot = $stmt->fetch();
}

$slots = $pdo->query("SELECT s.*,
        (SELECT COUNT(*) FROM appointments a WHERE a.slot_id = s.id AND a.status IN ('pending', 'confirmed')) AS active
        FROM slots s ORDER BY s.slot_date DESC, s.start_time DESC")->fetchAll();

$title = 'Manage Slots';
include __DIR__ . '/../header.php';
?>
<h1>Manage Time Slots</h1>

<div class="card">
    <h2><?= $editSlot ? 'Edit Slot' : 'Add Slot' ?></h2>
    <form method="post" class="filters">
        <input type="hidden" name="action" value="<?= $editSlot ? 'update' : 'add' ?>">
        <?php if ($editSlot): ?>
            <input type="hidden" name="id" value="<?= (int)$editSlot['id'] ?>">
        <?php endif; ?>
        <input type="date" name="slot_date" value="<?= e($editSlot['slot_date'] ?? '') ?>">
        <input type="time" name="start_time" value="<?= e($editSlot ? shortTime($editSlot['start_time']) : '') ?>">
        <input type="time" name="end_time" value="<?= e($editSlot ? shortTime($editSlot['end_time']) : '') ?>">
        <input type="number" name="capacity" min="1" max="50" placeholder="Capacity" value="<?= e($editSlot['capacity'] ?? '') ?>">
        <button type="submit"><?= $editSlot ? 'Update' : 'Add' ?></button>
        <?php if ($editSlot): ?>
            <a class="btn gray" href="slots.php">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-wrap">
<table>
    <tr>
        <th>Date</th>
        <th>Start</th>
        <th>End</th>
        <th>Capacity</th>
        <th>Active Appointments</th>
        <th>Actions</th>
    </tr>
    <?php if (empty($slots)): ?>
        <tr><td colspan="6">No slots yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($slots as $s): ?>
        <tr>
            <td><?= e($s['slot_date']) ?></td>
            <td><?= e(shortTime($s['start_time'])) ?></td>
            <td><?= e(shortTime($s['end_time'])) ?></td>
            <td><?= (int)$s['capacity'] ?></td>
            <td><?= (int)$s['active'] ?></td>
            <td>
                <a class="btn" href="slots.php?edit=<?= (int)$s['id'] ?>">Edit</a>
                <form method="post" class="inline-form" onsubmit="return confirm('Delete this slot?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                    <button type="submit" class="red">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
