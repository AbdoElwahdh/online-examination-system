<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();

}
// Include the database connection
include '../db_connection.php';

$user_id=$_SESSION['user_id'];
if (!isset($_GET['id'])) {
    die("Exam ID is missing.");
}

$exam_id = intval($_GET['id']);

// Fetch the exam details
$query = "SELECT * FROM exams WHERE id = $exam_id";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Exam not found.");
}
$exam = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $course_id = intval($_POST['course_id']);

    $query = "UPDATE exams SET name = '$name', description = '$description', course_id = $course_id WHERE id = $exam_id";
    if (mysqli_query($conn, $query)) {
        header("Location: manage_exam.php");
        exit;
    } else {
        die("Error updating exam: " . mysqli_error($conn));
    }
}

// Fetch courses
$courses_query =$conn->prepare("SELECT id, name FROM courses where created_by = ?");
$courses_query->bind_param("i", $user_id);
$courses_query->execute();
$courses=$courses_query->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Exam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
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
    
    <div class="container mt-5">
        <h1 class="text-center">Edit Exam</h1>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="name" class="form-label">Exam Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($exam['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" required><?= htmlspecialchars($exam['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="course_id" class="form-label">Course</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <?php while ($course = mysqli_fetch_assoc($courses)): ?>
                        <option value="<?= $course['id'] ?>" <?= $course['id'] == $exam['course_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Update Exam</button>
        </form>
    </div>
</body>
</html>