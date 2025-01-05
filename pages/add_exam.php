<?php
include('../db_connection.php');
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();

}
$created_by = $_SESSION['user_id'];
// Escape input to prevent SQL injection
$created_by = mysqli_real_escape_string($conn, $created_by);

$courses_query = "SELECT id, name FROM courses WHERE created_by = '$created_by'";
$courses_result = mysqli_query($conn, $courses_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Exam Management</title>
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

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select, textarea, button {
            width: 100%;
            margin-bottom: 16px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4facfe;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #00c6ff;
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
     <div class="container">
        <h1>Create Exam</h1>
        <form id="examForm" method="POST" action="save_exam.php">
            <label for="examTitle">Exam Title:</label>
            <input type="text" id="examTitle" name="examTitle" required>
            <input type="hidden" id="hidden" name="count" style="display: none;" >

            <label for="examDescription">Exam Description:</label>
            <textarea id="examDescription" name="examDescription" rows="4" required></textarea>
            <div class="mb-3">
                <label for="Course" class="form-label">Course</label>
                <select class="form-control" id="Course" name="Course" required>
                    <option value="" disabled selected>Select a Course</option>
                    <?php
                    if (mysqli_num_rows($courses_result) > 0) {
                        while ($row = mysqli_fetch_assoc($courses_result)) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                    }            
                    else {
                        echo "<option value='' disabled>No courses available</option>";
                        }
                         ?>
                       </select>
            </div>
            <button type="button" id="addQuestionBtn">Add Question</button>
            <div id="questionsContainer"></div>
            <input type="submit" name="submit" value="save exam">
            
            <!-- <button type="submit">Save Exam</button> -->
        </form>
    </div>
    </div>

    <script>
        const questionsContainer = document.getElementById('questionsContainer');
        const addQuestionBtn = document.getElementById('addQuestionBtn');
        let questions_count = 0;
        addQuestionBtn.addEventListener('click', () => {
            const questionDiv = document.createElement('div');
            questionDiv.classList.add('question');
            questions_count+=1;
            const input = document.getElementById("hidden");
            input.value = questions_count;
            questionDiv.innerHTML = `
                <label>Question:</label>
                <textarea name="questions[]" rows="3" required></textarea>

                <label>Question Type:</label>
                <select name="questionType[]" class="question-type" required>
                    <option value="mcq">Multiple Choice</option>
                    <option value="truefalse">TrueFalse</option>
                </select>

                <div class="mcq-options">
                    <label>Options:</label>
                    <input type="text" name="options[]" placeholder="Option 1">
                    <input type="text" name="options[]" placeholder="Option 2">
                    <input type="text" name="options[]" placeholder="Option 3">
                    <input type="text" name="options[]" placeholder="Option 4">
                </div>
                <label>Correct Answer:</label>
                <textarea name="answer[]" rows="2" ></textarea>
                <button type="button" class="removeQuestionBtn">Remove Question</button>
            `;

            questionDiv.querySelector('.removeQuestionBtn').addEventListener('click', () => {
                questionDiv.remove();
            });

            questionsContainer.appendChild(questionDiv);
        });

        questionsContainer.addEventListener('change', (e) => {
            if (e.target.classList.contains('question-type')) {
                const mcqOptions = e.target.closest('.question').querySelector('.mcq-options');
                if (e.target.value === 'mcq') {
                    mcqOptions.style.display = 'block';
                } else {
                    mcqOptions.style.display = 'none';
                }
            }
        });
        
    </script>
</body>
</html>