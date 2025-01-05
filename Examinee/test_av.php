<?php
session_start();
require_once 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$examinee_id = $_SESSION['user_id'];

// Get user info
$user_query = "SELECT name, email, profile_image FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $examinee_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();

// Get comprehensive statistics
$stats_query = "SELECT 
    COUNT(DISTINCT exam_id) as total_exams,
    AVG(score) as average_score,
    MAX(score) as highest_score,
    MIN(score) as lowest_score,
    COUNT(CASE WHEN score >= e.passing_score THEN 1 END) as passed_exams,
    AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration,
    DATE_FORMAT(MIN(start_time), '%Y-%m-%d') as first_exam_date,
    (SELECT COUNT(*) FROM answers WHERE examinee_id = ee.examinee_id) as total_answers
FROM examinee_exams ee
JOIN exams e ON ee.exam_id = e.id
WHERE ee.examinee_id = ?";

$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("i", $examinee_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

// Get monthly performance data for chart
$monthly_query = "SELECT 
    DATE_FORMAT(end_time, '%Y-%m') as month,
    AVG(score) as avg_score,
    COUNT(*) as exams_count
FROM examinee_exams
WHERE examinee_id = ?
GROUP BY DATE_FORMAT(end_time, '%Y-%m')
ORDER BY month DESC
LIMIT 6";

$monthly_stmt = $conn->prepare($monthly_query);
$monthly_stmt->bind_param("i", $examinee_id);
$monthly_stmt->execute();
$monthly_result = $monthly_stmt->get_result();
$monthly_data = [];
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_data[] = $row;
}

// Get recent exam results with detailed info
$recent_query = "SELECT 
    ee.*,
    e.exam_title,
    e.exam_description,
    e.passing_score,
    e.total_questions,
    c.category_name,
    (SELECT COUNT(*) FROM answers WHERE exam_id = ee.exam_id AND examinee_id = ee.examinee_id) as answered_questions,
    (SELECT COUNT(*) FROM answers a 
     JOIN questions q ON a.question_id = q.id 
     WHERE a.exam_id = ee.exam_id AND a.examinee_id = ee.examinee_id AND a.answer_text = q.correct_answer) as correct_answers
FROM examinee_exams ee
JOIN exams e ON ee.exam_id = e.id
JOIN categories c ON e.category_id = c.id
WHERE ee.examinee_id = ?
ORDER BY ee.end_time DESC";

$recent_stmt = $conn->prepare($recent_query);
$recent_stmt->bind_param("i", $examinee_id);
$recent_stmt->execute();
$recent_results = $recent_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | نتائج الاختبارات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f1c40f;
            --text-color: #2c3e50;
            --bg-color: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 2rem 2rem;
            box-shadow: var(--card-shadow);
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: var(--card-shadow);
        }

        .stats-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .chart-container {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .result-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            margin: 1rem 0;
        }

        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 500;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0.5rem 0;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .category-badge {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.8rem;
        }

        .performance-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .exam-stats {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
        }

        .exam-stat-item {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: rgba(0,0,0,0.05);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .shine {
            position: relative;
            overflow: hidden;
        }

        .shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255,255,255,0) 0%,
                rgba(255,255,255,0.3) 50%,
                rgba(255,255,255,0) 100%
            );
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            from { transform: translateX(-100%) rotate(30deg); }
            to { transform: translateX(100%) rotate(30deg); }
        }
    </style>
</head>
<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="profile-section">
                        <img src="<?php echo htmlspecialchars($user_info['profile_image'] ?? 'assets/default-profile.png'); ?>" 
                             alt="Profile" class="profile-image">
                        <div>
                            <h1 class="h3 mb-1"><?php echo htmlspecialchars($user_info['name']); ?></h1>
                            <p class="mb-0"><i class="fas fa-graduation-cap me-2"></i>طالب</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex justify-content-md-end align-items-center gap-3">
                        <div class="text-end">
                            <h4 class="mb-1"><?php echo number_format($stats['average_score'], 1); ?>%</h4>
                            <p class="mb-0">متوسط الدرجات</p>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-1"><?php echo $stats['total_exams']; ?></h4>
                            <p class="mb-0">إجمالي الاختبارات</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card fade-in" style="animation-delay: 0.1s">
                    <i class="fas fa-trophy fa-2x mb-2" style="color: var(--success-color)"></i>
                    <div class="stat-value"><?php echo $stats['passed_exams']; ?></div>
                    <div class="stat-label">الاختبارات المجتازة</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card fade-in" style="animation-delay: 0.2s">
                    <i class="fas fa-clock fa-2x mb-2" style="color: var(--warning-color)"></i>
                    <div class="stat-value"><?php echo round($stats['avg_duration']); ?></div>
                    <div class="stat-label">متوسط مدة الاختبار (دقيقة)</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card fade-in" style="animation-delay: 0.3s">
                    <i class="fas fa-check-circle fa-2x mb-2" style="color: var(--secondary-color)"></i>
                    <div class="stat-value"><?php echo $stats['total_answers']; ?></div>
                    <div class="stat-label">إجمالي الإجابات</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card fade-in" style="animation-delay: 0.4s">
                    <i class="fas fa-star fa-2x mb-2" style="color: var(--accent-color)"></i>
                    <div class="stat-value"><?php echo number_format($stats['highest_score'], 1); ?>%</div>
                    <div class="stat-label">أعلى درجة</div>
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="chart-container fade-in" style="animation-delay: 0.5s">
            <h3 class="mb-4">تطور الأداء</h3>
            <canvas id="performanceChart" height="100"></canvas>
        </div>

        <!-- Recent Results -->
        <h3 class="mb-4">نتائج الاختبارات</h3>
        <?php while ($row = $recent_results->fetch_assoc()): 
            $completion_rate = ($row['answered_questions'] / $row['total_questions']) * 100;
            $accuracy_rate = ($row['correct_answers'] / $row['answered_questions']) * 100;
            $passed = $row['score'] >= $row['passing_score'];
        ?>
            <div class="result-card fade-in">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="category-badge mb-2"><?php echo htmlspecialchars($row['category_name']); ?></span>
                        <h4 class="mb-1"><?php echo htmlspecialchars($row['exam_title']); ?></h4>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($row['exam_description']); ?></p>
                    </div>
                    <span class="badge badge-custom <?php echo $passed ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo $passed ? 'ناجح' : 'لم يجتز'; ?>
                    </span>
                </div>

                <div class="exam-stats">
                    <div class="exam-stat-item">
                        <div class="small text-muted">الدرجة</div>
                        <div class="h5 mb-0"><?php echo number_format($row['score'], 1); ?>%</div>
                    