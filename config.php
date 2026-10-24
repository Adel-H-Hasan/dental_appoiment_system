<?php
session_start();
date_default_timezone_set('Asia/Amman');

if (!isset($root)) {
    $root = '';
}

$host = 'localhost';
$dbname = 'dental_db';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed. Check config.php and make sure database.sql was imported.");
}

function e($text)
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function setFlash($type, $text)
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin()
{
    global $root;
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in first.');
        header("Location: " . $root . "login.php");
        exit;
    }
}

function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        die("Access denied. Admins only.");
    }
}

function slotStarted($date, $time)
{
    return strtotime($date . ' ' . $time) <= time();
}

function activeCount($pdo, $slotId)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE slot_id = ? AND status IN ('pending', 'confirmed')");
    $stmt->execute([$slotId]);
    return (int)$stmt->fetchColumn();
}

function shortTime($time)
{
    return substr($time, 0, 5);
}
