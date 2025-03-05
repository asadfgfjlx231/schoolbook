<?php
include_once("querys.php");
include_once("confing.php");
include_once("db.php");
include_once("html.php");
include_once("request.php");
htmlHead();

updateGrade();
 grade();
 insertGrade();
 deleteGrade();
 insertStudent();
 updateStudentName();
 deleteStudent();
 insertClass();
 updateClassName();
insertSubject();
updateSubject();
deleteSubject();
menu();
formDisplay();
year();
displaydata();
displaymessage($servername, $username,$password,$generatedNames);
htmlEnd();



