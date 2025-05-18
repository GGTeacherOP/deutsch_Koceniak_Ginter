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
