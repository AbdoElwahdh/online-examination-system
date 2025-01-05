<?php
// Include your database connection
include 'config.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view this page.");
}

$user_id = $_SESSION['user_id'];
$response = [];

// For debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['receiver_type'])) {
    $receiver_type = $_POST['receiver_type'];

    if ($receiver_type == 'admin') {
        $response = ['receiver_id' => 1];
    } elseif ($receiver_type == 'instructor') {
        // Modified query to get instructors for enrolled courses
        $query = "
            SELECT DISTINCT 
                u.id as instructor_id,
                u.username as instructor_name,
                c.id as course_id,
                c.name as course_name
            FROM users u
            JOIN courses c ON u.id = c.created_by
            JOIN enrollment e ON c.id = e.course_id
            WHERE e.user_id = ?
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // For debugging
        if (!$result) {
            error_log("MySQL Error: " . $conn->error);
            die("Query failed: " . $conn->error);
        }

        $instructors = [];
        while ($row = $result->fetch_assoc()) {
            $instructors[] = [
                'instructor_id' => $row['instructor_id'],
                'instructor_name' => $row['instructor_name'],
                'course_id' => $row['course_id'],
                'course_name' => $row['course_name']
            ];
        }

        $response = ['instructors' => $instructors];
        
        // For debugging
        error_log("Found " . count($instructors) . " instructors");
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>