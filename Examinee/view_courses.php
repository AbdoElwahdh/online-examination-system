<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('config.php');
include('get_courses.php')
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Examination System - Courses</title>
    <link rel="stylesheet" href="course_style.css">
</head>
<body>
    <div class="container">
        <h1>Available Courses</h1>
        <div class="courses-grid" id="coursesContainer">
            <!-- Course cards will be dynamically inserted here by script.js -->
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>