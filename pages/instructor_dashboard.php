<?php
include '../db_connection.php';
// include 'D:/xampp/htdocs/online examination system/includes/header.php';
// include 'D:/xampp/htdocs/online examination system/includes/sidebar.php';


session_start();

// Check if the user is logged in and is an instructor (role_id = 2)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
}

// Get total exams
$user_id = $_SESSION['user_id'];
$totalExamsQuery = $conn->prepare("SELECT COUNT(*) AS total_exams FROM exams where created_by = ? ");
// $totalExams = mysqli_fetch_assoc($totalExamsQuery)['total_exams'];
$totalExamsQuery->bind_param("i", $user_id);
$totalExamsQuery->execute();
$totalExams = $totalExamsQuery->get_result()->fetch_assoc()['total_exams'];

// Get total courses
// $totalCoursesQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_courses FROM courses");
// $totalCourses = mysqli_fetch_assoc($totalCoursesQuery)['total_courses'];
$totalCoursesQuery = $conn->prepare("SELECT COUNT(*) AS total_courses FROM courses where created_by = ? ");
$totalCoursesQuery->bind_param("i", $user_id);
$totalCoursesQuery->execute();
$totalCourses = $totalCoursesQuery->get_result()->fetch_assoc()['total_courses'];


// Get total students
// $totalStudentsQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_students FROM users");
// $totalStudents = mysqli_fetch_assoc($totalStudentsQuery)['total_students'];
$totalStudentsQuery = $conn->prepare(
    "SELECT COUNT(*) AS total_students FROM enrollment
     where course_id in (
     select id from courses where created_by = ?) "
);
$totalStudentsQuery->bind_param("i", $user_id);
$totalStudentsQuery->execute();
$totalStudents = $totalStudentsQuery->get_result()->fetch_assoc()['total_students'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styling */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #4e73df, #224abe);
            color: #fff;
            position: fixed;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
        }

        .sidebar h2 {
            text-align: center;
            color: #f8f9fc;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: #f8f9fc;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            margin-bottom: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .sidebar a:hover {
            background: #2e59d9;
        }

        .dashboard-content {
            margin-left: 270px;
            padding: 20px;
            box-sizing: border-box;
            width: calc(100% - 270px);
        }

        @media (max-width: 768px) {
            .dashboard-content {
                margin-left: 0;
                width: 100%;
            }
        }

        .dashboard-content h1 {
            margin-bottom: 20px;
            color: #343a40;
        }

        /* Card and Row Styling */
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background: #f8f9fc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1;
            min-width: 280px;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            color: #4e73df;
            margin-bottom: 10px;
        }

        .card p {
            margin-bottom: 15px;
            color:rgb(255, 196, 0);
        }

        .btn {
            background: #4e73df;
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .btn:hover {
            background: #2e59d9;
        }

        @media (max-width: 576px) {
            .card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Instructor Dashboard</h3>
        <a href="instructor_dashboard.php">Home</a>
        <a href="add_exam.php">Add Exam</a>
        <a href="manage_exam.php">Manage Exams</a>
        <a href="view_results.php">View Results</a>
        <a href="reports.php">Reports</a>
        <a href="grant_enrollment.php">Enrollment Requests</a>
        <a href="feedbacks.php">Feedbacks</a>
        <a href="logout.php">Logout</a>
    </div>

   <div class="dashboard-content">
    <div class="container mt-5">
        <h1 class="text-center">Online Examination System - Dashboard</h1>
        <div class="row mt-4">
            <!-- Cards for statistics -->
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Exams</h5>
                        <p class="card-text" id="totalExams"><?= $totalExams; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Courses</h5>
                        <p class="card-text" id="totalCourses"><?= $totalCourses; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <p class="card-text" id="totalStudents"><?= $totalStudents; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
</html>
