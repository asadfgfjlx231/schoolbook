<?php
echo '<form method="post" class="button-group">';
echo '<input type="submit" name="install" value="install">';
echo '</form>';
if (isset($_POST["install"])) {
    executeSQLFile('schoolbook_cr.sql');
    
    
}

function executeSQLFile($filePath) {
    // Adatbázis kapcsolat beállítása
    $dbHost = "localhost";     // Az adatbázis hosztja
    $dbUser = "root";          // Adatbázis felhasználó
    $dbPass = "";              // Adatbázis jelszó
    $dbName = "schoolbook_cr";    // Adatbázis neve

    // Kapcsolódás az adatbázishoz
    $connection = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Kapcsolódási hiba kezelése
    if ($connection->connect_error) {
        die("Adatbázis kapcsolat sikertelen: " . $connection->connect_error);
    }

    // SQL fájl beolvasása
    if (!file_exists($filePath)) {
        die("A megadott SQL fájl nem található: $filePath");
    }
    $sqlContent = file_get_contents($filePath);

    // SQL utasítások végrehajtása
    if ($connection->multi_query($sqlContent)) {
        do {
            // Feldolgozzuk az eredményeket (ha vannak)
            if ($result = $connection->store_result()) {
                $result->free();
            }
        } while ($connection->next_result());
        echo "Az SQL fájl sikeresen lefutott.";
    } else {
        echo "Hiba történt az SQL futtatása során: " . $connection->error;
    }

    // Kapcsolat lezárása
    $connection->close();
}

// Függvény hívása a schoolbook_cr.sql fájl futtatásához

