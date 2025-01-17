<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../helpers/middleware.php';

handleCors();


// get booking
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM bookings";
    $stmt = $conn->query($query);

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "bookings" => $bookings]);
    exit;
}

// create booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parse_str(file_get_contents("php://input"), $data);

    $room_id = $data['room_id'];
    $user_id = $data['user_id'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $status = $data['status'];


    if (empty($room_id) || empty($user_id) || empty($start_date) || empty($end_date)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input: Missing required fields']);
        exit;
    }

    
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, start_date, end_date, status) VALUES (:user_id, :room_id, :start_date, :end_date, :status)");
                        
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        if($stmt->execute()) {
            echo json_encode(["success"=>true,'message' => 'Booking created successfully']);
        } else {
            echo json_encode(["success"=>false ,'message' => 'Failed to create booking']);
        }
}

// update booking
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str(file_get_contents("php://input"), $data);

        $id = $data['booking_id'];
        $user_id = $data['user_id'];
        $room_id = $data['room_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $status = $data['status'];


        $query = "UPDATE bookings SET user_id = :user_id,  room_id = :room_id,  start_date = :start_date, end_date = :end_date,status = :status WHERE id = :id";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    
        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
        }
    
        exit;
    }
    
// delete a booking
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $booking_id = $data['booking_id'];

    $query = "DELETE FROM bookings WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam("id", $booking_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Booking deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete booking."]);
    }
    exit;
}

// Handle GET requests for a specific user's bookings
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    try {
        $stmt = $conn->prepare("SELECT bookings.*, rooms.room_number, rooms.floor 
                                FROM bookings 
                                JOIN rooms ON bookings.room_id = rooms.id 
                                WHERE bookings.user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $userBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($userBookings) {
            echo json_encode($userBookings);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No bookings found for this user']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to retrieve user bookings', 'error' => $e->getMessage()]);
    }
}

?>
