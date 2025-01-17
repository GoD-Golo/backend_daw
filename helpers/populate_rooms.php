<?php
require_once '../config/database.php';

try {
    $stmt = $conn->prepare("INSERT INTO rooms (floor, room_number, type, availability, breakfast, image) VALUES (:floor, :room_number, :type, 'available', :breakfast, :image)");

    // DHotel structure
    $total_floors = 11;
    $rooms_per_floor = 12;
    $premium_top_floor = 4;

    for ($floor = 1; $floor <= $total_floors; $floor++) {
        if ($floor === 1) {
            continue; // Skip first floor (lobby and restaurant)
        }

        $rooms = ($floor === $total_floors) ? $premium_top_floor : $rooms_per_floor;
        $type = ($floor === $total_floors) ? 'premium' : 'normal';

        for ($room_number = 1; $room_number <= $rooms; $room_number++) {
            $formatted_room_number = ($floor * 100) + $room_number; // 2 floor,  10 room nr -> 210
            $stmt->execute([
                ':floor' => $floor,
                ':room_number' => $formatted_room_number,
                ':type' => $type,
                ':breakfast' => 0, 
                ':image' => 'default-room.jpg', // Temporary image
            ]);
        }
    }

    echo "Rooms populated successfully!";
} catch (Exception $e) {
    echo "Error populating rooms: " . $e->getMessage();
}
?>
