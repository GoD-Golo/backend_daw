<?php

function handleCors() {
    // header('Access-Control-Allow-Origin: http://localhost:3001');
    // header('Access-Control-Allow-Origin: https://frontend-daw.vercel.app');
    header('Access-Control-Allow-Origin: https://frontend-daw.vercel.app');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Add other methods if needed
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204); // No Content
        exit;
    }
}

session_start();

// gets 'em cookies
function getCookie(string $key, ?string $default = null): ?string {
    if (isset($_COOKIE[$key])) {
        return htmlspecialchars($_COOKIE[$key], ENT_QUOTES, 'UTF-8');
    }
    return $default;
}

// Check log in?
function checkAccess(array $allowedRoles = []) {
    if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized: Please log in to access this resource.',
        ]);
        exit;
    }

    $userRole = $_SESSION['role'];

    // Check role
    if (!in_array($userRole, $allowedRoles)) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Forbidden: You do not have permission to access this resource.',
        ]);
        exit;
    }
}

// set session
function setUserSession($userId, $role) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['role'] = $role;
}

// logoutUser function
function logoutUser() {
    session_destroy();
}

