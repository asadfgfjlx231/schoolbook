<?php
function displaydata() {
    if (isset($_POST['avgall_btn'])) {
        
        $sql = " SELECT c.Year,st.Name,c.Code,Avg(grade)
        FROM grades g 
        JOIN subjects s ON s.id=g.subject_id 
        JOIN students st ON st.id=g.student_id 
        JOIN classes c ON c.id=st.Class_id 
        GROUP BY g.student_id
        ORDER BY Avg(grade) DESC;
               ";
               $diakok = lekerdezesToTomb($sql);
               
               echo<<<HTML
          
               <h3>Átlagok iskola szinten</h3>
               
       HTML;
       echoAvgAllAll($diakok);

       
       $sql = " SELECT c.Year,c.Code,Avg(grade)
       FROM grades g 
       JOIN subjects s ON s.id=g.subject_id 
       JOIN students st ON st.id=g.student_id 
       JOIN classes c ON c.id=st.Class_id 
       GROUP BY c.Code
       ORDER BY Avg(grade) DESC;
              ";
              $diakok = lekerdezesToTomb($sql);
              
              echo<<<HTML
         
              <h3>Átlagok iskola szinten</h3>
              
      HTML;
      echoAvgAllClass($diakok);
              
               

    }
    if (isset($_POST['avg_btn'])) {
       /* echo<<<HTML
   
        <h3>Átlagok iskolaszinten tantárgyanként</h3>
        
HTML;
        $sql = "SELECT Avg(grade),s.Name FROM grades g JOIN subjects s ON s.id=g.subject_id GROUP BY s.Name;";
        $diakok = lekerdezesToTomb($sql);
        echoAllAvg($diakok);*/
        $sql = "        SELECT c.Year,c.Code,s.Name,Avg(grade) FROM grades g JOIN subjects s ON s.id=g.subject_id JOIN students st ON st.id=g.student_id JOIN classes c ON c.id=st.Class_id GROUP BY c.Code, s.Name;";
        $diakok = lekerdezesToTomb($sql);
        echo<<<HTML
   
        <h3>Átlagok osztályszinten tantárgyanként</h3>
        
        
HTML;
echoClassAvg($diakok);
$sql = "SELECT c.Year,s.Name AS TName,st.Name,c.Code,Avg(grade)
        FROM grades g 
        JOIN subjects s ON s.id=g.subject_id 
        JOIN students st ON st.id=g.student_id 
        JOIN classes c ON c.id=st.Class_id 
        GROUP BY s.Name,g.student_id
        ORDER BY s.Name, Avg(grade) DESC;
        ";
        $diakok = lekerdezesToTomb($sql);
        
        echo<<<HTML
   
        <h3>Átlag Iskola szinten tantárgyanként</h3>
        
HTML;
        echoAVGAll($diakok);

        
       /* */




        
    }
    if (isset($_POST['rank_btn'])) {
        $sql = "SELECT s.Name AS TName,st.Name,c.Code,Avg(grade)
        FROM grades g 
        JOIN subjects s ON s.id=g.subject_id 
        JOIN students st ON st.id=g.student_id 
        JOIN classes c ON c.id=st.Class_id 
        GROUP BY s.Name,g.student_id
        ORDER BY s.Name, Avg(grade) DESC;
        ";
        $diakok = lekerdezesToTomb($sql);
        
        echo<<<HTML
   
        <h3>Rangsor Iskola szinten tantárgyanként</h3>
        
HTML;
        echoRankAll($diakok);

        $sql = "SELECT c.Code,s.Name AS TName,st.Name,Avg(grade)
 FROM grades g 
 JOIN subjects s ON s.id=g.subject_id 
 JOIN students st ON st.id=g.student_id 
 JOIN classes c ON c.id=st.Class_id 
 GROUP BY s.Name,c.Code,g.student_id
 ORDER BY c.Code,s.Name, Avg(grade) DESC;;
        ";
        $diakok = lekerdezesToTomb($sql);
        
        echo<<<HTML
   
        <h3>Rangsor Osztály szinten tantárgyanként</h3>
        
HTML;
        echoRankAllByClass($diakok);
        
        $sql = " SELECT st.Name,c.Code,Avg(grade)
 FROM grades g 
 JOIN subjects s ON s.id=g.subject_id 
 JOIN students st ON st.id=g.student_id 
 JOIN classes c ON c.id=st.Class_id 
 GROUP BY g.student_id
 ORDER BY Avg(grade) DESC;
        ";
        $diakok = lekerdezesToTomb($sql);
        
        echo<<<HTML
   
        <h3>Rangsor iskola szinten összesítve</h3>
        
HTML;
        echoRankAllAll($diakok);
        $sql = "  SELECT c.Code,st.Name,Avg(grade)
 FROM grades g 
 JOIN subjects s ON s.id=g.subject_id 
 JOIN students st ON st.id=g.student_id 
 JOIN classes c ON c.id=st.Class_id 
 GROUP BY c.Code, g.student_id
 ORDER BY c.Code,Avg(grade) DESC;
        ";
        $diakok = lekerdezesToTomb($sql);
        
        echo<<<HTML
   
        <h3>Rangsor osztály szinten összesítve</h3>
        
HTML;
        echoRankAllAllByClass($diakok);
        
    }
    if (isset($_POST['subjAvg_btn'])) {
        echo<<<HTML
   
        <h3>Átlagok Talulónként tantárgyanként</h3>
        
HTML;
        $sql = "SELECT st.Name,c.Code,s.Name AS TName,Avg(grade)
 FROM grades g 
 JOIN subjects s ON s.id=g.subject_id 
 JOIN students st ON st.id=g.student_id 
 JOIN classes c ON c.id=st.Class_id 
 GROUP BY st.Name, TName
 ORDER BY st.Name ASC;";
        $diakok = lekerdezesToTomb($sql);
        echosubjAvg($diakok);}
    if (isset($_POST['best_btn'])) {
            echo<<<HTML
       
            <h3>Legjobb és legrosszabb tanuló tantárgyanként</h3>
            
    HTML;
            $sql = "WITH StudentAverages AS (
    SELECT 
        students.name AS Student_Name,
        subjects.name AS Subject_Name,
        AVG(grades.grade) AS Avg_Grade
    FROM grades
    JOIN students ON grades.student_id = students.id
    JOIN subjects ON grades.subject_id = subjects.id
    GROUP BY students.id, subjects.id
),
BestWorst AS (
    SELECT 
        Subject_Name,
        Student_Name AS Best_Student,
        Avg_Grade AS Best_Average
    FROM StudentAverages
    WHERE (Subject_Name, Avg_Grade) IN 
          (SELECT Subject_Name, MAX(Avg_Grade) FROM StudentAverages GROUP BY Subject_Name)
    
    UNION ALL
    
    SELECT 
        Subject_Name,
        Student_Name AS Worst_Student,
        Avg_Grade AS Worst_Average
    FROM StudentAverages
    WHERE (Subject_Name, Avg_Grade) IN 
          (SELECT Subject_Name, MIN(Avg_Grade) FROM StudentAverages GROUP BY Subject_Name)
)
SELECT * FROM BestWorst
ORDER BY Subject_Name, Best_Average DESC;";
            $diakok = lekerdezesToTomb($sql);
            echoBest($diakok);
            echo<<<HTML
       
            <h3>Legjobb és legrosszabb tanuló</h3>
            
    HTML;
            $sql = "(SELECT st.Name AS Student, AVG(g.grade) AS Average_Grade
 FROM grades g
 JOIN subjects s ON s.id = g.subject_id
 JOIN students st ON st.id = g.student_id
 JOIN classes c ON c.id = st.Class_id
 GROUP BY g.student_id
 ORDER BY Average_Grade DESC
 LIMIT 1)
 
