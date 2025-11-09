<?php
include('includes/db.php');

require_once 'controllers/authController.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = registerUser(trim($_POST['ime']), trim($_POST['email']), $_POST['lozinka']);
    if ($res === true) {
        header('Location: login.php');
        exit;
    } else {
        $message = $res;  
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8mb4">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Registracija</h2>
        <form method="POST">
            <div class="form-group">
                <label for="ime">Ime:</label>
                <input type="text" class="form-control" id="ime" name="ime" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="lozinka">Lozinka:</label>
                <input type="password" class="form-control" id="lozinka" name="lozinka" required>
            </div>
            <div class="container text-center">
                <button type="submit" class="btn btn-primary">Registruj se</button>
                <p>Imaš nalog? <a href="login.php">Prijavi se</a></p>
            </div>
        </form>
    </div>
</body>
</html>

