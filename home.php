<?php
session_start();
require_once 'includes/db.php';

$kategorije_result = $conn->query("SELECT * FROM kategorija ORDER BY naziv ASC");

$filter_kategorija = $_GET['kategorija_id'] ?? '';

if (!empty($filter_kategorija)) {
    $stmt = $conn->prepare("SELECT p.*, k.naziv AS kategorija_naziv
                            FROM proizvod p
                            LEFT JOIN kategorija k ON p.kategorija_id = k.kategorija_id
                            WHERE p.kategorija_id = ?
                            ORDER BY p.prozivod_id DESC");
    $stmt->bind_param("i", $filter_kategorija);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT p.*, k.naziv AS kategorija_naziv
            FROM proizvod p
            LEFT JOIN kategorija k ON p.kategorija_id = k.kategorija_id
            ORDER BY p.prozivod_id DESC";
    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="sr">
<head>
  <meta charset="utf-8mb4">
  <title>Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header>
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <strong class="me-2">Korisnik:</strong>
      <span class="me-3"><?= htmlspecialchars($_SESSION['korisnik_ime'] ?? "Nepoznat") ?></span>

      <?php if (isset($_SESSION['uloga']) && $_SESSION['uloga'] === 'admin'): ?>
        <form action="admin/pregledAdmin.php" method="get" style="display: inline;">
          <button type="submit" class="btn btn-primary me-3">Pregled</button>
        </form>
      <?php endif; ?>
    </div>

    <h1 class="title">Knjigazz</h1>

    <div class="d-flex align-items-center">
      <a href="korpa.php" class="btn btn-outline-primary me-2">Pregled korpe</a>
      <a href="logout.php" class="btn btn-outline-danger">Odjavi se</a>
    </div>
  </div>
</header>
<div style="height: 100px;"></div>

  <div class="container mt-5">

    
    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="kategorija_id" class="form-select">
                    <option value="">Sve kategorije</option>
                    <?php while ($kat = $kategorije_result->fetch_assoc()): ?>
                        <option value="<?= $kat['kategorija_id'] ?>" <?= ($kat['kategorija_id'] == $filter_kategorija) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat['naziv']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filtriraj</button>
            </div>
        </div>
    </form>
    
    <div class="row"> 
      <?php while ($row = $result->fetch_assoc()): ?>

        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <?php if (!empty($row['slika']) && file_exists($row['slika'])): ?>
              <img src="<?= htmlspecialchars($row['slika']) ?>" class="card-img-top" style="height:300px; object-fit:cover;">
            <?php else: ?>
              <img src="source/default.png" class="card-img-top" style="height:300px; object-fit:cover;">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['naziv']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($row['opis']) ?></p>
              <p><strong>Cena:</strong> <?= number_format($row['cena'], 2) ?> RSD</p>
              <p><span class="badge bg-secondary"><?= htmlspecialchars($row['kategorija_naziv'] ?? 'Nema kategorije') ?></span></p>
              <form method="POST" action="controllers/cartController.php">
                <input type="hidden" name="proizvod_id" value="<?= $row['prozivod_id'] ?>">
                <button type="submit" class="btn btn-success">Dodaj u korpu</button>
              </form>
            </div>  
          </div>
        </div>
        
      <?php endwhile; ?>
    </div>
  </div>
  <?php if (isset($_GET['poruka'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['poruka']) ?></div>
  <?php endif; ?>
</body>
</html>

