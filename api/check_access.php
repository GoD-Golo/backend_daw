<?php
require_once '../helpers/middleware.php';

handleCors();

$userId = getCookie('user_id');
$userRole = getCookie('user_role');
$userName = getCookie('user_name');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// error_log('Cookies in check_access.php: ' . print_r($_COOKIE, true));


$response = [];

// Check if required cookies are set
if ($userId && in_array($userRole, ['admin', 'client'], true)) {
    // Valid cookies, construct response
    $response = [
        'success' => true,
        'user' => [
            'id' => $userId,
            'role' => $userRole,
            'name' => $userName,
        ],
    ];
} else {
    $response = [
        'success' => false,
        'message' => empty($userId) || empty($userRole) || empty($userName) 
            ? 'Unauthorized: User not logged in.' 
            : 'Invalid user data. Please log in again.',
    ];
}

// Set the response headers for JSON and return the response
header('Content-Type: application/json');
echo json_encode($response);
