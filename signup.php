<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request.']);
}

$fn   = trim($_POST['first_name'] ?? '');
$ln   = trim($_POST['last_name']  ?? '');
$dob  = trim($_POST['dob']        ?? '');
$em   = strtolower(trim($_POST['email']    ?? ''));
$pw   = $_POST['password']         ?? '';
$pw2  = $_POST['confirm_password'] ?? '';

// Validate
if (!$fn || !$ln || !$dob || !$em || !$pw || !$pw2) {
    json_out(['success' => false, 'message' => 'All fields are required.']);
}
if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
    json_out(['success' => false, 'message' => 'Invalid email address.']);
}
if (strlen($pw) < 8) {
    json_out(['success' => false, 'message' => 'Password must be at least 8 characters.']);
}
if ($pw !== $pw2) {
    json_out(['success' => false, 'message' => 'Passwords do not match.']);
}

$pdo = db();

// Check existing email
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$em]);
if ($stmt->fetch()) {
    json_out(['success' => false, 'message' => 'An account with that email already exists.']);
}

// Insert
$hash = password_hash($pw, PASSWORD_BCRYPT);
$stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, dob, email, password) VALUES (?,?,?,?,?)');
$stmt->execute([$fn, $ln, $dob, $em, $hash]);
$userId = $pdo->lastInsertId();

// Save session
$_SESSION['user_id']    = $userId;
$_SESSION['first_name'] = $fn;
$_SESSION['last_name']  = $ln;
$_SESSION['email']      = $em;
$_SESSION['avatar_url'] = null;
$_SESSION['joined']     = date('c');

json_out([
    'success'    => true,
    'first_name' => $fn,
    'last_name'  => $ln,
    'email'      => $em,
    'avatar'     => null,
    'joined'     => $_SESSION['joined']
]);