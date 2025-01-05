<?php

if (!isset($_SESSION['user_id']) && $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];


$query = "SELECT name,description,id ,duration FROM exams 
                where course_id in (select course_id from enrollment where user_id = $user_id )"; 
                
$result = mysqli_query($conn, $query);


?>