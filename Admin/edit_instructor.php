<?php
session_start();
include('../db_connection.php');

// Only Admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

$instructor_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $query = "UPDATE users SET username = '$username', email = '$email' WHERE id = $instructor_id";
    if (mysqli_query($conn, $query)) {
        header("Location: manage_instructors.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$query = "SELECT * FROM users WHERE id = $instructor_id";
$result = mysqli_query($conn, $query);
$instructor = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <title>Edit Instructor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Instructor</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $instructor['username'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $instructor['email'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Instructor</button>
            <a href="manage_instructors.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>