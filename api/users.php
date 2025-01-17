<?php
require_once '../config/database.php';
require_once '../helpers/middleware.php';

handleCors();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $user_id = $data['user_id'];

    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete user."]);
    }
    exit;
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);

    $user_id = $data['user_id'];
    $name = $data['name'];
    $email = $data['email'];
    $role = $data['role'];

    if (empty($user_id) || empty($name) || empty($email) || empty($role)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $query = "UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update user."]);
    }
    exit;
}

// add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parse_str(file_get_contents("php://input"), $data);

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User added successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add user."]);
    }
    exit;
}

// get a user's bookings or all users
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    parse_str(file_get_contents("php://input"), $data);
    if (isset($_GET['get_bookings'])) {
        $query = "SELECT users.name, bookings.* FROM users 
                  JOIN bookings ON users.id = bookings.user_id";
        $stmt = $conn->query($query);

        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "bookings" => $bookings]);
    } elseif (isset($_GET['get_users'])) {
        $query = "SELECT * FROM users";
        $stmt = $conn->query($query);

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "users" => $users]);
    }
    exit;
}

?>
