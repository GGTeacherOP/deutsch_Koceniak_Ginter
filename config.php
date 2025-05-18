<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portalpracy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
ini_set('upload_max_filesize', '2M');
ini_set('post_max_size', '4M');
?>
<?php
// config.php
session_start();

$host = 'localhost';
$dbname = 'portal_pracy';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ustawienie domyślnego czasu strefy czasowej
    date_default_timezone_set('Europe/Warsaw');
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Funkcja do sprawdzania czy użytkownik jest zalogowany
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Funkcja do sprawdzania roli użytkownika
function checkRole($requiredRole) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
        header("Location: brak_dostepu.php");
        exit();
    }
}
?>