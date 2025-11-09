<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['korisnik_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnik_id = $_SESSION['korisnik_id'];
    $proizvod_id = intval($_POST['proizvod_id']);
    $kolicina = intval($_POST['kolicina']);

    if ($kolicina <= 0) {
        $kolicina = 1;
    }

    $sql = "SELECT * FROM korpa WHERE korisnik_id = ? AND proizvod_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $korisnik_id, $proizvod_id);
    $stmt->execute();
    $postojeci = $stmt->get_result();

    if ($postojeci->num_rows > 0) {
        $sql_update = "UPDATE korpa SET kolicina = kolicina + ? WHERE korisnik_id = ? AND proizvod_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iii", $kolicina, $korisnik_id, $proizvod_id);
        $stmt_update->execute();
    } else {
        $sql_insert = "INSERT INTO korpa (korisnik_id, proizvod_id, kolicina, datum) VALUES (?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iii", $korisnik_id, $proizvod_id, $kolicina);
        $stmt_insert->execute();
    }

    header("Location: ../home.php?poruka=Proizvod dodat u korpu");
    exit;
}
?>