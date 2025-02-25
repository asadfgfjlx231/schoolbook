<?php
include_once("html.php");
include_once("request.php");
$servername = "localhost";
$username = "root";
$password = "";
$db="school";

 
function connectToDatabase($servername, $username, $password) {
    $conn = new mysqli($servername, $username, $password);
   
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
   
    return $conn;
}
 
function createDatabase($conn) {
    $sql = "CREATE SCHEMA IF NOT EXISTS `School` DEFAULT CHARACTER SET utf8;";
    if ($conn->query($sql) === TRUE) {
        echo "Az adatbázis sikeresen létre lett hozva!<br>";
    } else {
        echo "Hiba történt az adatbázis létrehozásakor: " . $conn->error . "<br>";
    }
}
 
function createSubjectsTable($conn) {
    $sql = "
    CREATE TABLE IF NOT EXISTS `School`.`Subjects` (
      `id` INT NULL AUTO_INCREMENT,
      `Name` VARCHAR(20) NOT NULL,
      PRIMARY KEY (`id`))
    ENGINE = InnoDB;";
   
    if ($conn->query($sql) === TRUE) {
        echo "A `Subjects` tábla sikeresen létre lett hozva!<br>";
    } else {
        echo "Hiba történt a `Subjects` tábla létrehozásakor: " . $conn->error . "<br>";
    }
}
 
function createClassesTable($conn) {
    $sql = "
    CREATE TABLE IF NOT EXISTS `School`.`Classes` (
      `id` INT NULL AUTO_INCREMENT,
      `Code` VARCHAR(3) NOT NULL,
      `Year` INT NULL,
      PRIMARY KEY (`id`))
    ENGINE = InnoDB;";
   
    if ($conn->query($sql) === TRUE) {
        echo "A `Classes` tábla sikeresen létre lett hozva!<br>";
    } else {
        echo "Hiba történt a `Classes` tábla létrehozásakor: " . $conn->error . "<br>";
    }
}
 
function createStudentsTable($conn) {
    $sql = "
    CREATE TABLE IF NOT EXISTS `School`.`Students` (
      `id` INT NULL AUTO_INCREMENT,
      `Name` VARCHAR(50) NULL,
      `Gender` INT NULL,
      `Class_id` INT NULL,
      PRIMARY KEY (`id`))
    ENGINE = InnoDB;";
   
    if ($conn->query($sql) === TRUE) {
        echo "A `Students` tábla sikeresen létre lett hozva!<br>";
    } else {
        echo "Hiba történt a `Students` tábla létrehozásakor: " . $conn->error . "<br>";
    }
}
 
function createGradesTable($conn) {
    $sql = "
    CREATE TABLE IF NOT EXISTS `School`.`Grades` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `student_id` INT NULL,
      `subject_id` INT NULL,
      `grade` INT NULL,
      `date` DATE NULL,
      PRIMARY KEY (`id`))
    ENGINE = InnoDB;";
   
    if ($conn->query($sql) === TRUE) {
        echo "A `Grades` tábla sikeresen létre lett hozva!<br>";
    } else {
        echo "Hiba történt a `Grades` tábla létrehozásakor: " . $conn->error . "<br>";
    }
}
 
function closeConnection($conn) {
    $conn->close();
}
function doesDatabaseExist($dbName) {
   
    $dbHost = "localhost";     
    $dbUser = "root";         
    $dbPass = "";              

 
    $connection = new mysqli($dbHost, $dbUser, $dbPass);


    if ($connection->connect_error) {
        die("Adatbázis kapcsolat sikertelen: " . $connection->connect_error);
    }


    $query = "SHOW DATABASES LIKE '$dbName'";
    $result = $connection->query($query);

  
    $exists = $result && $result->num_rows > 0;

   
    if ($result) {
        $result->free();
    }
    $connection->close();

    return $exists; 
}

