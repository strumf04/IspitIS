<?php
session_start();
if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$sql = "SELECT prozivod_id, naziv, slika FROM proizvod ORDER BY prozivod_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8mb4">
    <title>Prikaz knjiga - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2 class="mb-4">Sve knjige</h2>
    <a href="dodajProizvod.php" class="btn btn-primary mb-3">Dodaj novu knjigu</a>

    <?php if ($result->num_rows === 0): ?>
        <p>Nema nijedne knjige u bazi.</p>
    <?php else: ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($row['slika']) && file_exists("../" . $row['slika'])): ?>
                            <img src="../<?= htmlspecialchars($row['slika']) ?>"
                                 class="card-img-top" style="height:200px;object-fit:cover;">                       
                            <?php else: ?>
                                <img src="../default.png" class="card-img-top" style="height:300px; object-fit:cover;">
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['naziv']) ?></h5>
                            <div class="mt-auto">
                                <a href="izmenaProizvoda.php?id=<?= $row['prozivod_id'] ?>" class="btn btn-sm btn-warning">Izmeni</a>
                                <form method="POST" action="../controllers/deleteBook.php" class="d-inline"
                                      onsubmit="return confirm('Da li stvarno želiš da obrišeš ovu knjigu?');">
                                    <input type="hidden" name="prozivod_id" value="<?= $row['prozivod_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Obriši</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

</body>
</html>
