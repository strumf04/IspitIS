<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['korpa_id'])) {
    $korpa_id = intval($_POST['korpa_id']);

    $stmt = $conn->prepare("DELETE FROM korpa WHERE korpa_id = ?");
    $stmt->bind_param("i", $korpa_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../korpa.php');
exit;