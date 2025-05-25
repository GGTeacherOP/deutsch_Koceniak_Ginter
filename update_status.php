<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aplikacji = intval($_POST['id_aplikacji']);
    $status = $_POST['status'];

    // Aktualizacja statusu w bazie danych
    $stmt = $conn->prepare("UPDATE aplikacje SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id_aplikacji);
    $stmt->execute();

    // Przekierowanie z powrotem do strony aplikacji
    header("Location: aplikacje.php?id=" . $_GET['id']);
    exit();
}
?>
