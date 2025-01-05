<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
// Include the database connection file
include '../db_connection.php';

// Handle deletion if a delete request is made
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $deleteQuery = "DELETE FROM exams WHERE id = '$delete_id'";
    if (mysqli_query($conn, $deleteQuery)) {
        $message = "Exam deleted successfully.";
    } else {
        $message = "Error deleting exam: " . mysqli_error($conn);
    }
}

// Fetch all exams along with course names
$query = "SELECT exams.id, exams.name AS name , exams.description, courses.name AS course_name 
          FROM exams 
          JOIN courses ON exams.course_id = courses.id 
          where exams.created_by =?
          ORDER BY exams.created_at DESC";
$Texams=$conn->prepare($query);
$Texams->bind_param("i", $user_id);
$Texams->execute();
$result=$Texams->get_result();


if (!$result) {
    die("Error fetching exams: " . mysqli_error($conn));
}
$exams = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams</title>
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
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
        <h1 class="text-center">Manage Exams</h1>
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="mt-4">
            <a href="add_exam.php" class="btn btn-success mb-3">Add New Exam</a>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exam Name</th>
                        <th>Description</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($exams)): ?>
                        <?php foreach ($exams as $exam): ?>
                            <tr>
                                <td><?= htmlspecialchars($exam['id']) ?></td>
                                <td><?= htmlspecialchars($exam['name']) ?></td>
                                <td><?= htmlspecialchars($exam['description']) ?></td>
                                <td><?= htmlspecialchars($exam['course_name']) ?></td>
                                <td>
                                    <a href="edit_exam.php?id=<?= $exam['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $exam['id'] ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No exams available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <script>
        // Add event listeners to all delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const examId = this.getAttribute('data-id');
                const confirmation = confirm("Are you sure you want to delete this exam?");
                if (confirmation) {
                    // Redirect to the same page with delete_id in the query string
                    window.location.href = "manage_exam.php?delete_id=" + examId;
                }
            });
        });
    </script>
</body>
</html>