UNION ALL

(SELECT st.Name AS Student, AVG(g.grade) AS Average_Grade
 FROM grades g
 JOIN subjects s ON s.id = g.subject_id
 JOIN students st ON st.id = g.student_id
 JOIN classes c ON c.id = st.Class_id
 GROUP BY g.student_id
 ORDER BY Average_Grade ASC
 LIMIT 1);";
            $diakok = lekerdezesToTomb($sql);
            echoBestAll($diakok);}
            if (isset($_POST['Hall_btn'])) {
                echo<<<HTML
                  
                       <h1>Hall of fame</h1>
                       
               HTML;
        
                $sql = " SELECT c.Year,st.Name,c.Code,Avg(grade)
                FROM grades g 
                JOIN subjects s ON s.id=g.subject_id 
                JOIN students st ON st.id=g.student_id 
                JOIN classes c ON c.id=st.Class_id 
                GROUP BY g.student_id
                ORDER BY Avg(grade) DESC
                Limit 10;
                       ";
                       $diakok = lekerdezesToTomb($sql);
                       
                       echo<<<HTML
                  
                       <h3>tíz legjobb tanuló</h3>
                       
               HTML;
               echoHallAll($diakok);
        
               
               $sql = " SELECT c.Year,c.Code,Avg(grade)
               FROM grades g 
               JOIN subjects s ON s.id=g.subject_id 
               JOIN students st ON st.id=g.student_id 
               JOIN classes c ON c.id=st.Class_id 
               GROUP BY c.Code
               ORDER BY Avg(grade) DESC
               Limit 10;
                      ";
                      $diakok = lekerdezesToTomb($sql);
                      
                      echo<<<HTML
                 
                      <h3>Tíz legjobb osztály</h3>
                      
              HTML;
              echoHallClass($diakok);
                      
                       
        
            }
}
function insertGrade() {
        if (isset($_POST['submit_grade'])) {
            $student_id = $_POST['selected_student'];
            $subject_id = $_POST['selected_subject'];
            $grade = $_POST['grade'];
    
            $today = date("Y-m-d"); 
            $sql = "INSERT INTO grades (Student_id, Subject_id, Grade, Date) VALUES ('$student_id', '$subject_id', '$grade', '$today');";
            
    
            if (executeQuery($sql)) {
                echo "<p style='color: green;'>A jegy sikeresen rögzítve!</p>";
            } else {
                echo "<p style='color: red;'>Hiba történt a jegy rögzítésekor.</p>";
            }
            
            unset($_SESSION['jegyek']);
        }
    }

    function updateGrade() {
        if (isset($_POST['modify_grade'])) {
            $grade_id = $_POST['grade_id'];
            $new_grade = $_POST['new_grade'];
    
            $sql = "UPDATE grades SET Grade = '$new_grade' WHERE id = '$grade_id';";
    
            if (executeQuery($sql)) {
                echo "<p style='color: green;'>A jegy sikeresen módosítva!</p>";
            } else {
                echo "<p style='color: red;'>Hiba történt a jegy módosításakor.</p>";
            }
            unset($_SESSION['jegyek']);
        }
    }
    
    function deleteGrade() {
        if (isset($_POST['delete_grade'])) {
            $grade_id = $_POST['grade_id'];
    
            $sql = "DELETE FROM grades WHERE id = '$grade_id';";
    
            if (executeQuery($sql)) {
                echo "<p style='color: green;'>A jegy sikeresen törölve!</p>";
            } else {
                echo "<p style='color: red;'>Hiba történt a jegy törlésekor.</p>";
            }
            unset($_SESSION['jegyek']);
        }
    }
    function insertStudent() {
        if (isset($_POST['add_student'])) {
            $class_id = $_POST['selected_class'];
            $student_name = trim($_POST['student_name']);
            $student_gender = $_POST['student_gender'];
    
            if (!empty($student_name)) {
                $sql = "INSERT INTO students (Name,Gender,Class_id) VALUES ('$student_name',$student_gender,'$class_id');";
    
                if (executeQuery($sql)) {
                    echo "<p style='color: green;'>A tanuló sikeresen hozzáadva!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt a tanuló hozzáadásakor.</p>";
                }
            } else {
                echo "<p style='color: red;'>A név nem lehet üres!</p>";
            }
            unset($_SESSION['tanulok']);
        }
    }
    function updateStudentName() {
        if (isset($_POST['update_student'])) {
            $student_id = $_POST['selected_student'];
            $new_name = trim($_POST['new_student_name']);
    
            if (!empty($new_name)) {
                $sql = "UPDATE students SET Name='$new_name' WHERE id='$student_id'";
    
                if (executeQuery($sql)) {
                    echo "<p style='color: green;'>A tanuló neve sikeresen módosítva!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt a módosítás során.</p>";
                }
            } else {
                echo "<p style='color: red;'>A név nem lehet üres!</p>";
            }
            unset($_SESSION['tanulok']);
        }
    }
    function deleteStudent() {
        if (isset($_POST['delete_student'])) {
            $student_id = $_POST['selected_student'];
    
            if (!empty($student_id)) {
                
                $sql1 = "DELETE FROM grades WHERE student_id = '$student_id';";
                
                $sql2 = "DELETE FROM students WHERE id = '$student_id';";
    
                if (executeQuery($sql1) && executeQuery($sql2)) {
                    echo "<p style='color: green;'>A tanuló sikeresen törölve!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt a tanuló törlésekor.</p>";
                }
            } else {
                echo "<p style='color: red;'>Válassz ki egy tanulót!</p>";
            }
            unset($_SESSION['tanulok']);
        }
    }
    function insertClass() {
        if (isset($_POST['add_class'])) {
            $class_year = $_POST['class_year'];
            $class_name = trim($_POST['class_name']);
    
            if (!empty($class_name) && !empty($class_year)) {
                $sql = "INSERT INTO classes (Year, Code) VALUES ('$class_year', '$class_name');";
    
                if (executeQuery($sql)) {
                    echo "<p style='color: green;'>Az osztály sikeresen hozzáadva!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt az osztály hozzáadásakor.</p>";
                }
            } else {
                echo "<p style='color: red;'>Az év és az osztálynév nem lehet üres!</p>";
            }
            unset($_SESSION['osztaly']);
        }
    }
    function updateClassName() {
        if (isset($_POST['update_class_name'])) {
            $class_id = $_POST['selected_class'];
            $new_class_name = trim($_POST['new_class_name']);
    
            if (!empty($new_class_name)) {
                $sql = "UPDATE classes SET Code='$new_class_name' WHERE id='$class_id';";
    
                if (executeQuery($sql)) {
                    echo "<p style='color: green;'>Az osztály neve sikeresen módosítva!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt az osztály nevének módosításakor.</p>";
                }
            } else {
                echo "<p style='color: red;'>Az osztály neve nem lehet üres!</p>";
            }
            unset($_SESSION['osztaly']);
        }
    }
    function osztalyTorlese() {
        if (isset($_POST['delete_class'])) {
            $class_id = $_POST['selected_class'];
    
            if (!empty($class_id)) {
                
                $deleteGrades = "DELETE FROM grades WHERE Student_id IN (SELECT id FROM students WHERE Class_id = '$class_id');";
                $deleteStudents = "DELETE FROM students WHERE Class_id = '$class_id';";
                $deleteClass = "DELETE FROM classes WHERE id = '$class_id';";
    
                if (executeQuery($deleteGrades) && executeQuery($deleteStudents) && executeQuery($deleteClass)) {
                    echo "<p style='color: green;'>Az osztály és a hozzá tartozó adatok sikeresen törölve lettek!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt a törlés során.</p>";
                }
            } else {
                echo "<p style='color: red;'>Válassz ki egy osztályt!</p>";
            }
            unset($_SESSION['osztaly']);
        }
    
        echo <<<HTML
        <div class="formLek">
            <h2>Osztály törlése</h2>
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
            echo '<select name="selected_class">';
            echo '<option value="">-- Válassz osztályt --</option>';
    
            $classes = lekerdezesToTomb("SELECT id, Code FROM classes WHERE Year='$year';");
            foreach ($classes as $row) {
                echo '<option value="'.$row['id'].'">'.$row['Code'].'</option>';
            }
            echo '</select>';
            echo '<button type="submit" name="delete_class">Osztály törlése</button>';
        }
    
        echo "</form></div>";
    }
    function insertSubject() {
        if (isset($_POST['add_subj'])) {
            
            $subject_name = trim($_POST['subj_name']);
    
            if (!empty($subject_name)) {
                $sql = "INSERT INTO subjects (Name) VALUES ('$subject_name');";
    
                if (executeQuery($sql)) {
                    echo "<p style='color: green;'>A tantárgy sikeresen hozzáadva!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt a tantárgy hozzáadásakor.</p>";
                }
            } else {
                echo "<p style='color: red;'>A tantárgy neve nem lehet üres!</p>";
            }
        }
    }
