<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['korisnik_id'])) {
	header('Location: login.php');
	exit;
}

$korisnik_id = $_SESSION['korisnik_id'];

$poruka = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zavrsi'])) {
	$sql = "SELECT k.proizvod_id, k.kolicina
			FROM korpa k
			WHERE k.korisnik_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $korisnik_id);
	if ($stmt->execute()) {
		$poruka = "Hvala na kupovini! Vaša korpa je sada prazna.";
	} else {
		$poruka = "Greška prilikom završetka kupovine.";
	}
	$korpa_result = $stmt->get_result();

	while ($item = $korpa_result->fetch_assoc()) {
		$proizvod_id = $item['proizvod_id'];
		$kolicina = $item['kolicina'];

		$update_sql = "UPDATE proizvod 
			   SET kolicina = GREATEST(kolicina - ?, 0) 
			   WHERE prozivod_id = ?";
		$update_stmt = $conn->prepare($update_sql);
		$update_stmt->bind_param("ii", $kolicina, $proizvod_id);
		$update_stmt->execute();
		$update_stmt->close();
	}

	$stmt->close();

	$delete_sql = "DELETE FROM korpa WHERE korisnik_id = ?";
	$delete_stmt = $conn->prepare($delete_sql);
	$delete_stmt->bind_param("i", $korisnik_id);
	$delete_stmt->execute();
	$delete_stmt->close();
}

$sql = "SELECT k.korpa_id, p.naziv, p.cena, p.slika, k.kolicina, (p.cena * k.kolicina) AS ukupno
		FROM korpa k
		JOIN proizvod p ON k.proizvod_id = p.prozivod_id
		WHERE k.korisnik_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $korisnik_id);
$stmt->execute();
$result = $stmt->get_result();

$ukupna_cena = 0;
?>

<!DOCTYPE html>
<html lang="sr">
<head>
	<meta charset="utf-8mb4">
	<title>Korpa</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css">
</head>
<body class="container mt-5">
	<h2 class="mb-4">Vaša korpa</h2>

	<?php if ($result->num_rows > 0): ?>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Slika</th>
					<th>Naziv</th>
					<th>Količina</th>
					<th>Cena</th>
					<th>Ukupno</th>
					<th>Akcija</th>
				</tr>
			</thead>
			<tbody>
				<?php while ($row = $result->fetch_assoc()): 
					$ukupna_cena += $row['ukupno'];
				?>
					<tr>
						<td><img src="<?= htmlspecialchars($row['slika']) ?>" width="60"></td>
						<td><?= htmlspecialchars($row['naziv']) ?></td>
						<td><?= $row['kolicina'] ?></td>
						<td><?= number_format($row['cena'], 2) ?> RSD</td>
						<td><?= number_format($row['ukupno'], 2) ?> RSD</td>
						<td>
							<form method="POST" action="controllers/cartRemove.php" onsubmit="return confirm('Da li ste sigurni?');">
								<input type="hidden" name="korpa_id" value="<?= $row['korpa_id'] ?>">
								<button type="submit" class="btn btn-danger btn-sm">Ukloni</button>
							</form>
						</td>
					</tr>
				<?php endwhile; ?>
				<tr>
					<td colspan="4" class="text-end"><strong>Ukupno:</strong></td>
					<td colspan="2"><strong><?= number_format($ukupna_cena, 2) ?> RSD</strong></td>
				</tr>
			</tbody>
		</table>
		<form method="POST">
			<button type="submit" name="zavrsi" class="btn btn-success">Završi kupovinu</button>
		</form>
	<?php else: ?>
		<p>Vaša korpa je prazna.</p>
	<?php endif; ?>
</body>
</html>
<?php if (!empty($poruka)): ?>
			<div class="alert alert-success"><?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>