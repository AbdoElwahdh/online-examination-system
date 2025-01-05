<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['exam_id'])) {
    die("No exam selected.");
}

$exam_id = intval($_GET['exam_id']);
$user_id = $_SESSION['user_id'];

// Get exam details and user's score
$query = "SELECT ee.*, e.name as exam_title, e.description as exam_description,
          (SELECT COUNT(*) FROM questions WHERE exam_id = ee.exam_id) as total_questions,
          (SELECT COUNT(*) FROM answers WHERE exam_id = ee.exam_id AND examinee_id = ee.examinee_id) as answered_questions
          FROM examinee_exams ee
          JOIN exams e ON ee.exam_id = e.id
          WHERE ee.exam_id = ? AND ee.examinee_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $exam_id, $user_id);
$stmt->execute();
$exam_result = $stmt->get_result()->fetch_assoc();

// Get questions and answers
$query = "SELECT q.id, q.question_text, a.answer_text,
          (SELECT option_text FROM options WHERE question_id = q.id AND is_correct = 1) as correct_answer
          FROM questions q
          LEFT JOIN answers a ON q.id = a.question_id AND a.examinee_id = ?
          WHERE q.exam_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $exam_id);
$stmt->execute();
$questions_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Exam Result</title>
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
            max-width: 800px;
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
            position: relative;
        }

        .exam-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .exam-description {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .score-circle {
            width: 200px;
            height: 200px;
            background: white;
            border-radius: 50%;
            margin: 2rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .score-number {
            font-size: 3rem;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 0.5rem;
        }

        .score-label {
            color: #666;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            padding: 2rem;
            background: #f8f9fc;
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

        .questions-review {
            padding: 2rem;
        }

        .question-item {
            background: #f8f9fc;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .question-item:hover {
            transform: translateX(10px);
        }

        .question-text {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .answer-comparison {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .answer-box {
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .your-answer {
            background: #f0f0f0;
            border-left: 4px solid #764ba2;
        }

        .correct-answer {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }

        .time-info {
            background: #f8f9fc;
            padding: 1rem;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
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

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .answer-comparison {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="results-container">
        <div class="results-header">
            <h1 class="exam-title"><?php echo htmlspecialchars($exam_result['exam_title']); ?></h1>
            <p class="exam-description"><?php echo htmlspecialchars($exam_result['exam_description']); ?></p>
            <div class="score-circle floating">
                <div class="score-number"><?php echo number_format($exam_result['score'], 1); ?>%</div>
                <div class="score-label">Overall Score</div>
            </div>
        </div>

        <div class="time-info">
            <p>Started: <?php echo date('M d, Y H:i', strtotime($exam_result['start_time'])); ?></p>
            <p>Completed: <?php echo date('M d, Y H:i', strtotime($exam_result['end_time'])); ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $exam_result['total_questions']; ?></div>
                <div class="stat-label">Total Questions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $exam_result['answered_questions']; ?></div>
                <div class="stat-label">Answered Questions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php 
                    $completion_rate = ($exam_result['answered_questions'] / $exam_result['total_questions']) * 100;
                    echo number_format($completion_rate, 1); 
                ?>%</div>
                <div class="stat-label">Completion Rate</div>
            </div>
        </div>

        <div class="questions-review">
            <h3>Detailed Review</h3>
            <?php while ($question = $questions_result->fetch_assoc()): ?>
                <div class="question-item">
                    <div class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></div>
                    <div class="answer-comparison">
                        <div class="answer-box your-answer">
                            <strong>Your Answer:</strong><br>
                            <?php echo $question['answer_text'] ? htmlspecialchars($question['answer_text']) : '<em>No answer provided</em>'; ?>
                        </div>
                        <div class="answer-box correct-answer">
                            <strong>Correct Answer:</strong><br>
                            <?php echo htmlspecialchars($question['correct_answer']); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div style="text-align: center;">
            <a href="Result.php" class="btn-back">Return to Exam List</a>
        </div>
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

        // Animate score number counting up
        anime({
            targets: '.score-number',
            innerHTML: [0, <?php echo number_format($exam_result['score'], 1); ?>],
            round: 10,
            duration: 2000,
            easing: 'easeInOutExpo'
        });

        // Animate stat numbers counting up
        anime({
            targets: '.stat-number',
            innerHTML: [0, (el) => el.innerHTML],
            round: 1,
            duration: 2000,
            easing: 'easeInOutExpo',
            delay: anime.stagger(200)
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>