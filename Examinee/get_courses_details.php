<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Course ID is required']);
    exit;
}

$courseId = $_GET['id'];
$userId = $_SESSION['user_id'] ?? null;

try {
    // Get course details
    $stmt = $pdo->prepare("
        SELECT c.*, u.username as creator_name,
        (SELECT COUNT(*) FROM exams WHERE course_id = c.id) as total_exams
        FROM courses c
        JOIN users u ON c.created_by = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch();

    if (!$course) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Course not found']);
        exit;
    }

    // Get exam statistics if user is logged in
    if ($userId) {
        // Get completed exams count
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT ee.exam_id) as completed_exams,
            AVG(ee.score) as average_score
            FROM examinee_exams ee
            JOIN exams e ON ee.exam_id = e.id
            WHERE e.course_id = ? AND ee.examinee_id = ?
            AND ee.score IS NOT NULL
        ");
        $stmt->execute([$courseId, $userId]);
        $stats = $stmt->fetch();
        
        $course['completed_exams'] = $stats['completed_exams'] ?? 0;
        $course['average_score'] = $stats['average_score'] ? round($stats['average_score'], 2) : 0;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $course
    ]);

} catch(PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error fetching course details: ' . $e->getMessage()
    ]);
}
?>
