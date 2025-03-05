<?php
include_once("db.php");
include_once("querys.php");

function htmlHead() {
    echo <<<HTML
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Osztályok kezelése</title>
    <link rel="stylesheet" href="l.css">
    
</head>
<body>
<div class="container">
    <h1>Osztályok kezelése</h1>

HTML;

}
session_start(); 

function year() {
    echo <<<HTML
    <div class="formLek">
        <h2>Évek</h2>
        <form method="POST">
HTML;

    if (doesDatabaseExist("school")) {
        $sql = "SELECT DISTINCT Year FROM classes ORDER BY Year ASC;";
        $years = lekerdezesToTomb($sql);

        foreach ($years as $row) {
            $year = htmlspecialchars($row['Year']);
            echo '<button type="submit" name="selected_year" value="'.$year.'">'.$year.'</button>';
        }
    }

    echo '</form></div>';

    if (isset($_POST['selected_year'])) {
        $_SESSION['selected_year'] = $_POST['selected_year'];
    }

    if (isset($_SESSION['selected_year'])) {
        classes($_SESSION['selected_year']);
    }
}

function classes($year) {
    echo <<<HTML
    <div class="formLek">
        <h2>Osztályok ($year)</h2>
        <form method="POST">
HTML;

    if (doesDatabaseExist("school")) {
        $year = htmlspecialchars($year);
        $sql = "SELECT Code FROM classes WHERE Year='$year';";
        $classes = lekerdezesToTomb($sql);

        foreach ($classes as $row) {
            $code = htmlspecialchars($row['Code']);
            echo '<button type="submit" name="classCode" value="'.$code.'">'.$code.'</button>';
        }
    }

    echo '<button type="submit" name="show_top_students" value="'.$year.'">Legjobb 10 Tanuló</button>';
    echo '</form></div>';

    if (isset($_POST['classCode'])) {
        students($_POST['classCode']);
       
    }

    if (isset($_SESSION['selected_class'])) {
        students($_SESSION['selected_class']);
    }

    if (isset($_POST['show_top_students'])) {
        topStudents($_POST['show_top_students']);
    }
}

function students($class) {
    $class = htmlspecialchars($class);
    $sql = "SELECT s.Name FROM students s 
            JOIN classes c ON c.id = s.Class_id 
            WHERE c.Code = '$class' 
            ORDER BY s.Name ASC;";
    
    $diakok = lekerdezesToTomb($sql);

    echo <<<HTML
    <div class="formLek">
        <h3>Tanulók az osztályban: $class</h3>
        <ul>
HTML;

    if (empty($diakok)) {
        echo "<li>Nincsenek diákok ebben az osztályban.</li>";
    } else {
        foreach ($diakok as $diak) {
            echo "<li>" . htmlspecialchars($diak['Name']) . "</li>";
        }
    }

    echo "</ul></div>";
}

function topStudents($year) {
    $year = htmlspecialchars($year);
    $sql = "
        SELECT s.Name, ROUND(AVG(g.Grade), 2) AS average
        FROM students s
        JOIN grades g ON s.id = g.Student_id
        JOIN classes c ON s.Class_id = c.id
        WHERE c.Year = '$year'
        GROUP BY s.id
        ORDER BY average DESC
        LIMIT 10;
    ";
    
    $top_students = lekerdezesToTomb($sql);

    echo <<<HTML
    <div class="formLek">
        <h3>Legjobb 10 Tanuló az évben: $year</h3>
        <ul>
HTML;

    if (empty($top_students)) {
        echo "<li>Nincs elérhető adat.</li>";
    } else {
        foreach ($top_students as $student) {
            echo "<li>" . htmlspecialchars($student['Name']) . " - Átlag: " . $student['average'] . "</li>";
        }
    }

    echo "</ul></div>";
}

function echoClassAv($adatok) {
    if (empty($adatok)) {
        echo "<p>Nincs megjeleníthető adat.</p>";
        return;
    }

    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr><th>Év</th><th>Osztály</th><th>Tantárgy</th><th>Átlag</th></tr>";

    foreach ($adatok as $line) {
        echo "<tr>";
        echo "<td>" . $line['Year']. "</td>";
        echo "<td>" . htmlspecialchars($line['Code']) . "</td>";
        echo "<td>" . htmlspecialchars($line['Name']) . "</td>";
        echo "<td>" . number_format($line['Avg(grade)'], 2) . "</td>";
        echo "</tr>";
    }
}







