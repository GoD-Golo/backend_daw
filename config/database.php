<?php
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '/../.env');  
$dotenv->load();

// var_dump(getenv('ENV_HOST'));
// var_dump(getenv('ENV_DBNAME'));
// var_dump(getenv('ENV_USER'));
// var_dump(getenv('ENV_PASSWORD'));

// $host = getenv('ENV_HOST');
// $dbname = getenv('ENV_DBNAME');
// $user = getenv('ENV_USER');
// $password = getenv('ENV_PASSWORD');

// $host = 'localhost';
// $dbname = 'hotel_daw';
// $user = 'root';
// $password = '3912';

$host = 'sql8.freemysqlhosting.net:3306';
$dbname = 'sql8758283';
$user = 'sql8758283';
$password = 'ttNtpsHIjr';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
