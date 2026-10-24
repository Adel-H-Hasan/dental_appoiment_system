<?php
$root = '';
require __DIR__ . '/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

if (isAdmin()) {
    setFlash('error', 'Admins cannot book appointments.');
    header("Location: index.php");
    exit;
}

$slotId = (int)($_POST['slot_id'] ?? 0);
$note = trim($_POST['note'] ?? '');
$userId = $_SESSION['user_id'];
$error = '';

$stmt = $pdo->prepare("SELECT * FROM slots WHERE id = ?");
$stmt->execute([$slotId]);
$slot = $stmt->fetch();

if (!$slot) {
    $error = "This slot does not exist.";
} elseif (slotStarted($slot['slot_date'], $slot['start_time'])) {
    $error = "You cannot book a slot in the past.";
} elseif (strlen($note) > 255) {
    $error = "The note is too long (255 characters max).";
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ? AND slot_id = ?");
    $stmt->execute([$userId, $slotId]);

    if ($stmt->fetchColumn() > 0) {
        $error = "You already have an appointment for this slot.";
    } elseif (activeCount($pdo, $slotId) >= $slot['capacity']) {
        $error = "This slot is full.";
    }
}

if ($error !== '') {
    setFlash('error', $error);
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("INSERT INTO appointments (user_id, slot_id, note, status) VALUES (?, ?, ?, 'pending')");
$stmt->execute([$userId, $slotId, $note === '' ? null : $note]);

setFlash('success', 'Appointment requested. Status: Pending.');
header("Location: my_appointments.php");
exit;
