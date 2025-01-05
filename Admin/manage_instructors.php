<?php
session_start();
include('../db_connection.php');

// Only Admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

// Handle instructor deletion
if (isset($_GET['delete_id'])) {
    $instructor_id = intval($_GET['delete_id']);

    // Check if instructor has assigned courses
    $checkCoursesQuery = "SELECT COUNT(*) AS course_count FROM courses WHERE created_by = $instructor_id";
    $result = mysqli_query($conn, $checkCoursesQuery);
    $courseCount = mysqli_fetch_assoc($result)['course_count'];

    if ($courseCount > 0) {
        $error = "Instructor has assigned courses and cannot be deleted.";
    } else {
        $query = "DELETE FROM users WHERE id = $instructor_id AND role_id = 2";

        if (mysqli_query($conn, $query)) {
            $message = "Instructor deleted successfully.";
        } else {
            $error = "Error deleting instructor.";
        }
    }
    
}

// Fetch all instructors from the database
$query = "SELECT * FROM users WHERE role_id = 2";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <title>Manage Instructors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
            justify-content: flex-start;
        }
        .action-btns a {
            white-space: nowrap;
        }
        .table-responsive {
                overflow-x: auto; /* Make the table scrollable */
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
        <h1>Manage Instructors</h1>

        <!-- Buttons for adding and going back -->
        <div class="d-flex justify-content-between flex-wrap">
            <a href="add_instructor.php" class="btn btn-add mb-2">Add New Instructor</a>
            <a href="admin_dashboard.php" class="btn btn-back mb-2">Back to Admin Dashboard</a>
        </div>

        <!-- Table for instructors, made responsive -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td>
                            <!-- Action buttons inside a flex container for responsiveness -->
                            <div class="action-btns">
                                <a href="edit_instructor.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?delete_id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Are you sure you want to delete this instructor?')" 
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
