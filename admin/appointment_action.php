<?php
$root = '../';
require __DIR__ . '/../config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: appointments.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

$rules = [
    'confirm' => ['from' => ['pending'], 'to' => 'confirmed'],
    'reject' => ['from' => ['pending'], 'to' => 'rejected'],
    'cancel' => ['from' => ['pending', 'confirmed'], 'to' => 'cancelled'],
];

$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->execute([$id]);
$appointment = $stmt->fetch();

if (!$appointment) {
    setFlash('error', 'Appointment not found.');
} elseif (!isset($rules[$action])) {
    setFlash('error', 'Invalid action.');
} elseif (!in_array($appointment['status'], $rules[$action]['from'])) {
    setFlash('error', 'This status change is not allowed.');
} else {
    $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->execute([$rules[$action]['to'], $id]);
    setFlash('success', 'Appointment ' . $rules[$action]['to'] . '.');
}

header("Location: appointments.php");
exit;
