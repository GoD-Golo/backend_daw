<?php
require_once '../helpers/middleware.php';

handleCors();

$_SESSION = [];

session_destroy();

// Clear cookies
$cookieOptions = [
    'expires' => time() + 86400, // 1 day
    'path' => '/',
    'domain' => '.localhost', // Adjust to your domain (no leading dot)
    'secure' => false, // Set to true in production (requires HTTPS)
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict', // Adjust based on your needs ('Strict', 'Lax', or 'None')
];

setcookie('user_id', '', $cookieOptions);
setcookie('user_role', '', $cookieOptions);
setcookie('user_name', '', $cookieOptions);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'User has been signed out successfully.'
]);
