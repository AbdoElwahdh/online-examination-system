<?php
session_start();
include('config.php');

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

// Retrieve the exam_id from the URL
if (!isset($_GET['exam_id'])) {
    die("No exam selected.");
}
$exam_id = intval($_GET['exam_id']); // Convert to integer for safety

// Retrieve all questions for the exam
$query = "SELECT id, question_text FROM questions WHERE exam_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[$row['id']] = $row['question_text'];
}

// Initialize variables for scoring
$total_questions = count($questions);
$correct_answers = 0;
$submitted_answers = [];
$start_time =$_SESSION['start_time'];
// Process each submitted answer
foreach ($questions as $question_id => $question_text) {
    if (isset($_POST['q' . $question_id])) {
        $submitted_answers[$question_id] = $_POST['q' . $question_id];

        // Fetch the correct answer from the database
        $query = "SELECT option_text FROM options WHERE question_id = ? AND is_correct = 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $correct_result = $stmt->get_result();
        $correct_option = $correct_result->fetch_assoc()['option_text'];
        
        // Check if the submitted answer matches the correct answer
        if ($_POST['q' . $question_id] === $correct_option) {
            $correct_answers++;
        }
        // Insert the answer into the `answers` table
        $user_id = $_SESSION['user_id'];
        $answer_text = $_POST['q' . $question_id];

        $insert_answer_query = "INSERT INTO answers (examinee_id, exam_id, question_id, answer_text) 
                        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_answer_query);
        $stmt->bind_param("iiis", $user_id, $exam_id, $question_id, $answer_text);
        $stmt->execute();

    
    }
}

// Calculate the score
$score = ($total_questions > 0) ? ($correct_answers / $total_questions) * 100 : 0;

// Display results
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
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

        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #fff;
            opacity: 0;
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
            grid-template-columns: repeat(2, 1fr);
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

        .performance-message {
            text-align: center;
            padding: 1rem;
            color: #666;
            font-size: 1.1rem;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body>
 
    <?php
    // Your existing PHP code here
    $score = isset($score) ? $score : 85;
    $correct_answers = isset($correct_answers) ? $correct_answers : 17;
    $total_questions = isset($total_questions) ? $total_questions : 20;
    
    // Fetch all correct answers at once and store them in an array
    $correct_answers_array = [];
    foreach ($questions as $question_id => $_) {
        $query = "SELECT option_text FROM options WHERE question_id = ? AND is_correct = 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $correct_result = $stmt->get_result();
        $correct_answers_array[$question_id] = $correct_result->fetch_assoc()['option_text'];
    }

    $user_id = $_SESSION['user_id'];

    $query_ins = "INSERT INTO examinee_exams (examinee_id, exam_id, start_time, end_time, score) 
              VALUES ($user_id, $exam_id, '$start_time', NOW(), $score)";
    mysqli_query($conn, $query_ins);

    ?>
    <div class="results-container">
        <div class="results-header">
            <h2>Your Exam Results</h2>
            <div class="score-circle floating">
                <div class="score-number"><?php echo round($score, 1); ?>%</div>
                <div class="score-label">Overall Score</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $correct_answers; ?></div>
                <div class="stat-label">Correct Answers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_questions - $correct_answers; ?></div>
                <div class="stat-label">Areas for Improvement</div>
            </div>
        </div>

        <div class="performance-message">
            <?php
            if ($score >= 90) {
                echo "Outstanding performance! You've mastered this subject! 🌟";
            } elseif ($score >= 75) {
                echo "Great job! You're showing strong understanding! 👏";
            } elseif ($score >= 60) {
                echo "Good effort! Keep practicing to improve further! 💪";
            } else {
                echo "Keep going! With more practice, you'll improve! 🎯";
            }
            ?>
        </div>

        <div class="questions-review">
            <h3>Detailed Review</h3>
            <?php foreach ($questions as $question_id => $question_text): ?>
                <div class="question-item">
                    <div class="question-text"><?php echo $question_text; ?></div>
                    <div class="answer-comparison">
                        <div class="answer-box your-answer">
                            <strong>Your Answer:</strong><br>
                            <?php echo isset($submitted_answers[$question_id]) ? $submitted_answers[$question_id] : '<em>No answer provided</em>'; ?>
                        </div>
                        <div class="answer-box correct-answer">
                            <strong>Correct Answer:</strong><br>
                            <?php echo $correct_answers_array[$question_id]; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


        <div style="text-align: center;">
            <a href="dashboard.php" class="btn-back">Return to Exam List</a>
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

        // Create confetti effect for high scores
        if (<?php echo $score; ?> >= 80) {
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                document.querySelector('.results-header').appendChild(confetti);
                
                anime({
                    targets: confetti,
                    left: anime.random(0, 100) + '%',
                    top: anime.random(0, 100) + '%',
                    opacity: [0, 1, 0],
                    scale: anime.random(1, 2),
                    rotate: anime.random(0, 360),
                    duration: anime.random(1000, 2000),
                    delay: anime.random(0, 1000),
                    loop: true
                });
            }
        }

        // Animate score number counting up
        anime({
            targets: '.score-number',
            innerHTML: [0, <?php echo round($score, 1); ?>],
            round: 10,
            duration: 2000,
            easing: 'easeInOutExpo'
        });
    </script>
</body>
</html>