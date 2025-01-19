<?php
require_once '../config/database.php'; 
require_once '../helpers/middleware.php';

handleCors();

$response = [];

try {
    // Read and decode the incoming JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['email']) || empty($data['password'])) {
        throw new Exception('Required fields: email, password.');
    }

    $email = trim($data['email']);
    $password = trim($data['password']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format.');
    }

    // Query the database for the user
    $stmt = $conn->prepare('SELECT id, name, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password is valid
    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception('Invalid email or password.');
    }

    // Set session data in cookies
    $cookieOptions = [
        'expires' => time() + 86400, // 1 day
        'path' => '/',
        'domain' => '.shark-app-twz3c.ondigitalocean.app', // Adjust to your domain (no leading dot)
        'secure' => true, // Set to true in production (requires HTTPS)
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'None', // Adjust based on your needs ('Strict', 'Lax', or 'None')
    ];

    setcookie('user_id', $user['id'], $cookieOptions);
    setcookie('user_role', $user['role'], $cookieOptions);
    setcookie('user_name', $user['name'], $cookieOptions);

    // Return success response
    $response = [
        'success' => true,
        'message' => 'Sign-in successful.',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $email,
        ],
    ];
} catch (Exception $e) {
    // Handle errors and return appropriate response
    http_response_code(400);
    error_log('Error during sign-in: ' . $e->getMessage()); // Log the error for debugging
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
    ];
}

// Ensure JSON response
header('Content-Type: application/json');
echo json_encode($response);
