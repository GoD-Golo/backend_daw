<?php
function authenticate($role = null) {
    session_start();

    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized']);
        exit;
    }

    if ($role && $_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo json_encode(['message' => 'Forbidden']);
        exit;
    }
}
?>
