<?php
$root = '';
require __DIR__ . '/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my_appointments.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT a.*, s.slot_date, s.start_time
                       FROM appointments a
                       JOIN slots s ON a.slot_id = s.id
                       WHERE a.id = ? AND a.user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$appointment = $stmt->fetch();

if (!$appointment) {
    setFlash('error', 'Appointment not found.');
} elseif (!in_array($appointment['status'], ['pending', 'confirmed'])) {
    setFlash('error', 'This appointment cannot be cancelled.');
} elseif (slotStarted($appointment['slot_date'], $appointment['start_time'])) {
    setFlash('error', 'You cannot cancel after the slot has started.');
} else {
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    setFlash('success', 'Appointment cancelled.');
}

header("Location: my_appointments.php");
exit;
