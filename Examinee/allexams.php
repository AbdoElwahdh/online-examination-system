<?php
session_start();
include('config.php');

// Only students can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Query to get only exams that haven't been taken by the user
$query = "SELECT e.id, e.name, e.duration, e.description, 
          (SELECT COUNT(*) FROM questions WHERE exam_id = e.id) as question_count
          FROM exams e
          WHERE NOT EXISTS (
              SELECT 1 
              FROM examinee_exams ee 
              WHERE ee.exam_id = e.id 
              AND ee.examinee_id = ?
          )
          and e.course_id in (select course_id from enrollment where user_id=$user_id and accepted =1)
          ORDER BY e.id DESC";

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
    <title>Available Exams</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            color: #2d3748;
        }

        .header h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .header p {
            color: #4a5568;
            font-size: 1.1rem;
        }

        .exam-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .exam-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .exam-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
        }

        .exam-title {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .exam-body {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .exam-info {
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
            color: #4a5568;
        }

        .info-item i {
            margin-right: 0.5rem;
            color: #667eea;
            width: 20px;
        }

        .exam-description {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .exam-footer {
            padding: 1.5rem;
            background: #f8f9fc;
            border-top: 1px solid #e2e8f0;
        }

        .btn-start {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
        }

        .btn-start:hover {
            transform: translateY(-2px);
        }

        .btn-start i {
            margin-left: 0.5rem;
        }

        .no-exams {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .no-exams i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .no-exams h3 {
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .no-exams p {
            color: #718096;
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

        @media (max-width: 768px) {
            .exam-grid {
                grid-template-columns: 1fr;
            }

            .header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Available Exams</h2>
            <p>Start a new exam from the list below</p>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="exam-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="exam-card">
                        <div class="exam-header">
                            <h3 class="exam-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        </div>
                        
                        <div class="exam-body">
                            <div class="exam-info">
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Duration: <?php echo $row['duration']; ?> minutes</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-question-circle"></i>
                                    <span>Questions: <?php echo $row['question_count']; ?></span>
                                </div>
                            </div>
                            
                            <?php if (isset($row['description'])): ?>
                                <p class="exam-description"><?php echo htmlspecialchars($row['description']); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="exam-footer">
                            <form method="get" action="exam.php">
                                <input type="hidden" name="exam_id" value="<?php echo $row['id']; ?>">
                                <button class="btn-start" type="submit">
                                    Start Exam
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-exams">
                <i class="fas fa-check-circle"></i>
                <h3>All Caught Up!</h3>
                <p>You've completed all available exams. Check back later for new ones.</p>
            </div>
        <?php endif; ?>
        <div style="text-align: center;">
            <a href="dashboard.php" class="btn-back">Return to Home</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>