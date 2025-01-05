<?php 
session_start();
include('../db_connection.php');

// Only Admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

$totalInstructorsQuery = "SELECT COUNT(*) AS total_instructors FROM users WHERE role_id = 2";
$totalInstructorsResult = mysqli_query($conn, $totalInstructorsQuery);
$totalInstructors = mysqli_fetch_assoc($totalInstructorsResult);

$totalCoursesQuery = "SELECT COUNT(*) AS total_courses FROM courses";
$totalCoursesResult = mysqli_query($conn, $totalCoursesQuery);
$totalCourses = mysqli_fetch_assoc($totalCoursesResult);

$totalExamsQuery = "SELECT COUNT(*) AS total_exams FROM exams";
$totalExamsResult = mysqli_query($conn, $totalExamsQuery);
$totalExams = mysqli_fetch_assoc($totalExamsResult);
// Fetching total feedbacks
$totalFeedbacksQuery = "SELECT COUNT(*) AS total_feedbacks FROM feedbacks";
$totalFeedbacksResult = mysqli_query($conn, $totalFeedbacksQuery);
$totalFeedbacks = mysqli_fetch_assoc($totalFeedbacksResult);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            color: #6c757d;
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
        <h2>Admin Dashboard</h2>
        <a href="admin_dashboard.php">Home</a>
        <a href="manage_instructors.php">Manage Instructors</a>
        <a href="manage_courses.php">Manage Courses</a>
        <a href="view_results.php">View Results</a>
        <a href="reports.php">Reports</a>
        <a href="feedbacks.php">Feedbacks</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="dashboard-content">
        <h1>Dashboard Overview</h1>
        <div class="row">
            <div class="card">
                <h3>Manage Instructors</h3>
                <p>View and manage all instructors.</p>
                <p><strong>Total Instructors:</strong> <?= $totalInstructors['total_instructors'] ?></p>
                <a href="manage_instructors.php" class="btn">Manage Instructors</a>
            </div>
            <div class="card">
                <h3>Manage Courses</h3>
                <p>Manage courses for your platform.</p>
                <p><strong>Total Courses:</strong> <?= $totalCourses['total_courses'] ?></p>
                <a href="manage_courses.php" class="btn">Manage Courses</a>
            </div>
            <div class="card">
                <h3>View Results</h3>
                <p>View the results of students for each course and exam.</p>
                <p><strong>Total Exams:</strong> <?= $totalExams['total_exams'] ?></p>
                <a href="view_results.php" class="btn">View Results</a>
            </div>
            <div class="card">
                <h3>Reports</h3>
                <p>View detailed reports for courses and exams.</p>
                <a href="reports.php" class="btn">View Reports</a>
            </div>
            <div class="card">
                <h3>Feedbacks</h3>
                <p>View feedbacks from students about courses and exams</p>
                <p><strong>Total Feedbacks:</strong> <?= $totalFeedbacks['total_feedbacks'] ?></p>
                <a href="feedbacks.php" class="btn">View Feedbacks</a>
            </div>
        </div>
    </div>
</body>
</html>
