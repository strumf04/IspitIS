<?php
require_once 'vendor/autoload.php'; 
require_once 'includes/db.php';

$faker = Faker\Factory::create();



    /////BRISANJE SVEGA IZ TABELA////////

$conn->query("SET FOREIGN_KEY_CHECKS = 0"); 
$conn->query("TRUNCATE TABLE korpa");
$conn->query("TRUNCATE TABLE proizvod");
$conn->query("SET FOREIGN_KEY_CHECKS = 1"); 


$autori = [
    'Ivo Andrić', 'Mesa Selimović', 'Danilo Kiš', 'Desanka Maksimović', 'Dobrica Ćosić', 
    'Mihailo Lalić', 'Borislav Pekić', 'Miloš Crnjanski', 'Isidora Sekulić', 'Branko Ćopić',
    'Ernest Hemingway', 'George Orwell', 'F. Scott Fitzgerald', 'Jane Austen', 'Charles Dickens',
    'Leo Tolstoy', 'Fyodor Dostoevsky', 'Mark Twain', 'J.K. Rowling', 'Stephen King',
    'Agatha Christie', 'Haruki Murakami', 'Gabriel García Márquez', 'Paulo Coelho', 'Oscar Wilde',
    'Franz Kafka', 'Victor Hugo', 'J.R.R. Tolkien', 'C.S. Lewis', 'John Steinbeck',
    'Albert Camus', 'Arthur Conan Doyle', 'William Shakespeare', 'Virginia Woolf', 'Emily Brontë',
    'Aldous Huxley', 'Herman Melville', 'Ray Bradbury', 'Kurt Vonnegut', 'Erich Maria Remarque',
    'Umberto Eco', 'Neil Gaiman', 'James Joyce', 'Margaret Atwood', 'Terry Pratchett',
    'Jack London', 'Dante Alighieri', 'Homer', 'Sun Tzu', 'Plato'
];

$kat_result = $conn->query("SELECT kategorija_id FROM kategorija");
$kategorija_ids = [];
while ($kat = $kat_result->fetch_assoc()) {
    $kategorija_ids[] = $kat['kategorija_id'];
}

for ($i = 0; $i < 700; $i++) {
    $naziv = $faker->sentence(3);
    $opis = $faker->paragraph();
    $cena = $faker->randomFloat(2, 300, 3000); 
    $kolicina = $faker->numberBetween(1, 20);
    $kategorija_id = $faker->randomElement($kategorija_ids);
    $slika = "source/default.png";
    $autor = $faker->randomElement($autori);

    $stmt = $conn->prepare("INSERT INTO proizvod (naziv, opis, cena, kolicina, kategorija_id, slika, autor) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiiss", $naziv, $opis, $cena, $kolicina, $kategorija_id, $slika, $autor);
    $stmt->execute();
    $stmt->close();
}


for ($i = 0; $i < 1000; $i++) {
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


for ($i = 0; $i < 4500; $i++) {
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
