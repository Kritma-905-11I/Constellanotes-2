<?php
// ================================================
// config.php — XAMPP credentials
// ================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // XAMPP default = blank, no password
define('DB_NAME', 'luminary_db');

define('UPLOAD_DIR', __DIR__ . '/../uploads/avatars/');
define('UPLOAD_URL', 'uploads/avatars/');

function db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
            exit;
        }
    }
    return $pdo;
}

function json_out($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

session_start();