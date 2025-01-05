<?php
// Database connection (modify with your own credentials)
include '../db_connection.php';
// Assuming you have a session for the logged-in user
session_start();
$user_id = $_SESSION['user_id'];  // User ID of the logged-in user

// Fetch the course IDs the user is enrolled in
$sql = "SELECT course_id FROM Enrollment WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$course_ids = [];
while ($row = $result->fetch_assoc()) {
    $course_ids[] = $row['course_id'];
}

if (empty($course_ids)) {
    echo json_encode([]);  // No courses found, return an empty array
    exit;
}

// Fetch instructors for these courses
$course_ids_placeholder = implode(",", array_fill(0, count($course_ids), "?"));
$sql_instructors = "
    SELECT u.id, u.username
    FROM users u
    JOIN courses c ON u.id = c.created_by
    WHERE c.id IN ($course_ids_placeholder)
";

$stmt_instructors = $conn->prepare($sql_instructors);
$stmt_instructors->bind_param(str_repeat("i", count($course_ids)), ...$course_ids);
$stmt_instructors->execute();
$result_instructors = $stmt_instructors->get_result();

$instructors = [];
while ($row = $result_instructors->fetch_assoc()) {
    $instructors[] = $row;
}

echo json_encode($instructors);  // Return the instructors as JSON

$conn->close();
?>