function updateSubject() {
        if (isset($_POST['edit_subj'])) {
            $subject_id = $_POST['selected_subject'];
            $new_subject_name = trim($_POST['new_subj_name']);
    
            if (!empty($new_subject_name)) {
                $sql = "UPDATE subjects SET Name = '$new_subject_name' WHERE id = '$subject_id';";
    
                if (executeQuery($sql)) {
                    echo "<p style='color: green;'>A tantárgy sikeresen módosítva!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt a módosítás során.</p>";
                }
            } else {
                echo "<p style='color: red;'>A tantárgy neve nem lehet üres!</p>";
            }
        }
    }
    function deleteSubject() {
        if (isset($_POST['delete_subj'])) {
            $subject_id = $_POST['subj_id'];
    
            if (!empty($subject_id)) {
                executeQuery("DELETE FROM grades WHERE Subject_id = '$subject_id';");
                $sql = "DELETE FROM subjects WHERE id = '$subject_id';";
    
                if (executeQuery($sql)) {
                    echo "<p style='color: green;'>A tantárgy és a hozzá tartozó jegyek sikeresen törölve!</p>";
                } else {
                    echo "<p style='color: red;'>Hiba történt a tantárgy törlésekor.</p>";
                }
            } else {
                echo "<p style='color: red;'>Nem választottál ki tantárgyat!</p>";
            }
        }
    }
    
    
    