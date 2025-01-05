<?php
// Ensure the session is started to access the user session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require('config.php');

// Check if the exam_id is set and valid
if (isset($_GET['exam_id'])) {
    $exam_id = $_GET['exam_id']; // Retrieve the exam_id from the URL
} else {
    echo "Exam ID is not set.";
    exit();
}

// Function to get options for each question
function get_options($question_id) {
    global $conn;
    // Query to get options for a given question
    $query = "SELECT option_text, is_correct FROM options WHERE question_id = $question_id";
    $result = mysqli_query($conn, $query);
    return $result; // Return the options result
}

// Query to get questions based on the exam_id
$query = "SELECT id, question_text, question_type FROM questions WHERE exam_id = $exam_id";
$questions_result = mysqli_query($conn, $query);

// Check if questions exist for the given exam
if (mysqli_num_rows($questions_result) > 0) {
    // Store the questions in an array
    $questions = mysqli_fetch_all($questions_result, MYSQLI_ASSOC);
} else {
    echo "No questions found for this exam.";
    exit();
}
?>
