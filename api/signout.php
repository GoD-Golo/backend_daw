<?php
require_once '../helpers/middleware.php';

handleCors();

$_SESSION = [];

session_destroy();

// Clear cookies
$cookieOptions = [
    'expires' => time() + 86400, // 1 day
    'path' => '/',
    'domain' => '.shark-app-twz3c.ondigitalocean.app', 
    'secure' => true, 
    'httponly' => true, 
    'samesite' => 'None',
];

setcookie('user_id', '', $cookieOptions);
setcookie('user_role', '', $cookieOptions);
setcookie('user_name', '', $cookieOptions);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'User has been signed out successfully.'
]);
