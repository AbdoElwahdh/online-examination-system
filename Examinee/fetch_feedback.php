<?php
// Database connection
include '../db_connection.php';

// Fetch all feedback
$sql = "SELECT f.id, f.feedback_text, f.created_at, u.username AS user_name, r.username AS receiver_name
        FROM feedbacks f
        LEFT JOIN users u ON f.user_id = u.id
        LEFT JOIN users r ON f.receiver = r.id
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $feedbacks = [];
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
} else {
    $feedbacks = [];
}

$conn->close();
?>
