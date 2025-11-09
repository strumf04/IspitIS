<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include('includes/db.php');
if ($conn->connect_error) {
    die("Greška pri konekciji s bazom: " . $conn->connect_error);
}

require_once 'controllers/authController.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = loginUser(trim($_POST['email']), $_POST['lozinka']);
    if ($res === true) {
        header('Location: home.php');
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
    <title>Prijava</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Prijava</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="lozinka">Lozinka:</label>
                <input type="password" class="form-control" id="lozinka" name="lozinka" required>
            </div>
            <div class="container text-center">
                <button type="submit" class="btn btn-primary">Prijavi se</button>
                <p>Nemaš nalog? <a href="register.php">Registruj se</a></p>
            </div>
            </div>

            

        </form>
    </div>
</body>
</html>

<?php if ($message): ?>
  <div class="alert alert-danger mt-3"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>