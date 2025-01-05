<?php 
session_start();
include('../db_connection.php');

// Only Admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}
$user=$_SESSION['user_id'];
// Fetching all feedbacks sent to instructor
$feedbackQuery = "SELECT feedbacks.id, feedbacks.feedback_text, feedbacks.response, users.username, users.role_id, courses.name AS course_name 
                  FROM feedbacks
                  JOIN users ON feedbacks.user_id = users.id
                  JOIN courses ON feedbacks.course_id = courses.id
                  WHERE feedbacks.receiver_id = $user ORDER BY feedbacks.created_at DESC";
$feedbackResult = mysqli_query($conn, $feedbackQuery);

// Handling the response submission
if (isset($_POST['response_submit'])) {
    $feedback_id = $_POST['feedback_id'];
    $response_text = mysqli_real_escape_string($conn, $_POST['response_text']);
    $updateQuery = "UPDATE feedbacks SET response = '$response_text' WHERE id = $feedback_id";
    mysqli_query($conn, $updateQuery);
    header("Location: feedbacks.php"); // Redirect to the same page after updating
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <title>Admin - Feedbacks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .feedback-card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            margin-bottom: 20px;
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .feedback-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }
        .feedback-card h5 {
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 18px;
        }
        .feedback-card p {
            font-size: 16px;
        }
        .btn {
            border-radius: 8px;
            padding: 10px 15px;
            background-color: #4e73df;
            color: #fff;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #2e59d9;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px;
            font-size: 16px;
        }
        .response-area {
            margin-top: 20px;
        }
        .response-card {
            background-color: #e2e3e5;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
        }


    .menu-item a {
        font-size: 14px;
    }

    .btn {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .feedback-card {
        padding: 15px;
    }

    .feedback-card h5 {
        font-size: 16px;
    }

    .feedback-card p {
        font-size: 14px;
    }

    .form-control {
        font-size: 14px;
    }

    .btn {
        padding: 8px 12px;
    }
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
        <h1>Feedbacks Overview</h1>

        <!-- Display feedbacks in cards -->
        <?php while ($feedback = mysqli_fetch_assoc($feedbackResult)) { ?>
        <div class="feedback-card">
            <h5>
                <?php 
                // Displaying user name and role (student or instructor)
                echo $feedback['username'] . " (" . ($feedback['role_id'] == 2 ? "Student" : "Instructor") . ")";
                ?>
            </h5>
            <p><strong>Course:</strong> <?= $feedback['course_name'] ?></p>
            <p><strong>Feedback:</strong> <?= $feedback['feedback_text'] ?></p>
            <p><strong>Response:</strong></p>
            <div class="response-card">
                <?= $feedback['response'] ? $feedback['response'] : "No response yet." ?>
            </div>

            <!-- Response form -->
            <?php if ($feedback['response'] == NULL) { ?>
            <div class="response-area">
                <form action="feedbacks.php" method="POST">
                    <input type="hidden" name="feedback_id" value="<?= $feedback['id'] ?>">
                    <textarea name="response_text" class="form-control" rows="4" placeholder="Enter your response here..." required></textarea>
                    <button type="submit" name="response_submit" class="btn mt-3">Submit Response</button>
                </form>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</body>
</html>
