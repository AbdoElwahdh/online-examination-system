<?php
session_start();

// Include your database connection
include 'config.php';

// For debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit feedback.");
}

$user_id = $_SESSION['user_id'];
$feedback_text = isset($_POST['feedback_text']) ? $_POST['feedback_text'] : 'didnt get response';
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 1;
$course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 1;

// For debugging
error_log("Received feedback - User: $user_id, Receiver: $receiver_id, Course: $course_id");

// Validate inputs
if (empty($feedback_text) || !$receiver_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Insert feedback
$query = "INSERT INTO feedbacks (user_id, course_id, feedback_text, receiver_id, created_at)
          VALUES (?, ?, ?, ?, NOW())";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("iisi", $user_id, $course_id, $feedback_text, $receiver_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Feedback submitted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error submitting feedback']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing query']);
}

$conn->close();
?>