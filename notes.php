<?php
require_once 'config.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    json_out(['success' => false, 'message' => 'Not logged in.']);
}
$uid    = $_SESSION['user_id'];
$pdo    = db();
$method = $_SERVER['REQUEST_METHOD'];

// GET — list all notes for a star
if ($method === 'GET') {
    $star_id = intval($_GET['star_id'] ?? 0);
    if (!$star_id) json_out(['success' => false, 'message' => 'star_id required.']);
    // Verify star belongs to user
    $s = $pdo->prepare('SELECT id FROM stars WHERE id = ? AND user_id = ?');
    $s->execute([$star_id, $uid]);
    if (!$s->fetch()) json_out(['success' => false, 'message' => 'Star not found.']);
    $stmt = $pdo->prepare('SELECT * FROM notes WHERE star_id = ? AND user_id = ? ORDER BY updated_at DESC');
    $stmt->execute([$star_id, $uid]);
    json_out(['success' => true, 'notes' => $stmt->fetchAll()]);
}

// POST — create note
if ($method === 'POST') {
    $data    = json_decode(file_get_contents('php://input'), true);
    $star_id = intval($data['star_id'] ?? 0);
    $title   = trim($data['title']    ?? 'Untitled');
    $body    = $data['body']           ?? '';
    $hue     = intval($data['color_hue'] ?? 270);
    if (!$star_id) json_out(['success' => false, 'message' => 'star_id required.']);
    $stmt = $pdo->prepare('INSERT INTO notes (star_id, user_id, title, body, color_hue) VALUES (?,?,?,?,?)');
    $stmt->execute([$star_id, $uid, $title, $body, $hue]);
    $id   = $pdo->lastInsertId();
    $stmt = $pdo->prepare('SELECT * FROM notes WHERE id = ?');
    $stmt->execute([$id]);
    json_out(['success' => true, 'note' => $stmt->fetch()]);
}

// PUT — update note
if ($method === 'PUT') {
    $data  = json_decode(file_get_contents('php://input'), true);
    $id    = intval($data['id']        ?? 0);
    $title = trim($data['title']       ?? 'Untitled');
    $body  = $data['body']              ?? '';
    $hue   = intval($data['color_hue'] ?? 270);
    if (!$id) json_out(['success' => false, 'message' => 'Note id required.']);
    $stmt = $pdo->prepare('UPDATE notes SET title=?, body=?, color_hue=?, updated_at=NOW() WHERE id=? AND user_id=?');
    $stmt->execute([$title, $body, $hue, $id, $uid]);
    json_out(['success' => true]);
}

// DELETE — delete note
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = intval($data['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM notes WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $uid]);
    json_out(['success' => true]);
}

json_out(['success' => false, 'message' => 'Method not allowed.']);