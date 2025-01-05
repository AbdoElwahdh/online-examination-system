<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
include '../db_connection.php';


// Fetch courses
$courses_query =$conn->prepare("SELECT id, name FROM courses where created_by = ?");
$courses_query->bind_param("i", $user_id);
$courses_query->execute();
$courses=$courses_query->get_result();


$results = [];
$errorMessage = "";  

// Check if the form was submitted
if (isset($_POST['view_results'])) {
    if (empty($_POST['course']) || empty($_POST['exam'])) {
        // Set an error message if any of the fields are not selected
        $errorMessage = "Please select all fields: Course, Instructor, and Exam.";
    } else {
        // If all fields are selected, fetch results
        $examId = $_POST['exam'];

        // Fetch exam results
        $query = "SELECT u.username, u.email, ee.score 
                  FROM examinee_exams ee
                  JOIN users u ON ee.examinee_id = u.id
                  WHERE ee.exam_id = $examId";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $results[] = $row;
            }
        }
    }
}

// Load exams based on the selected course using Ajax
if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];

    // Fetch instructors related to the course
    $query = "SELECT id, name FROM exams
              WHERE course_id = $courseId and created_by=$user_id"; // 2 is the role_id for instructor
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Error: ' . mysqli_error($conn));
    }

    $available_exams = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $available_exams[] = $row;
    }

    echo json_encode($available_exams);
    exit();
}

// Load exams based on the selected course using Ajax
// if (isset($_GET['course_id'])) {
//     $instructorId = $_GET['course_id'];

//     // Fetch exams related to the instructor
//     $query = "SELECT id, name FROM exams WHERE course_id = $instructorId";
//     $result = mysqli_query($conn, $query);

//     if (!$result) {
//         die('Error: ' . mysqli_error($conn));
//     }

//     $exams = [];
//     while ($row = mysqli_fetch_assoc($result)) {
//         $exams[] = $row;
//     }

//     echo json_encode($exams);
//     exit();
// }
// ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <title>View Results</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        form {
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f4f4f9;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e0f7fa;
        }

        .exam-results-header {
            text-align: center;
            color: #4CAF50;
            font-weight: bold;
        }

        .view-results-button {
            color: #fff;
            background-color: #4CAF50;
            font-weight: bold;
        }
        @media (max-width: 768px) {
        form {
            width: 95%;
            padding: 15px;
        }

        table {
            width: 95%;
            font-size: 14px;
        }

        th, td {
            padding: 8px;
        }

        button {
            padding: 10px;
            font-size: 14px;
        }
     }

     @media (max-width: 480px) {
        form {
            width: 100%;
        }

        table {
            width: 100%;
            font-size: 12px;
        }

        th, td {
            padding: 6px;
        }

        button {
            padding: 8px;
            font-size: 12px;
        }

        h2, h3 {
            font-size: 18px;
        }
     }

    </style>
    <script>
        // Load instructors based on the selected course
        function loadExams(courseId) {
            fetch('view_results.php?course_id=' + courseId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let examSelect = document.getElementById('exam');
                examSelect.innerHTML = '<option value="">Select Exams</option>';
                data.forEach(exam => {
                    let option = document.createElement('option');
                    option.value = exam.id;
                    option.textContent = exam.name;
                    examSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading exams:', error);
            });
        }

        // Load exams based on the selected instructor
        // function loadExams(instructorId) {
        //     fetch('view_results.php?instructor_id=' + instructorId)
        //     .then(response => {
        //         if (!response.ok) {
        //             throw new Error('Network response was not ok');
        //         }
        //         return response.json();
        //     })
        //     .then(data => {
        //         let examSelect = document.getElementById('exam');
        //         examSelect.innerHTML = '<option value="">Select Exam</option>';
        //         data.forEach(exam => {
        //             let option = document.createElement('option');
        //             option.value = exam.id;
        //             option.textContent = exam.name;
        //             examSelect.appendChild(option);
        //         });
        //     })
        //     .catch(error => {
        //         console.error('Error loading exams:', error);
        //     });
        // }
    </script>
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
<h2>View Results</h2>

<form action="view_results.php" method="POST">
    <label for="course">Select Course:</label>
    <select name="course" id="course" onchange="loadExams(this.value)">
        <option value="">Select Course</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?= $course['id'] ?>"><?= $course['name'] ?></option>
        <?php endforeach; ?>
    </select>

    <!-- <label for="instructor">Select Instructor:</label>
    <select name="instructor" id="instructor" onchange="loadExams(this.value)">
        <option value="">Select Instructor</option>
    </select> -->

    <label for="exam">Select Exam:</label>
    <select name="exam" id="exam">
        <option value="">Select Exam</option>
    </select>

    <button class="view-results-button" type="submit" name="view_results">View Results</button>
</form>

<?php if (isset($results) && !empty($results)): ?>
    <h3 class="exam-results-header">Exam Results</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Score</th>
        </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= $row['username'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['score'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php elseif (isset($_POST['view_results'])): ?>
    <p>No results found.</p>
<?php endif; ?>
<?php if ($errorMessage): ?>
    <div style="color: red; text-align: center; font-weight: bold;">
        <?= $errorMessage ?>
    </div>
<?php endif; ?>

</div>
</body>
</html>
