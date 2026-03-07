<?php
require_once 'config.php';
header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) {
    json_out(['logged_in' => false]);
}
json_out([
    'logged_in'  => true,
    'first_name' => $_SESSION['first_name'],
    'last_name'  => $_SESSION['last_name'],
    'email'      => $_SESSION['email'],
    'avatar'     => $_SESSION['avatar_url'],
    'joined'     => $_SESSION['joined']
]);