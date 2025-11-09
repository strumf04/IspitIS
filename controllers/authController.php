<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

function registerUser($ime, $email, $lozinka) {
    global $conn;

    $hashLozinka = password_hash($lozinka, PASSWORD_DEFAULT);
    $uloga = 'korisnik'; 


    $stmt = $conn->prepare("INSERT INTO korisnik (ime, email, lozinka, uloga) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $ime, $email, $hashLozinka, $uloga);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $greska = "Greška pri registraciji: " . $stmt->error;
        $stmt->close();
        return $greska;
    }


}

function loginUser($email, $lozinka) {
    global $conn;
    $stmt = $conn->prepare("SELECT korisnik_id, ime, lozinka, uloga FROM korisnik WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($lozinka, $row['lozinka'])) {
            $_SESSION['korisnik_id'] = $row['korisnik_id'];
            $_SESSION['korisnik_ime'] = $row['ime'];
            $_SESSION['uloga'] = $row['uloga'];
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return "Pogrešna lozinka.";
        }
        
    } else {
        $stmt->close();
        return "Korisnik sa tim emailom ne postoji.";
    }
}
?>