<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request.']);
}

$em = strtolower(trim($_POST['email']    ?? ''));
$pw = $_POST['password'] ?? '';

if (!$em || !$pw) {
    json_out(['success' => false, 'message' => 'Email and password are required.']);
}

$pdo  = db();
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$em]);
$user = $stmt->fetch();

if (!$user || !password_verify($pw, $user['password'])) {
    json_out(['success' => false, 'message' => 'Incorrect email or password.']);
}

// Save session
$_SESSION['user_id']    = $user['id'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name']  = $user['last_name'];
$_SESSION['email']      = $user['email'];
$_SESSION['avatar_url'] = $user['avatar_url'];
$_SESSION['joined']     = $user['created_at'];

json_out([
    'success'    => true,
    'first_name' => $user['first_name'],
    'last_name'  => $user['last_name'],
    'email'      => $user['email'],
    'avatar'     => $user['avatar_url'],
    'joined'     => $user['created_at']
]);