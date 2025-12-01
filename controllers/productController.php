<?php
require_once __DIR__ . '/../includes/db.php';

function addProduct($naziv, $opis, $cena, $kategorija_id, $kolicina, $file) {
    global $conn;

    if (empty($naziv) || empty($opis) || empty($cena) || empty($kategorija_id) || empty($kolicina)) {
        return "Sva polja su obavezna.";
    }

    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $imeFajla = basename($file['name']);
        $targetDir = __DIR__ . '/../source/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . $imeFajla;
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $putanjaSlike = 'source/' . $imeFajla;
            $stmt = $conn->prepare(
                "INSERT INTO proizvod (naziv, opis, cena, kategorija_id, kolicina, slika)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssdiis", $naziv, $opis, $cena, $kategorija_id, $kolicina, $putanjaSlike);
            if ($stmt->execute()) {
                $stmt->close();
                return "Proizvod uspešno dodat.";
            } else {
                $err = $stmt->error;
                $stmt->close();
                return "Greška pri unosu u bazu: " . $err;
            }
        } else {
            return "Greška pri uploadu slike.";
        }
    }
    return "Molimo odaberite sliku.";
}


function updateProduct($id, $naziv, $opis, $cena, $kolicina, $file = null) {
    global $conn;
    $putanjaSlike = null;

    if (isset($file) && !empty($file['name']) && $file['error'] === UPLOAD_ERR_OK) {
        $imeFajla = basename($file['name']);
        $targetDir = __DIR__ . '/../source/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . $imeFajla;
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $putanjaSlike = 'source/' . $imeFajla;
        } else {
            return "Greška pri uploadu nove slike.";
        }
    }

    if ($putanjaSlike) {
        $stmt = $conn->prepare(
            "UPDATE proizvod 
             SET naziv=?, opis=?, cena=?, kolicina=?, slika=? 
             WHERE prozivod_id=?"
        );
        $stmt->bind_param("ssdisi", $naziv, $opis, $cena, $kolicina, $putanjaSlike, $id);
    } else {
        $stmt = $conn->prepare(
            "UPDATE proizvod 
             SET naziv=?, opis=?, cena=?, kolicina=? 
             WHERE prozivod_id=?"
        );
        $stmt->bind_param("ssdii", $naziv, $opis, $cena, $kolicina, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        return "Proizvod je uspešno izmenjen.";
    } else {
        $err = $stmt->error;
        $stmt->close();
        return "Greška pri izmeni proizvoda: " . $err;
    }
}

function getProductById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM proizvod WHERE prozivod_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
}