function insertSubjectsIntoDatabase() {
    
    $host = 'localhost'; 
    $dbname = 'school'; 
    $username = 'root'; 
    $password = ''; 


    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO subjects (Name) VALUES (:name)";

        $stmt = $pdo->prepare($sql);


        foreach (SUBJECTS as $subject) {
            $stmt->execute([':name' => $subject]);
        }

        echo "A tantárgyak sikeresen feltöltve az adatbázisba.";
    } catch (PDOException $e) {

        echo "Hiba történt: " . $e->getMessage();
    }
}
function insertClassesIntoDatabase() {
    
    $host = 'localhost'; 
    $dbname = 'school'; 
    $username = 'root'; 
    $password = ''; 


    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

       
    
        $sql = "INSERT INTO classes (Code,Year) VALUES (:code,:year)";

       
        $stmt = $pdo->prepare($sql);

        foreach (CLASSES as $class) {
            $year = rand(2021, 2025);

            $stmt->execute([':code' => $class,
        ':year'=>$year] )
            ;
        }

        echo "A osztályok sikeresen feltöltve az adatbázisba.";
    } catch (PDOException $e) {
       
        echo "Hiba történt: " . $e->getMessage();
    }
}
function getName() {
    $nevek = [];

    for ($i = 0; $i < 180; $i++) {

        $veznev = NAMES['lastnames'][rand(0, count(NAMES['lastnames']) - 1)];

        $nem = rand(0, 1);

        if ($nem == 1) { 
            $kernev = NAMES['firstnames']['men'][rand(0, count(NAMES['firstnames']['men']) - 1)];
        } else { 
            $kernev = NAMES['firstnames']['women'][rand(0, count(NAMES['firstnames']['women']) - 1)];
        }


        $nev = $veznev . " " . $kernev;


        $nevek[] = ["name" => $nev, "gender" => $nem];
    }

    return $nevek;
}


function insertStudentsIntoDatabase($nevek) {

    $host = 'localhost';
    $dbname = 'school';
    $username = 'root';
    $password = '';

    try {

        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO students (Name, Gender, Class_id) VALUES (:name, :gender, :class_id)";

        $stmt = $pdo->prepare($sql);

        $classIds = [];
        for ($classId = 1; $classId <= 12; $classId++) {
            for ($i = 0; $i < 15; $i++) {
                $classIds[] = $classId;
            }
        }

        foreach ($nevek as $index => $student) {
            $stmt->execute([
                ':name' => $student['name'],
                ':gender' => $student['gender'],
                ':class_id' => $classIds[$index % count($classIds)] 
            ]);
        }

        echo "A tanulók sikeresen feltöltve az adatbázisba.";
    } catch (PDOException $e) {
        
        echo "Hiba történt: " . $e->getMessage();
    }
}


$generatedNames = getName();




function insertGradesIntoDatabase() {
    
    $host = 'localhost';
    $dbname = 'school';
    $username = 'root';
    $password = '';

    try {

        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO grades (student_id, subject_id, grade, date) VALUES (:student_id, :subject_id, :grade, :date)";


        $stmt = $pdo->prepare($sql);


        $fixedDate = '1984-01-24';


        $studentIds = [];
        for ($studentId = 1; $studentId <= 180; $studentId++) {
            
                $studentIds[] = $studentId;
            
        }
        

        
        foreach ($studentIds as $studentId) {
            $jegyszam=rand(3, 5);
            for ($i = 1; $i <= $jegyszam; $i++){

            for ($subjectId = 1; $subjectId <= 8; $subjectId++) {
                
                $randomGrade = rand(1, 5);

                
                $stmt->execute([
                    ':student_id' => $studentId,
                    ':subject_id' => $subjectId,
                    ':grade' => $randomGrade,
                    ':date' => $fixedDate,
                ]);
            }
        }
        }

        echo "A jegyek sikeresen feltöltve az adatbázisba.";
        
    } catch (PDOException $e) {
       
        echo "Hiba történt: " . $e->getMessage();
    }
    

}
function lekerdezesToTomb($sql) {
    $eredmenyTomb = [];
    $servername = "localhost";
$username = "root";
$password = "";
$dbname = "school";

$conn = mysqli_connect($servername, $username, $password, $dbname);

    if ($conn) {
        $result = mysqli_query($conn, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $eredmenyTomb[] = $row; 
            }
            mysqli_free_result($result);
        } else {
            die("Hiba a lekérdezés végrehajtása során: " . mysqli_error($conn));
        }
    } else {
        die("Hiba az adatbázis kapcsolattal!");
    }
    mysqli_close($conn);

    return $eredmenyTomb; 
    
}
function executeQuery($sql) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "school";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Kapcsolati hiba: " . mysqli_connect_error());
    }

    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);

    return $result;
}







 

?>
 
