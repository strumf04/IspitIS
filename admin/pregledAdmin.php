<?php
session_start();
if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8mb4">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin-style.css" rel="css/style.css"> 
</head>
<body class="container mt-5">
    <h2 >Dobrodošli, <?php echo htmlspecialchars($_SESSION['korisnik_ime']); ?></h2>
    <hr>
    <ul class="admin-links list-unstyled">
        <li><a href="dodajProizvod.php" class="btn btn-primary mb-2">Dodaj novu knjigu</a></li>
        <li><a href="prikazKnjiga.php" class="btn btn-success mb-2">Prikaži sve knjige</a></li>
        <li><a href="statistika.php" class="btn btn-secondary mb-2">Statisticki prikaz</a></li>
        <li><a href="../home.php" class="btn btn-secondary mb-2">Home page</a></li>
    </ul>
</body>
</html>