<?php
require_once '../config/database.php';
require_once '../helpers/middleware.php';

handleCors();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parse_str(file_get_contents("php://input"), $data);

    $room_number = $data['room_number'];
    $room_type = $data['type'];
    $price = $data['price'];
    $availability = $data['availability'];
    $floor = $data['room_number']%100;

    if (empty($room_number) || empty($room_type) || empty($price) || empty($availability)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }


    $query = "INSERT INTO rooms (room_number, type, price, availability,floor) VALUES (:room_number, :type, :price, :availability,:floor)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':room_number', $room_number, PDO::PARAM_STR);
    $stmt->bindParam(':type', $room_type, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':availability', $availability, PDO::PARAM_STR);
    $stmt->bindParam(':floor', $floor, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Room added successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add room."]);
    }
    exit;
}


// delete XD
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $room_id = $data['room_id'];

    $query = "DELETE FROM rooms WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam("id", $room_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Room deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete room."]);
    }
    exit;
}

// update
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);

    $room_id = $data['room_id'];
    $type = $data['type'];
    $price = $data['price'];
    $availability = $data['availability'];

    $query = "UPDATE rooms SET type = :type, price = :price, availability = :availability WHERE id = :id";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':availability', $availability, PDO::PARAM_STR);
    $stmt->bindParam(':id', $room_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Room updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update room."]);
    }
    exit;
}



// get all
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM rooms";
    $stmt = $conn->query($query);

    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "rooms" => $rooms]);
    exit;
}

?>
