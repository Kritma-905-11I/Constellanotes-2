<?php
require_once 'config.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    json_out(['success' => false, 'message' => 'Not logged in.']);
}
$uid = $_SESSION['user_id'];
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

// GET — list all stars for user
if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM stars WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$uid]);
    json_out(['success' => true, 'stars' => $stmt->fetchAll()]);
}

// POST — create new star
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['star_name'] ?? '');
    $hue  = intval($data['color_hue'] ?? 270);
    if (!$name) {
        json_out(['success' => false, 'message' => 'Star name is required.']);
    }
    $stmt = $pdo->prepare('INSERT INTO stars (user_id, star_name, color_hue) VALUES (?,?,?)');
    $stmt->execute([$uid, $name, $hue]);
    $id = $pdo->lastInsertId();
    $stmt = $pdo->prepare('SELECT * FROM stars WHERE id = ?');
    $stmt->execute([$id]);
    json_out(['success' => true, 'star' => $stmt->fetch()]);
}

// DELETE — remove a star
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = intval($data['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM stars WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $uid]);
    json_out(['success' => true]);
}

json_out(['success' => false, 'message' => 'Method not allowed.']);