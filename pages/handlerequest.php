<?php
// Enable error reporting for debugging
ini_set('display_errors', 1); // Enable error output temporarily
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', __DIR__ . '/php_errors2.log'); // Log errors to a file
error_reporting(E_ALL); // Report all errors
header('Content-Type: application/json'); // Ensure JSON response

try {
    // Include database connection
    include '../db_connection.php';

    // Parse JSON input
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    $requesterId = $data['requesterId'] ?? null;
    $action = $data['action'] ?? null;

    if (!$requesterId || !$action) {
        throw new Exception('Invalid parameters.');
    }

    if ($action === 'approve') {
        // Update the `is_accepted` column in the database
        $stmt = $conn->prepare('UPDATE enrollment SET accepted = 1 WHERE user_id = ?');
        $stmt->bind_param('i', $requesterId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Request approved.']);
        } else {
            throw new Exception('Failed to approve request.');
        }
    } elseif ($action === 'reject') {
        // Delete the record from the database
        $stmt = $conn->prepare('DELETE FROM enrollment WHERE user_id = ?');
        $stmt->bind_param('i', $requesterId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Request rejected.']);
        } else {
            throw new Exception('Failed to reject request.');
        }
    } else {
        throw new Exception('Invalid action.');
    }
} catch (Exception $e) {
    // Log the error and return a JSON response
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

