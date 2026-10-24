<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Dental Clinic') ?></title>
    <link rel="stylesheet" href="<?= $root ?>style.css">
</head>
<body>
<nav>
    <a class="brand" href="<?= $root ?>index.php">Dental Clinic</a>
    <div class="links">
        <?php if (isAdmin()): ?>
            <a href="<?= $root ?>admin/index.php">Dashboard</a>
            <a href="<?= $root ?>admin/appointments.php">Appointments</a>
            <a href="<?= $root ?>admin/users.php">Users</a>
            <a href="<?= $root ?>admin/slots.php">Slots</a>
            <a href="<?= $root ?>logout.php">Logout</a>
        <?php elseif (isLoggedIn()): ?>
            <a href="<?= $root ?>index.php">Available Slots</a>
            <a href="<?= $root ?>my_appointments.php">My Appointments</a>
            <a href="<?= $root ?>logout.php">Logout (<?= e($_SESSION['name']) ?>)</a>
        <?php else: ?>
            <a href="<?= $root ?>index.php">Available Slots</a>
            <a href="<?= $root ?>login.php">Login</a>
            <a href="<?= $root ?>register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
<?php
if (isset($_SESSION['flash'])) {
    echo '<div class="alert ' . e($_SESSION['flash']['type']) . '">' . e($_SESSION['flash']['text']) . '</div>';
    unset($_SESSION['flash']);
}
?>
