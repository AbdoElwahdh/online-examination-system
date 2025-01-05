<?php
session_start();
include('../db_connection.php');

// Only Admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

// Handle course deletion
if (isset($_GET['delete_id'])) {
    $course_id = intval($_GET['delete_id']);
    $query = "DELETE FROM courses WHERE id = $course_id";

    if (mysqli_query($conn, $query)) {
        $message = "Course deleted successfully.";
    } else {
        $error = "Error deleting course.";
    }
}

// Fetch all courses from the database
$query = "SELECT c.id, c.name, c.description, u.username AS Instructor 
          FROM courses c 
          JOIN users u ON c.created_by = u.id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <title>Manage Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }
        .btn-add, .btn-back {
            margin-bottom: 20px;
        }
        .btn-add {
            background-color: #4e73df;
            color: #fff;
        }
        .btn-add:hover {
            background-color: #2e59d9;
        }
        .btn-back {
            background-color: #20c997;
            color: #fff;
        }
        .btn-back:hover {
            background-color: #17a589;
        }
        .table th {
            background-color: #4e73df;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .action-btns {
            display: flex;
            gap: 5px;
        }
        .action-btns a {
            white-space: nowrap;
        }
        .table-responsive {
                overflow-x: auto; /* Ensure table is scrollable if necessary */
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
    <div class="container">
        <h1>Manage Courses</h1>

        <!-- Buttons for adding and going back -->
        <div class="d-flex justify-content-between flex-wrap">
            <a href="add_course.php" class="btn btn-add mb-2">Add New Course</a>
            <a href="admin_dashboard.php" class="btn btn-back mb-2">Back to Admin Dashboard</a>
        </div>

        <!-- Table for courses, made responsive -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Instructor</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['Instructor'] ?></td>
                        <td>
                            <!-- Action buttons inside a flex container for responsiveness -->
                            <div class="action-btns">
                                <a href="edit_course.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?delete_id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Are you sure you want to delete this course?')" 
                                   class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