function menu() {
    echo <<<HTML
    <div class="formLek">
        <h2>Lekérdezések</h2>
        <form method="POST">
HTML;
    if (doesDatabaseExist("school")) {
        echo '<button type="submit" name="avg_btn">Átlagok lekérdezése</button>';
        echo '<button type="submit" name="rank_btn">Rangsor lekérdezése</button>';
        echo '<button type="submit" name="subjAvg_btn">Tantárgyi átlagok</button>';
        echo '<button type="submit" name="best_btn">Legjobb osztály</button>';
        echo '<button type="submit" name="avgall_btn">Össz átlag</button>';
        echo '<button type="submit" name="Hall_btn">Hall of fame</button>';
       
        
    }
    echo '</form></div>';
}

function echosubjAvg($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Tanuló</th><th>Osztály</th><th>Tantárgy</th><th>Átlag</th></tr>";
    
    foreach ($adatok as $line) {
        echo "<tr>";
        
       
        echo "<td>" . htmlspecialchars($line["Name"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["TName"]) . "</td>";
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
        
        
    }

    echo "</table>";
}
function echoBest($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Tantárgy</th><th>Tanuló</th><th>Átlag</th></tr>";
    
    foreach ($adatok as $line) {
        echo "<tr>";
        
       
        echo "<td>" . htmlspecialchars($line["Subject_Name"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Best_Student"]) . "</td>";
        echo "<td>" . number_format($line["Best_Average"], 4) . "</td>"; 
        echo "</tr>";
        
        
    }

    echo "</table>";
}
function echoBestAll($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Tanuló</th><th>Átlag</th></tr>";
    
    foreach ($adatok as $line) {
        echo "<tr>";
        
       
        echo "<td>" . htmlspecialchars($line["Student"]) . "</td>";
        
        echo "<td>" . number_format($line["Average_Grade"], 4) . "</td>"; 
        echo "</tr>";
        
        
    }

    echo "</table>";
}
function echoRankAll($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Sorszám</th><th>Tantárgy</th><th>Tanuló</th><th>Osztály</th><th>Átlag</th></tr>";
    $i=1;
    foreach ($adatok as $index => $line) {
        
        if($i>180)
        {$i=1;}

        echo "<tr>";
        echo "<td>" . ($i) . "</td>"; 
        echo "<td>" . htmlspecialchars($line["TName"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Name"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
        $i++;
    }

    echo "</table>";
}
function echoAVGAll($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Év</th><th>Tantárgy</th><th>Tanuló</th><th>Osztály</th><th>Átlag</th></tr>";
    $i=1;
    foreach ($adatok as $index => $line) {
        
        if($i>180)
        {$i=1;}

        echo "<tr>";
        echo "<td>" .$line["Year"]. "</td>"; 
        echo "<td>" . htmlspecialchars($line["TName"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Name"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
        $i++;
    }

    echo "</table>";
}
function echoRankAllAll($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Sorszám</th><th>Tanuló</th><th>Osztály</th><th>Átlag</th></tr>";

    foreach ($adatok as $index => $line) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>"; 
        echo "<td>" . htmlspecialchars($line["Name"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
    }

    echo "</table>";
    
}
function echoHallAll($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Sorszám</th><th>ÉV</th><th>Tanuló</th><th>Osztály</th><th>Átlag</th></tr>";

    foreach ($adatok as $index => $line) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>"; 
        echo "<td>" .$line["Year"]  . "</td>"; 
        echo "<td>" . htmlspecialchars($line["Name"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
    }

    echo "</table>";
    
}
function echoAvgAllAll($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Year</th><th>Tanuló</th><th>Osztály</th><th>Átlag</th></tr>";

    foreach ($adatok as $index => $line) {
        echo "<tr>";
        echo "<td>" .$line["Year"]  . "</td>"; 
        echo "<td>" . htmlspecialchars($line["Name"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
    }

    echo "</table>";
}
function echoAvgAllClass($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Year</th><th>Osztály</th><th>Átlag</th></tr>";

    foreach ($adatok as $index => $line) {
        echo "<tr>";
        echo "<td>" .$line["Year"]  . "</td>"; 
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
    }

    echo "</table>";
}
function echoRankAllByClass($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Sorszám</th><th>Osztály</th><th>Tantárgy</th><th>Tanuló</th><th>Átlag</th></tr>";
    $i=1;
    foreach ($adatok as $index => $line) {
        if($i>15)
        {$i=1;}

        echo "<tr>";
        echo "<td>" . ($i) . "</td>"; 
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["TName"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Name"]) . "</td>";
        $i++;
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
    }

    echo "</table>";
}
function echoRankAllAllByClass($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Sorszám</th><th>Osztály</th><th>Tanuló</th><th>Átlag</th></tr>";
    $i=1;
    foreach ($adatok as $index => $line) {
        if($i>15)
        {$i=1;}

        echo "<tr>";
        echo "<td>" . ($i) . "</td>"; 
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . htmlspecialchars($line["Name"]) . "</td>";
        $i++;
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
    }

    echo "</table>";
}
function echoHallClass($adatok) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Sorszám</th><th>Year</th><th>Osztály</th><th>Átlag</th></tr>";

    foreach ($adatok as $index => $line) {
        echo "<tr>";
        echo "<td>" .$index+1  . "</td>"; 
        echo "<td>" .$line["Year"]  . "</td>"; 
        echo "<td>" . htmlspecialchars($line["Code"]) . "</td>";
        echo "<td>" . number_format($line["Avg(grade)"], 4) . "</td>"; 
        echo "</tr>";
    }

    echo "</table>";
}

function echoAllAvg($adatok) {
    if (empty($adatok)) {
        echo "<p>Nincs megjeleníthető adat.</p>";
        return;
    }

    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr><th>Tantárgy</th><th>Átlag</th></tr>";

    foreach ($adatok as $line) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($line['Name']) . "</td>";
        echo "<td>" . number_format($line['Avg(grade)'], 2) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}
function echoClassAvg($adatok) {
    if (empty($adatok)) {
        echo "<p>Nincs megjeleníthető adat.</p>";
        return;
    }

    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr><th>Év</th><th>Osztály</th><th>Tantárgy</th><th>Átlag</th></tr>";

    foreach ($adatok as $line) {
        echo "<tr>";
        echo "<td>" . $line['Year']. "</td>";
        echo "<td>" . htmlspecialchars($line['Code']) . "</td>";
        echo "<td>" . htmlspecialchars($line['Name']) . "</td>";
        echo "<td>" . number_format($line['Avg(grade)'], 2) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}


function outputAndFlush($message) {
    echo "<div class='message'>$message</div>";
    ob_flush();
    flush();
}

function displaymessage($servername, $username, $password, $generatedNames) {
    if (isset($_POST['create_db'])) {
        echo "<div class='message-container'>";
        $conn = connectToDatabase($servername, $username, $password);
        outputAndFlush(createDatabase($conn));
        outputAndFlush(createSubjectsTable($conn));
        outputAndFlush(createClassesTable($conn));
        outputAndFlush(createStudentsTable($conn));
        outputAndFlush(createGradesTable($conn));
        outputAndFlush(insertStudentsIntoDatabase($generatedNames));
        
        outputAndFlush(insertClassesIntoDatabase());
        outputAndFlush(insertSubjectsIntoDatabase());
        outputAndFlush(insertGradesIntoDatabase());
        closeConnection($conn);
        echo "</div>";
    }
}

function formDisplay() {
    if (!doesDatabaseExist("school")){
    echo <<<HTML
    <div class="form-container">
        <h2>Adatbázis és táblák kezelése</h2>
        <form method="POST">
HTML;
    
        echo '<button type="submit" name="create_db">Adatbázis létrehozása</button>';
    
    echo '</form></div>';
}
}

function htmlEnd() {
    echo <<<HTML
</div>
</body>
</html>
HTML;
}
function gradeForm() {
    echo <<<HTML
    <div class="formLek">
    <h2>Jegy Rögzítés</h2>
        <form method="POST">
    
        
        
HTML;

    echo '<label>Válassz egy évet:</label>';
    echo '<select name="selected_year" onchange="this.form.submit()">';
    echo '<option value="">-- Válassz évet --</option>';

    $years = lekerdezesToTomb("SELECT DISTINCT Year FROM classes ORDER BY Year ASC;");
    foreach ($years as $row) {
        $selected = (isset($_POST['selected_year']) && $_POST['selected_year'] == $row['Year']) ? 'selected' : '';
        echo '<option value="'.$row['Year'].'" '.$selected.'>'.$row['Year'].'</option>';
    }
    echo '</select>';

    if (isset($_POST['selected_year'])) {
        $year = $_POST['selected_year'];

        echo '<label>Válassz egy osztályt:</label>';
        echo '<select name="selected_class" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz osztályt --</option>';

        $classes = lekerdezesToTomb("SELECT Code FROM classes WHERE Year='$year';");
        foreach ($classes as $row) {
            $selected = (isset($_POST['selected_class']) && $_POST['selected_class'] == $row['Code']) ? 'selected' : '';
            echo '<option value="'.$row['Code'].'" '.$selected.'>'.$row['Code'].'</option>';
        }
        echo '</select>';
    }

    if (isset($_POST['selected_class'])) {
        $class = $_POST['selected_class'];

        echo '<label>Válassz egy diákot:</label>';
        echo '<select name="selected_student" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz diákot --</option>';

        $students = lekerdezesToTomb("SELECT id, Name FROM students WHERE Class_id=(SELECT id FROM classes WHERE Code='$class');");
        foreach ($students as $row) {
            $selected = (isset($_POST['selected_student']) && $_POST['selected_student'] == $row['id']) ? 'selected' : '';
            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Name'].'</option>';
        }
        echo '</select>';
    }

    if (isset($_POST['selected_student'])) {
        echo '<label>Válassz egy tantárgyat:</label>';
        echo '<select name="selected_subject">';
        echo '<option value="">-- Válassz tantárgyat --</option>';

        $subjects = lekerdezesToTomb("SELECT id, Name FROM subjects;");
        foreach ($subjects as $row) {
            echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
        }
        echo '</select>';

        echo '<label>Adj meg egy jegyet (1-5):</label>';
        echo '<input type="number" name="grade" min="1" max="5" required>';

        echo '<button type="submit" name="submit_grade">Jegy feltöltése</button>';
    }
    echo"</form></div>";

    
}

function grade(){
    echo <<<HTML
    <div class="formLek">
        <h2>Hozzáadás, Módosítás törlés</h2>
        <form method="POST">
HTML;
    if (isset($_POST['jegyek'])) {$_SESSION['jegyek'] = $_POST['jegyek'];unset($_SESSION['tanulok']);unset($_SESSION['osztaly']);unset($_SESSION['tantargy']);}
    if (isset($_POST['tanulok'])) {$_SESSION['tanulok'] = $_POST['tanulok'];unset($_SESSION['jegyek']);unset($_SESSION['osztaly']);unset($_SESSION['tantargy']);}
    if (isset($_POST['osztaly'])) {$_SESSION['osztaly'] = $_POST['osztaly'];unset($_SESSION['tanulok']);unset($_SESSION['jegyek']);unset($_SESSION['tantargy']);}
    if (isset($_POST['tantargy'])) {$_SESSION['tantargy'] = $_POST['tantargy'];unset($_SESSION['tanulok']);unset($_SESSION['jegyek']);unset($_SESSION['osztaly']);}
    echo '<button type="submit" name="jegyek">jegyek</button>';
    echo '<button type="submit" name="tanulok">Tanulók</button>';
    echo '<button type="submit" name="osztaly">Osztályok</button>';
    echo '<button type="submit" name="tantargy">Tantárgy</button>';
    if (isset($_SESSION['jegyek'])) {gradeForm();modifyGradeForm();deleteGradeForm();}
    if (isset($_SESSION['tanulok'])) {addStudentForm(); editStudentForm();deleteStudentForm();}
    if (isset($_SESSION['osztaly'])) {addClassForm();updateClassNameForm(); osztalyTorlese();}
    if (isset($_SESSION['tantargy'])) {addSubjForm(); addSubjectEditForm();deleteSubjectForm();}
    echo '</form></div>';
    
}
function deleteSubjectForm() {
    echo <<<HTML
    <div class="formLek">
        <h2>Tantárgy Törlése</h2>
        <form method="POST">
            <label>Válassz egy tantárgyat:</label>
            <select name="subj_id" required>
                <option value="">-- Válassz tantárgyat --</option>
HTML;

    $subjects = lekerdezesToTomb("SELECT id, Name FROM subjects ORDER BY Name ASC;");
    foreach ($subjects as $row) {
        echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
    }

    echo <<<HTML
            </select>
            <button type="submit" name="delete_subj">Tantárgy törlése</button>
        </form>
    </div>
    HTML;
}

function modifyGradeForm() {
    echo <<<HTML
    <div class="formLek">
   
        <h2>Jegy Módosítása</h2>
        <form method="POST">

HTML;

    
    echo '<label>Válassz egy évet:</label>';
    echo '<select name="selected_year" onchange="this.form.submit()">';
    echo '<option value="">-- Válassz évet --</option>';

    $years = lekerdezesToTomb("SELECT DISTINCT Year FROM classes ORDER BY Year ASC;");
    foreach ($years as $row) {
        $selected = (isset($_POST['selected_year']) && $_POST['selected_year'] == $row['Year']) ? 'selected' : '';
        echo '<option value="'.$row['Year'].'" '.$selected.'>'.$row['Year'].'</option>';
    }
    echo '</select>';

    
    if (isset($_POST['selected_year'])) {
        $year = $_POST['selected_year'];
        echo '<label>Válassz egy osztályt:</label>';
        echo '<select name="selected_class" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz osztályt --</option>';

        $classes = lekerdezesToTomb("SELECT Code FROM classes WHERE Year='$year';");
        foreach ($classes as $row) {
            $selected = (isset($_POST['selected_class']) && $_POST['selected_class'] == $row['Code']) ? 'selected' : '';
            echo '<option value="'.$row['Code'].'" '.$selected.'>'.$row['Code'].'</option>';
        }
        echo '</select>';
    }

    
    if (isset($_POST['selected_class'])) {
        $class = $_POST['selected_class'];
        echo '<label>Válassz egy diákot:</label>';
        echo '<select name="selected_student" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz diákot --</option>';

        $students = lekerdezesToTomb("SELECT id, Name FROM students WHERE Class_id=(SELECT id FROM classes WHERE Code='$class');");
        foreach ($students as $row) {
            $selected = (isset($_POST['selected_student']) && $_POST['selected_student'] == $row['id']) ? 'selected' : '';
            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Name'].'</option>';
        }
        echo '</select>';
    }

    
    if (isset($_POST['selected_student'])) {
        $student_id = $_POST['selected_student'];
        echo '<label>Válassz egy tantárgyat:</label>';
        echo '<select name="selected_subject" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz tantárgyat --</option>';

        $subjects = lekerdezesToTomb("SELECT DISTINCT s.id, s.Name FROM grades g JOIN subjects s ON g.Subject_id = s.id WHERE g.Student_id = '$student_id';");
        foreach ($subjects as $row) {
            $selected = (isset($_POST['selected_subject']) && $_POST['selected_subject'] == $row['id']) ? 'selected' : '';
            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Name'].'</option>';
        }
        echo '</select>';
    }

   
    if (isset($_POST['selected_subject'])) {
        $subject_id = $_POST['selected_subject'];
        echo '<label>Válassz egy dátumot:</label>';
        echo '<select name="selected_date" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz dátumot --</option>';

        $dates = lekerdezesToTomb("SELECT DISTINCT Date FROM grades WHERE Student_id='$student_id' AND Subject_id='$subject_id' ORDER BY Date ASC;");
        foreach ($dates as $row) {
            echo '<option value="'.$row['Date'].'">'.$row['Date'].'</option>';
        }
        echo '</select>';
    }

    
    if (isset($_POST['selected_date'])) {
        $date = $_POST['selected_date'];
        $grade_data = lekerdezesToTomb("SELECT id, Grade FROM grades WHERE Student_id='$student_id' AND Subject_id='$subject_id' AND Date='$date';");
        
        if (!empty($grade_data)) {
            $grade_id = $grade_data[0]['id'];
            $current_grade = $grade_data[0]['Grade'];

            echo '<label>Jelenlegi jegy: ' . $current_grade . '</label>';
            echo '<label>Új jegy (1-5):</label>';
            echo '<input type="number" name="new_grade" min="1" max="5" required>';
            echo '<input type="hidden" name="grade_id" value="'.$grade_id.'">';
            echo '<button type="submit" name="modify_grade">Jegy módosítása</button>';
        } else {
            echo "<p style='color: red;'>Nincs ilyen jegy!</p>";
        }
    }

    echo"</form></div>";
}
function deleteGradeForm() {
    echo <<<HTML
    <div class="formLek">
        <h2>Jegy Törlése</h2>
        <form method="POST">
HTML;

    
    echo '<label>Válassz egy évet:</label>';
    echo '<select name="selected_year" onchange="this.form.submit()">';
    echo '<option value="">-- Válassz évet --</option>';

    $years = lekerdezesToTomb("SELECT DISTINCT Year FROM classes ORDER BY Year ASC;");
    foreach ($years as $row) {
        $selected = (isset($_POST['selected_year']) && $_POST['selected_year'] == $row['Year']) ? 'selected' : '';
        echo '<option value="'.$row['Year'].'" '.$selected.'>'.$row['Year'].'</option>';
    }
    echo '</select>';

   
    if (isset($_POST['selected_year'])) {
        $year = $_POST['selected_year'];
        echo '<label>Válassz egy osztályt:</label>';
        echo '<select name="selected_class" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz osztályt --</option>';

        $classes = lekerdezesToTomb("SELECT Code FROM classes WHERE Year='$year';");
        foreach ($classes as $row) {
            $selected = (isset($_POST['selected_class']) && $_POST['selected_class'] == $row['Code']) ? 'selected' : '';
            echo '<option value="'.$row['Code'].'" '.$selected.'>'.$row['Code'].'</option>';
        }
        echo '</select>';
    }

   
    if (isset($_POST['selected_class'])) {
        $class = $_POST['selected_class'];
        echo '<label>Válassz egy diákot:</label>';
        echo '<select name="selected_student" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz diákot --</option>';

        $students = lekerdezesToTomb("SELECT id, Name FROM students WHERE Class_id=(SELECT id FROM classes WHERE Code='$class');");
        foreach ($students as $row) {
            $selected = (isset($_POST['selected_student']) && $_POST['selected_student'] == $row['id']) ? 'selected' : '';
            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Name'].'</option>';
        }
        echo '</select>';
    }

    
    if (isset($_POST['selected_student'])) {
        $student_id = $_POST['selected_student'];
        echo '<label>Válassz egy tantárgyat:</label>';
        echo '<select name="selected_subject" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz tantárgyat --</option>';

        $subjects = lekerdezesToTomb("SELECT DISTINCT s.id, s.Name FROM grades g JOIN subjects s ON g.Subject_id = s.id WHERE g.Student_id = '$student_id';");
        foreach ($subjects as $row) {
            $selected = (isset($_POST['selected_subject']) && $_POST['selected_subject'] == $row['id']) ? 'selected' : '';
            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Name'].'</option>';
        }
        echo '</select>';
    }

   
    if (isset($_POST['selected_subject'])) {
        $subject_id = $_POST['selected_subject'];
        echo '<label>Válassz egy dátumot:</label>';
        echo '<select name="selected_date" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz dátumot --</option>';

        $dates = lekerdezesToTomb("SELECT DISTINCT Date FROM grades WHERE Student_id='$student_id' AND Subject_id='$subject_id' ORDER BY Date ASC;");
        foreach ($dates as $row) {
            echo '<option value="'.$row['Date'].'">'.$row['Date'].'</option>';
        }
        echo '</select>';
    }

    
    if (isset($_POST['selected_date'])) {
        $date = $_POST['selected_date'];
        $grade_data = lekerdezesToTomb("SELECT id, Grade FROM grades WHERE Student_id='$student_id' AND Subject_id='$subject_id' AND Date='$date';");

        if (!empty($grade_data)) {
            $grade_id = $grade_data[0]['id'];
            $current_grade = $grade_data[0]['Grade'];

            echo '<label>Jelenlegi jegy: ' . $current_grade . '</label>';
            echo '<input type="hidden" name="grade_id" value="'.$grade_id.'">';
            echo '<button type="submit" name="delete_grade">Jegy törlése</button>';
        } else {
            echo "<p style='color: red;'>Nincs ilyen jegy!</p>";
        }
    }

    echo "</form></div>";
}
function addStudentForm() {
    echo <<<HTML
    <div class="formLek">
        <h2>Új Tanuló Hozzáadása</h2>
        <form method="POST">
HTML;


    echo '<label>Válassz egy évet:</label>';
    echo '<select name="selected_year" onchange="this.form.submit()">';
    echo '<option value="">-- Válassz évet --</option>';

    $years = lekerdezesToTomb("SELECT DISTINCT Year FROM classes ORDER BY Year ASC;");
    foreach ($years as $row) {
        $selected = (isset($_POST['selected_year']) && $_POST['selected_year'] == $row['Year']) ? 'selected' : '';
        echo '<option value="'.$row['Year'].'" '.$selected.'>'.$row['Year'].'</option>';
    }
    echo '</select>';


    if (isset($_POST['selected_year'])) {
        $year = $_POST['selected_year'];
        echo '<label>Válassz egy osztályt:</label>';
        echo '<select name="selected_class" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz osztályt --</option>';

        $classes = lekerdezesToTomb("SELECT id, Code FROM classes WHERE Year='$year';");
        foreach ($classes as $row) {
            $selected = (isset($_POST['selected_class']) && $_POST['selected_class'] == $row['id']) ? 'selected' : '';
            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Code'].'</option>';
        }
        echo '</select>';
    }


    if (isset($_POST['selected_class'])) {
        echo '<label>Tanuló neve:</label>';
        echo '<input type="text" name="student_name" required>';
      
        echo '<label>Nem (0 = Férfi, 1 = Nő):</label>';
        echo '<select name="student_gender" required>';
        echo '<option value="0">Férfi</option>';
        echo '<option value="1">Nő</option>';
        echo '</select>';
        echo '<button type="submit" name="add_student">Tanuló hozzáadása</button>';
    }

    echo "</form></div>";
}
function editStudentForm() {
    echo <<<HTML
    <div class="formLek">
        <h2>Tanuló nevének módosítása</h2>
        <form method="POST">
HTML;

    echo '<label>Válassz egy évet:</label>';
    echo '<select name="selected_year" onchange="this.form.submit()">';
    echo '<option value="">-- Válassz évet --</option>';

    $years = lekerdezesToTomb("SELECT DISTINCT Year FROM classes ORDER BY Year ASC;");
    foreach ($years as $row) {
        $selected = (isset($_POST['selected_year']) && $_POST['selected_year'] == $row['Year']) ? 'selected' : '';
        echo '<option value="'.$row['Year'].'" '.$selected.'>'.$row['Year'].'</option>';
    }
    echo '</select>';

    if (isset($_POST['selected_year'])) {
        $year = $_POST['selected_year'];
        echo '<label>Válassz egy osztályt:</label>';
        echo '<select name="selected_class" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz osztályt --</option>';

        $classes = lekerdezesToTomb("SELECT id, Code FROM classes WHERE Year='$year';");
        foreach ($classes as $row) {
            $selected = (isset($_POST['selected_class']) && $_POST['selected_class'] == $row['id']) ? 'selected' : '';
            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Code'].'</option>';
        }
        echo '</select>';
    }

    if (isset($_POST['selected_class'])) {
        $class_id = $_POST['selected_class'];
        echo '<label>Válassz egy tanulót:</label>';
        echo '<select name="selected_student" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz tanulót --</option>';

        $students = lekerdezesToTomb("SELECT id, Name FROM students WHERE Class_id='$class_id';");
        foreach ($students as $row) {
            $selected = (isset($_POST['selected_student']) && $_POST['selected_student'] == $row['id']) ? 'selected' : '';
            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Name'].'</option>';
        }
        echo '</select>';
    }

    if (isset($_POST['selected_student'])) {
        echo '<label>Új név:</label>';
        echo '<input type="text" name="new_student_name" required>';
        echo '<button type="submit" name="update_student">Név módosítása</button>';
    }

    echo "</form></div>";}
    function deleteStudentForm() {
        echo <<<HTML
        <div class="formLek">
            <h2>Tanuló Törlése</h2>
            <form method="POST">
    HTML;
    
        echo '<label>Válassz egy évet:</label>';
        echo '<select name="selected_year" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz évet --</option>';
    
        $years = lekerdezesToTomb("SELECT DISTINCT Year FROM classes ORDER BY Year ASC;");
        foreach ($years as $row) {
            $selected = (isset($_POST['selected_year']) && $_POST['selected_year'] == $row['Year']) ? 'selected' : '';
            echo '<option value="'.$row['Year'].'" '.$selected.'>'.$row['Year'].'</option>';
        }
        echo '</select>';
    
        if (isset($_POST['selected_year'])) {
            $year = $_POST['selected_year'];
            echo '<label>Válassz egy osztályt:</label>';
            echo '<select name="selected_class" onchange="this.form.submit()">';
            echo '<option value="">-- Válassz osztályt --</option>';
    
            $classes = lekerdezesToTomb("SELECT id, Code FROM classes WHERE Year='$year';");
            foreach ($classes as $row) {
                $selected = (isset($_POST['selected_class']) && $_POST['selected_class'] == $row['id']) ? 'selected' : '';
                echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Code'].'</option>';
            }
            echo '</select>';
        }
    
        if (isset($_POST['selected_class'])) {
            $class_id = $_POST['selected_class'];
            echo '<label>Válassz egy tanulót:</label>';
            echo '<select name="selected_student">';
            echo '<option value="">-- Válassz tanulót --</option>';
    
            $students = lekerdezesToTomb("SELECT id, Name FROM students WHERE Class_id='$class_id' ORDER BY Name ASC;");
            foreach ($students as $row) {
                echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
            }
            echo '</select>';
    
            echo '<button type="submit" name="delete_student">Tanuló törlése</button>';
        }
    
        echo "</form></div>";
    }
    function addClassForm() {
        echo <<<HTML
        <div class="formLek">
            <h2>Új Osztály Hozzáadása</h2>
            <form method="POST">
                <label>Év:</label>
                <input type="number" name="class_year" >
                <label>Osztály neve:</label>
                <input type="text" name="class_name" >
                <button type="submit" name="add_class">Osztály hozzáadása</button>
            </form>
        </div>
        HTML;
    }
    function updateClassNameForm() {
        echo <<<HTML
        <div class="formLek">
            <h2>Osztály Nevének Módosítása</h2>
            <form method="POST">
    HTML;
    
        echo '<label>Válassz egy évet:</label>';
        echo '<select name="selected_year" onchange="this.form.submit()">';
        echo '<option value="">-- Válassz évet --</option>';
    
        $years = lekerdezesToTomb("SELECT DISTINCT Year FROM classes ORDER BY Year ASC;");
        foreach ($years as $row) {
            $selected = (isset($_POST['selected_year']) && $_POST['selected_year'] == $row['Year']) ? 'selected' : '';
            echo '<option value="'.$row['Year'].'" '.$selected.'>'.$row['Year'].'</option>';
        }
        echo '</select>';
    
        if (isset($_POST['selected_year'])) {
            $year = $_POST['selected_year'];
            echo '<label>Válassz egy osztályt:</label>';
            echo '<select name="selected_class" onchange="this.form.submit()">';
            echo '<option value="">-- Válassz osztályt --</option>';
    
            $classes = lekerdezesToTomb("SELECT id, Code FROM classes WHERE Year='$year';");
            foreach ($classes as $row) {
                $selected = (isset($_POST['selected_class']) && $_POST['selected_class'] == $row['id']) ? 'selected' : '';
                echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['Code'].'</option>';
            }
            echo '</select>';
        }
    
        if (isset($_POST['selected_class'])) {
            echo '<label>Új osztálynév:</label>';
            echo '<input type="text" name="new_class_name">';
            echo '<button type="submit" name="update_class_name">Osztály névének módosítása</button>';
        }
    
        echo "</form></div>";
    }
function addSubjForm() {
        echo <<<HTML
        <div class="formLek">
            <h2>Új Tantárgy Hozzáadása</h2>
            <form method="POST">
                
                <label>Tantárgy neve:</label>
                <input type="text" name="subj_name" >
                <button type="submit" name="add_subj">Osztály hozzáadása</button>
            </form>
        </div>
        HTML;
    }
    function addSubjectEditForm() {
        echo <<<HTML
        <div class="formLek">
            <h2>Tantárgy Módosítása</h2>
            <form method="POST">
                
                <label>Válassz egy tantárgyat:</label>
                <select name="selected_subject" required>
                    <option value="">-- Válassz tantárgyat --</option>
    HTML;
    
        $subjects = lekerdezesToTomb("SELECT id, Name FROM subjects ORDER BY Name ASC;");
        foreach ($subjects as $row) {
            echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
        }
        echo '</select>';
    
        echo <<<HTML
                <label>Új név:</label>
                <input type="text" name="new_subj_name" required>
                <button type="submit" name="edit_subj">Módosítás</button>
            </form>
        </div>
        HTML;
    }


