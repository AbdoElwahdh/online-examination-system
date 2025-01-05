<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$examinee_id = $_SESSION['user_id'];

// Get overall statistics
$stats_query = "SELECT 
    COUNT(DISTINCT exam_id) as total_exams,
    AVG(score) as average_score,
    MAX(score) as highest_score,
    MIN(score) as lowest_score
FROM examinee_exams 
WHERE examinee_id = ?";

$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("i", $examinee_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result()->fetch_assoc();

// Get detailed exam results with additional information
$query = "SELECT 
    ee.*, 
    e.name as exam_title, 
    e.description as exam_description,
    ee.score as passing_score,
    (SELECT COUNT(*) FROM answers WHERE exam_id = ee.exam_id AND examinee_id = ee.examinee_id) as total_answers,
    (SELECT COUNT(*) FROM questions WHERE exam_id = ee.exam_id) as total_questions
FROM examinee_exams ee
JOIN exams e ON ee.exam_id = e.id
WHERE ee.examinee_id = ?
ORDER BY ee.end_time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $examinee_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --success-color: #2ec4b6;
            --info-color: #3a86ff;
            --warning-color: #ff9f1c;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .stats-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
        }

        .result-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            background: white;
        }

        .result-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }

        .badge-custom {
            font-size: 0.85em;
            padding: 0.5em 1em;
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-outline-primary {
            border-radius: 8px;
            padding: 0.5em 1.2em;
            border-width: 2px;
        }

        .animation-fade-in {
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 25px 25px;
        }

        .stat-icon {
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .btn-back {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            margin: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <h1 class="text-center mb-0">Exam Results Dashboard</h1>
        </div>
    </div>

    <div class="container py-4">
        <!-- Statistics Summary -->
        <div class="row mb-5 animation-fade-in">
            <div class="col-md-3 mb-4">
                <div class="card stats-card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                        <h5>Total Exams</h5>
                        <h3><?php echo $stats_result['total_exams']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card stats-card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h5>Average Score</h5>
                        <h3><?php echo number_format($stats_result['average_score'], 1); ?>%</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card stats-card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                        <h5>Highest Score</h5>
                        <h3><?php echo number_format($stats_result['highest_score'], 1); ?>%</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card stats-card bg-warning text-white h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-chart-bar fa-2x"></i>
                        </div>
                        <h5>Lowest Score</h5>
                        <h3><?php echo number_format($stats_result['lowest_score'], 1); ?>%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Results -->
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): 
                    $completion_rate = ($row['total_answers'] / $row['total_questions']) * 100;
                    $status_class = $row['score'] >= $row['passing_score'] ? 'success' : 'danger';
                ?>
                    <div class="col-md-6 mb-4 animation-fade-in">
                        <div class="card result-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['exam_title']); ?></h5>
                                    <span class="badge bg-<?php echo $status_class; ?> badge-custom">
                                        <?php echo $row['score'] >= $row['passing_score'] ? 'Passed' : 'Failed'; ?>
                                    </span>
                                </div>
                                
                                <p class="text-muted small mb-3">
                                    <?php echo htmlspecialchars($row['exam_description']); ?>
                                </p>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <label class="small text-muted">Completion Rate</label>
                                        <span class="small text-muted"><?php echo number_format($completion_rate, 1); ?>%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: <?php echo $completion_rate; ?>%"
                                             aria-valuenow="<?php echo $completion_rate; ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <small class="text-muted d-block mb-1">Score</small>
                                        <h5 class="mb-0"><?php echo number_format($row['score'], 1); ?>%</h5>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block mb-1">Passing Score</small>
                                        <h5 class="mb-0"><?php echo $row['passing_score']; ?>%</h5>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block mb-1">Questions</small>
                                        <h5 class="mb-0"><?php echo $row['total_questions']; ?></h5>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i> 
                                        <?php echo date('M d, Y H:i', strtotime($row['end_time'])); ?>
                                    </small>
                                    <a href="view_exam_result.php?exam_id=<?php echo $row['exam_id']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        View Details <i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center p-5 animation-fade-in">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4>No Exams Taken Yet</h4>
                <p class="mb-3">You haven't taken any exams yet. Start taking available exams from the main menu.</p>
                <a href="allexams.php" class="btn btn-primary">View Available Exams</a>
            </div>
        <?php endif; ?>
        <div style="text-align: center;">
            <a href="dashboard.php" class="btn-back">Return to Home</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$stats_stmt->close();
$conn->close();
?>