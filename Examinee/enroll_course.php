<?php
session_start();
require_once('config.php');

// Set header to return JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

// Validate course_id exists in POST data
if (!isset($data['course_id'])) {
    echo json_encode(['success' => false, 'message' => 'Course ID is required']);
    exit();
}

$course_id = intval($data['course_id']);

try {
    // Start transaction
    $conn->begin_transaction();

    // Check if course exists
    $check_course = "SELECT id FROM courses WHERE id = ?";
    $stmt = $conn->prepare($check_course);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Course does not exist');
    }

    // Check if user is already enrolled
    $check_enrollment = "SELECT id FROM enrollment WHERE user_id = ? AND course_id = ?";
    $stmt = $conn->prepare($check_enrollment);
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        throw new Exception('Already enrolled in this course');
    }

    // Insert enrollment
    $enroll_query = "INSERT INTO enrollment (user_id, course_id, enroll_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($enroll_query);
    $stmt->bind_param("ii", $user_id, $course_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to enroll in course');
    }

    
    // If everything is successful, commit the transaction
    $conn->commit();

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Successfully enrolled in course',
        'enrollment_id' => $conn->insert_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connections
$stmt->close();
$conn->close();
?>