<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prozivod_id'])) {
    $id = intval($_POST['prozivod_id']);
    
    $stmt = $conn->prepare("DELETE FROM proizvod WHERE prozivod_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../admin/prikazKnjiga.php");
exit;