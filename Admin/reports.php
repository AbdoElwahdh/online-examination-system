<?php
include('../db_connection.php');

// Fetch courses
$coursesQuery = "SELECT id, name FROM courses";
$coursesResult = mysqli_query($conn, $coursesQuery);
$courses = [];
while ($row = mysqli_fetch_assoc($coursesResult)) {
    $courses[] = $row;
}

// Fetch results based on the selected exam
$results = [];
if (isset($_POST['view_results']) && isset($_POST['exam'])) {
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

// Load instructors based on the selected course using Ajax
if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];

    // Fetch instructors related to the course
    $query = "SELECT u.id, u.username FROM users u
              JOIN courses c ON c.created_by = u.id
              WHERE c.id = $courseId AND u.role_id = 2"; // 2 is the role_id for instructor
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Error: ' . mysqli_error($conn));
    }

    $instructors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $instructors[] = $row;
    }

    echo json_encode($instructors);
    exit();
}

// Load exams based on the selected instructor using Ajax
if (isset($_GET['instructor_id'])) {
    $instructorId = $_GET['instructor_id'];

    // Fetch exams related to the instructor
    $query = "SELECT id, name FROM exams WHERE created_by = $instructorId";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Error: ' . mysqli_error($conn));
    }

    $exams = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $exams[] = $row;
    }

    echo json_encode($exams);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <title>View Report</title>
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
        .back-button {
            display: block;
            width: 150px;
            margin: 10px auto;
            padding: 10px;
            text-align: center;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
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
                width: 90%;
                padding: 15px;
            }

            table {
                width: 90%;
                font-size: 14px;
            }

            select {
                font-size: 14px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }

            h2 {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            form {
                width: 100%;
                padding: 10px;
            }

            table {
                width: 100%;
                font-size: 12px;
            }

            select {
                font-size: 12px;
                padding: 8px;
            }

            button {
                font-size: 12px;
                padding: 8px;
            }

            h2 {
                font-size: 18px;
            }
        }

    </style>
    <script>
        // Load instructors based on the selected course
        function loadInstructors(courseId) {
            fetch('view_results.php?course_id=' + courseId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let instructorSelect = document.getElementById('instructor');
                instructorSelect.innerHTML = '<option value="">Select Instructor</option>';
                data.forEach(instructor => {
                    let option = document.createElement('option');
                    option.value = instructor.id;
                    option.textContent = instructor.username;
                    instructorSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading instructors:', error);
            });
        }

        // Load exams based on the selected instructor
        function loadExams(instructorId) {
            fetch('view_results.php?instructor_id=' + instructorId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let examSelect = document.getElementById('exam');
                examSelect.innerHTML = '<option value="">Select Exam</option>';
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
         document.getElementById('course').addEventListener('change', function() {
         document.getElementById('hidden_course').value = this.value;
          });

         document.getElementById('instructor').addEventListener('change', function() {
         document.getElementById('hidden_instructor').value = this.value;
         });

         document.getElementById('exam').addEventListener('change', function() {
         document.getElementById('hidden_exam').value = this.value;
         });

         function validateForm(event) {
            // الحصول على القيم من الحقول
            let course = document.getElementById('course').value;
            let instructor = document.getElementById('instructor').value;
            let exam = document.getElementById('exam').value;

            // التحقق إذا كانت الحقول فارغة
            if (!course || !instructor || !exam) {
                // منع إرسال النموذج إذا كانت الحقول فارغة
                event.preventDefault();
                
                // عرض رسالة تنبيه للمستخدم
                alert('Please select a course, instructor, and exam before viewing the report.');
            }
        }
    </script>
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
<h2>View Report</h2>

<form action="course_report.php" method="GET" onsubmit="validateForm(event)">
    <label for="course">Select Course:</label>
    <select name="course" id="course" onchange="loadInstructors(this.value)">
        <option value="">Select Course</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?= $course['id'] ?>"><?= $course['name'] ?></option>
        <?php endforeach; ?>
    </select>

    <label for="instructor">Select Instructor:</label>
    <select name="instructor" id="instructor" onchange="loadExams(this.value)">
        <option value="">Select Instructor</option>
    </select>

    <label for="exam">Select Exam:</label>
    <select name="exam" id="exam">
        <option value="">Select Exam</option>
    </select>

    <button class="view-results-button" type="submit" name="view_report">View Report</button>
</form>
<a href="admin_dashboard.php" class="back-button">Back to Dashboard</a>




</div>
</body>
</html>
