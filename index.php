<?php
include_once("querys.php");
include_once("confing.php");
include_once("db.php");
include_once("html.php");
include_once("request.php");
htmlHead();
gradeForm(); 
insertGrade(); 

menu();
formDisplay();
year();
displaydata();
displaymessage($servername, $username,$password,$generatedNames);
htmlEnd();

