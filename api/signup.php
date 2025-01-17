<?php
require_once '../config/database.php';
require '../vendor/autoload.php';
require_once '../helpers/middleware.php';

handleCors();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = [];

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['email'], $data['password'])) {
        throw new Exception('Required fields: name, email, password.');
    }

    // -------- TEST DATA --------
    // $data = [
    //     'name' => 'John Doe',
    //     'email' => 'djrextheripper2@gmail.com',
    //     'password' => 'password123'
    // ];

    $name = trim($data['name']);
    $email = trim($data['email']);
    $password = trim($data['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format.');
    }
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long.');
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Email is already registered.');
    }

    $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $hashedPassword]);

    $mail = new PHPMailer(true);

    try {
        // SMTP settings for Gmail with an App Password
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hotel.daw.proiect@gmail.com'; 
        $mail->Password   = 'gfaactzupbdwzexj';             
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->setFrom('hotel.daw.proiect@gmail.com', 'Hotel DAW');
        $mail->addAddress($email, $name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Our Platform!';
        $mail->Body    = "Hi $name,<br><br>
                          Thank you for registering on our platform. We're excited to have you on board!<br><br>
                          If you have any questions or need assistance, feel free to reach out to us.<br><br>
                          Best regards,<br>The Team";
        $mail->AltBody = "Hi $name,\n\nThank you for registering on our platform. We're excited to have you on board!\n\nIf you have any questions or need assistance, feel free to reach out to us.\n\nBest regards,\nThe Team";
        
        // Debugging
        //$mail->SMTPDebug = 4;
        //$mail->Debugoutput = 'echo';
        
        $mail->send();

        $response = [
            'success' => true,
            'message' => 'User registered successfully. Confirmation email sent.',
        ];
    } catch (Exception $e) {
        throw new Exception('User registered, but failed to send confirmation email: ' . $mail->ErrorInfo);
    }
} catch (Exception $e) {
    http_response_code(400);
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
    ];
}

echo json_encode($response);
?>
