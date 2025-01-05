<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all exams taken by the user with exam details
$query = "SELECT ee.*, e.name as exam_title, 
          COUNT(DISTINCT q.id) as total_questions,
          (SELECT COUNT(*) 
           FROM answers a 
           JOIN options o ON a.question_id = o.question_id 
           WHERE a.examinee_id = ee.examinee_id 
           AND a.exam_id = ee.exam_id 
           AND a.answer_text = o.option_text 
           AND o.is_correct = 1) as correct_answers
          FROM examinee_exams ee
          JOIN exams e ON ee.exam_id = e.id
          LEFT JOIN questions q ON e.id = q.exam_id
          WHERE ee.examinee_id = ?
          GROUP BY ee.id
          ORDER BY ee.end_time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Exam Results</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .results-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
        }

        .results-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .results-summary {
            padding: 2rem;
            text-align: center;
            background: #f8f9fc;
        }

        .results-grid {
            display: grid;
            gap: 1.5rem;
            padding: 2rem;
        }

        .exam-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 1rem;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .exam-card:hover {
            transform: translateX(10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .exam-title {
            font-weight: 600;
            color: #333;
        }

        .exam-score {
            font-size: 1.5rem;
            font-weight: bold;
            color: #764ba2;
            text-align: center;
        }

        .exam-date {
            color: #666;
            font-size: 0.9rem;
            text-align: right;
        }

        .view-details {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: transform 0.3s ease;
            text-align: center;
        }

        .view-details:hover {
            transform: translateY(-2px);
        }

        .performance-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .outstanding {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .good {
            background: #fff3e0;
            color: #f57c00;
        }

        .needs-improvement {
            background: #ffebee;
            color: #c62828;
        }

        .no-exams {
            text-align: center;
            padding: 3rem;
            color: #666;
            font-size: 1.1rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="results-container">
        <div class="results-header">
            <h2>My Exam History</h2>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="results-summary">
                <?php
                $total_exams = $result->num_rows;
                $total_score = 0;
                $highest_score = 0;
                $result_data = [];
                
                while ($row = $result->fetch_assoc()) {
                    $total_score += $row['score'];
                    $highest_score = max($highest_score, $row['score']);
                    $result_data[] = $row;
                }
                
                $average_score = $total_score / $total_exams;
                ?>
                
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_exams; ?></div>
                        <div class="stat-label">Total Exams Taken</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo round($average_score, 1); ?>%</div>
                        <div class="stat-label">Average Score</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo round($highest_score, 1); ?>%</div>
                        <div class="stat-label">Highest Score</div>
                    </div>
                </div>
            </div>

            <div class="results-grid">
                <?php foreach ($result_data as $row): ?>
                    <div class="exam-card">
                        <div>
                            <div class="exam-title"><?php echo htmlspecialchars($row['exam_title']); ?></div>
                            <?php
                            $performance_class = '';
                            $performance_text = '';
                            if ($row['score'] >= 90) {
                                $performance_class = 'outstanding';
                                $performance_text = 'Outstanding';
                            } elseif ($row['score'] >= 75) {
                                $performance_class = 'good';
                                $performance_text = 'Good';
                            } else {
                                $performance_class = 'needs-improvement';
                                $performance_text = 'Needs Improvement';
                            }
                            ?>
                            <span class="performance-badge <?php echo $performance_class; ?>">
                                <?php echo $performance_text; ?>
                            </span>
                        </div>
                        <div class="exam-score">
                            <?php echo round($row['score'], 1); ?>%
                        </div>
                        <div class="exam-date">
                            <?php echo date('M d, Y', strtotime($row['end_time'])); ?><br>
                            <?php echo date('h:i A', strtotime($row['end_time'])); ?>
                        </div>
                        <a href="exam_result_detail.php?exam_id=<?php echo $row['exam_id']; ?>" class="view-details">
                            View Details
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-exams">
                <p>You haven't taken any exams yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Animate the results container on load
        anime({
            targets: '.results-container',
            opacity: [0, 1],
            translateY: [20, 0],
            duration: 1000,
            easing: 'easeOutExpo'
        });

        // Animate numbers counting up
        anime({
            targets: '.stat-number',
            innerHTML: [0, el => el.innerHTML],
            round: 10,
            duration: 2000,
            easing: 'easeInOutExpo'
        });
    </script>
</body>
</html>
