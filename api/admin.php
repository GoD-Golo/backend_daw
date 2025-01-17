<?php
require_once '../config/database.php'; // Include database configuration
require_once '../helpers/middleware.php'; // Include middleware for handling CORS

handleCors();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$response = [];

try {
    // Check 
    if (
        !isset($_COOKIE['user_id']) || 
        !isset($_COOKIE['user_role']) || 
        $_COOKIE['user_role'] !== 'admin'
    ) {
        http_response_code(403); // Forbidden
        throw new Exception('Access denied: You must be an admin to access this resource.');
    }

    $response = [
        'success' => true,
        'message' => 'Access granted: User is an admin.',
    ];
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
    ];
}

header('Content-Type: application/json');

echo json_encode($response);
?>