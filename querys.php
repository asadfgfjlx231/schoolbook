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
        }
    }
    