<?php
session_start();
include('../db_connection.php');

// Only Admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

$course_id = $_GET['id'];
$instructorsQuery = "SELECT id, username FROM users WHERE role_id = 2";
$instructorsResult = mysqli_query($conn, $instructorsQuery);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $created_by = $_POST['created_by'];

    $query = "UPDATE courses SET name = '$course_name', description = '$description', created_by = '$created_by' WHERE id = $course_id";
    if (mysqli_query($conn, $query)) {
        header("Location: manage_courses.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$query = "SELECT * FROM courses WHERE id = $course_id";
$result = mysqli_query($conn, $query);
$course = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Course</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="course_name" class="form-label">Course Name</label>
                <input type="text" class="form-control" id="course_name" name="course_name" value="<?= $course['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= $course['description'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="created_by" class="form-label">Instructor</label>
                <select class="form-control" id="created_by" name="created_by" required>
                    <option value="" disabled selected>Select an instructor</option>
                    <?php                     
                        while ($row = mysqli_fetch_assoc($instructorsResult)) {
                            echo "<option value='{$row['id']}'>{$row['username']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Course</button>
            <a href="manage_courses.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>