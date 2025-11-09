<?php
require_once 'vendor/autoload.php'; // Učitaj Composer i Faker
require_once 'includes/db.php';     // Tvoja konekcija ka bazi

$faker = Faker\Factory::create();

//
/* 

    /////BRISANJE SVEGA IZ TABELA////////

$conn->query("SET FOREIGN_KEY_CHECKS = 0"); 
$conn->query("TRUNCATE TABLE korpa");
$conn->query("TRUNCATE TABLE proizvod");
$conn->query("TRUNCATE TABLE korisnik");
$conn->query("SET FOREIGN_KEY_CHECKS = 1"); 
-------------------------------------------------------- */

$kat_result = $conn->query("SELECT kategorija_id FROM kategorija");
$kategorija_ids = [];
while ($kat = $kat_result->fetch_assoc()) {
    $kategorija_ids[] = $kat['kategorija_id'];
}

for ($i = 0; $i < 50; $i++) {
    $naziv = $faker->sentence(3);
    $opis = $faker->paragraph();
    $cena = $faker->randomFloat(2, 300, 3000); 
    $kolicina = $faker->numberBetween(1, 20);
    $kategorija_id = $faker->randomElement($kategorija_ids);
    $slika = "source/default.png";

    $stmt = $conn->prepare("INSERT INTO proizvod (naziv, opis, cena, kolicina, kategorija_id, slika) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiis", $naziv, $opis, $cena, $kolicina, $kategorija_id, $slika);
    $stmt->execute();
    $stmt->close();
}


for ($i = 0; $i < 5; $i++) {
    $ime = $faker->firstName();
    $email = $faker->unique()->safeEmail();
    $lozinka = password_hash('password123', PASSWORD_DEFAULT); 
    $uloga = 'korisnik';

    $stmt = $conn->prepare("INSERT INTO korisnik (ime, email, lozinka, uloga) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $ime, $email, $lozinka, $uloga);
    $stmt->execute();
    $stmt->close();
}


$korisnici_result = $conn->query("SELECT korisnik_id FROM korisnik");
$korisnik_ids = [];
while ($k = $korisnici_result->fetch_assoc()) {
    $korisnik_ids[] = $k['korisnik_id'];
}


$proizvodi_result = $conn->query("SELECT prozivod_id FROM proizvod");
$proizvod_ids = [];
while ($p = $proizvodi_result->fetch_assoc()) {
    $proizvod_ids[] = $p['prozivod_id'];
}


for ($i = 0; $i < 50; $i++) {
    $korisnik_id = $faker->randomElement($korisnik_ids);
    $proizvod_id = $faker->randomElement($proizvod_ids);
    $kolicina = $faker->numberBetween(1, 5);
    $datum = $faker->dateTimeThisYear()->format('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO korpa (korisnik_id, proizvod_id, kolicina, datum) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $korisnik_id, $proizvod_id, $kolicina, $datum);
    $stmt->execute();
    $stmt->close();
}

echo "Baza uspešno popunjena test podacima!";
?>
