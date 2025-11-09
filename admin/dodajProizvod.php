<?php
session_start();
if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
	header('Location: ../login.php');
	exit;
}

require_once __DIR__ . '/../controllers/productController.php';

$poruka = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$poruka = addProduct(
		trim($_POST['naziv']),
		trim($_POST['opis']),
		floatval($_POST['cena']), 
		trim($_POST['kategorija']),
		$_FILES['slika']
	);
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
	<meta charset="utf-8mb4">
	<title>Dodaj proizvod</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
	<h2>Dodaj novi proizvod</h2>
	<?php if ($poruka): ?>
		<div class="alert alert-info"><?= htmlspecialchars($poruka) ?></div>
	<?php endif; ?>
	<form method="POST" enctype="multipart/form-data">
		<div class="mb-3">
			<label class="form-label">Naziv</label>
			<input type="text" name="naziv" class="form-control" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Opis</label>
			<textarea name="opis" class="form-control" required></textarea>
		</div>
		<div class="mb-3">
			<label class="form-label">Cena</label>
			<input type="number" name="cena" step="0.01" class="form-control" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Kategorija</label>
			<input type="text" name="kategorija" class="form-control" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Slika</label>
			<input type="file" name="slika" class="form-control" required>
		</div>
		<button type="submit" class="btn btn-primary">Dodaj</button>
	</form>
</body>
</html>