<?php
session_start();
if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../controllers/productController.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Neispravan ID proizvoda.");
}

$prozivod_id = (int)$_GET['id'];
$proizvod = getProductById($prozivod_id);
if (!$proizvod) {
    die("Proizvod nije pronađen.");
}

$poruka = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kolicina = (int)$_POST['kolicina'];

    $poruka = updateProduct(
    $prozivod_id,
    trim($_POST['naziv']),
    trim($_POST['opis']),
    floatval($_POST['cena']),
    $kolicina,
    $_FILES['slika']
);
    $proizvod = getProductById($prozivod_id);
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8mb4">
    <title>Izmeni proizvod</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Izmeni proizvod</h2>
    <?php if ($poruka): ?>
        <div class="alert alert-info"><?= htmlspecialchars($poruka) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Naziv</label>
            <input type="text" name="naziv" class="form-control" value="<?= htmlspecialchars($proizvod['naziv']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Opis</label>
            <textarea name="opis" class="form-control" required><?= htmlspecialchars($proizvod['opis']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Cena</label>
            <input type="number" name="cena" step="0.01" class="form-control" value="<?= htmlspecialchars($proizvod['cena']) ?>" required>
        </div>
        <div class="form-group">
            <label for="kolicina">Količina:</label>
            <input type="number" name="kolicina" id="kolicina" class="form-control" required
                value="<?= htmlspecialchars($proizvod['kolicina']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Trenutna slika</label><br>
            <?php if (!empty($proizvod['slika']) && file_exists(__DIR__ . '/../' . $proizvod['slika'])): ?>
                <img src="../<?= $proizvod['slika'] ?>" style="max-height: 150px;">
            <?php else: ?>
                <p>Nema slike</p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Nova slika (opciono)</label>
            <input type="file" name="slika" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Sačuvaj izmene</button>
        <a href="prikazKnjiga.php" class="btn btn-secondary">Nazad</a>

        <a href="../home.php" class="btn btn-secondary">Nazad na početnu</a>

    </form>
</body>
</html>