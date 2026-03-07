<?php
require_once 'config.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    json_out(['success' => false, 'message' => 'Not logged in.']);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['avatar'])) {
    json_out(['success' => false, 'message' => 'No file uploaded.']);
}

$uid  = $_SESSION['user_id'];
$file = $_FILES['avatar'];

// Validate
$allowed = ['image/jpeg','image/png','image/gif','image/webp'];
$finfo   = finfo_open(FILEINFO_MIME_TYPE);
$mime    = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed)) {
    json_out(['success' => false, 'message' => 'Only JPG, PNG, GIF and WebP allowed.']);
}
if ($file['size'] > 5 * 1024 * 1024) {
    json_out(['success' => false, 'message' => 'File too large (max 5MB).']);
}

// Save file
if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
$ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $uid . '_' . time() . '.' . $ext;
$dest     = UPLOAD_DIR . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    json_out(['success' => false, 'message' => 'Upload failed.']);
}

$url = UPLOAD_URL . $filename;

// Update DB
$pdo  = db();
$stmt = $pdo->prepare('UPDATE users SET avatar_url = ? WHERE id = ?');
$stmt->execute([$url, $uid]);
$_SESSION['avatar_url'] = $url;

json_out(['success' => true, 'avatar_url' => $url]);