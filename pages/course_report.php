<?php 
include '../db_connection.php';
session_start();

// Check if the user is logged in and is an instructor (role_id = 2)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
} 
// استلام البيانات من الصفحة السابقة 
if (isset($_GET['course']) && isset($_GET['exam'])) { 
    $courseId = $_GET['course']; 
    $instructorId = $_SESSION['user_id']; 
    $examId = $_GET['exam']; 
 
    // استعلام لجلب معلومات الكورس 
    $courseQuery = "SELECT name FROM courses WHERE id = $courseId"; 
    $courseResult = mysqli_query($conn, $courseQuery); 
    $course = mysqli_fetch_assoc($courseResult); 
 
    // استعلام لجلب اسم المدرس 
    $instructorQuery = "SELECT username FROM users WHERE id = $instructorId"; 
    $instructorResult = mysqli_query($conn, $instructorQuery); 
    $instructor = mysqli_fetch_assoc($instructorResult); 
 
    // استعلام لجلب معلومات الامتحان 
    $examQuery = "SELECT name FROM exams WHERE id = $examId"; 
    $examResult = mysqli_query($conn, $examQuery); 
    $exam = mysqli_fetch_assoc($examResult); 
 
    // جلب عدد الطلاب الذين أجروا الامتحانات في هذا الكورس 
    $studentsQuery = "SELECT COUNT(DISTINCT examinee_id) AS total_students FROM examinee_exams WHERE exam_id IN (SELECT id FROM exams WHERE course_id = $courseId)"; 
    $studentsResult = mysqli_query($conn, $studentsQuery); 
    $students = mysqli_fetch_assoc($studentsResult); 
 
    // جلب نتائج الامتحان 
    $resultsQuery = "SELECT u.username, ee.score  
                    FROM examinee_exams ee 
                    JOIN users u ON ee.examinee_id = u.id 
                    WHERE ee.exam_id = $examId"; 
    $resultsResult = mysqli_query($conn, $resultsQuery); 
    $results = []; 
    while ($row = mysqli_fetch_assoc($resultsResult)) { 
        $results[] = $row; 
    } 
 
    // حساب الإحصائيات 
    $totalScores = array_column($results, 'score'); 
    if (count($totalScores) >0){
    $maxScore = max($totalScores); 
    $minScore = min($totalScores); 
    $averageScore = array_sum($totalScores) / count($totalScores); }
    else{
        $maxScore = 0; 
    $minScore = 0; 
    $averageScore = 0;
    }
} else { 
    die("Invalid access."); 
} 
?> 
 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <title>Course Report</title> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Link to Chart.js --> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> <!-- Link to jsPDF --> 
    <style>
        /* General Styling */
        /* body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #4e73df, #224abe);
            color: #fff;
            position: fixed;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
        }

        .sidebar h2 {
            text-align: center;
            color: #f8f9fc;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: #f8f9fc;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            margin-bottom: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .sidebar a:hover {
            background: #2e59d9;
        }

        .dashboard-content {
            margin-left: 270px;
            padding: 20px;
            box-sizing: border-box;
            width: calc(100% - 270px);
        }

        @media (max-width: 768px) {
            .dashboard-content {
                margin-left: 0;
                width: 100%;
            }
        }

        .dashboard-content h1 {
            margin-bottom: 20px;
            color: #343a40;
        }

        /* Card and Row Styling */
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background: #f8f9fc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1;
            min-width: 280px;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            color: #4e73df;
            margin-bottom: 10px;
        }

        .card p {
            margin-bottom: 15px;
            color:rgb(255, 196, 0);
        }

        .btn {
            background: #4e73df;
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .btn:hover {
            background: #2e59d9;
        }

        @media (max-width: 576px) {
            .card {
                min-width: 100%;
            }
        } */
        /* تنسيق صفحة التقرير */ 
        body { 
            font-family: 'Arial', sans-serif; 
            background-color: #f4f7fc; 
            color: #333; 
            margin: 0; 
            padding: 0; 
        } 
        h2, h3 { 
            color: #5f6368; 
        } 
        .container { 
            width: 80%; 
            margin: 50px auto; 
            background-color: #fff; 
            padding: 20px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            border-radius: 8px; 
        } 
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        } 
        th, td { 
            padding: 12px; 
            text-align: center; 
            border: 1px solid #ddd; 
        } 
        th { 
            background-color: #3b82f6; 
            color: white; 
        } 
        tr:nth-child(even) { 
            background-color: #f9f9f9; 
        } 
        .stat-box { 
            background-color: #eff6ff; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 30px; 
            display: flex; 
            justify-content: space-between; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        } 
        .stat-box p { 
            margin: 0; 
            font-size: 16px; 
        } 
        .stat-box p strong { 
            font-weight: bold; 
        } 
        .score-box { 
            background-color: #d1fae5; 
            padding: 15px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
            margin-top: 20px; 
        } 
        .score-box p { 
            margin: 0; 
        } 
        .rank { 
            background-color: #3b82f6; 
            color:white; 
            padding: 5px 10px; 
            border-radius: 5px; 
        } 
        .card { 
            background-color: #fffbf0; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
            margin: 10px 0; 
        } 
        .card-title { 
            font-size: 1.2em; 
            margin-bottom: 10px; 
        } 
        .card-description { 
            font-size: 1em; 
            color: #555; 
        } 
        .card-footer { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 10px; 
        } 
 
        /* زر PDF */ 
        .pdf-btn { 
            display: block; 
            width: 200px; 
            height: 50px; 
            margin: 20px auto; 
            background-color: #3b82f6; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 18px; 
            cursor: pointer; 
            text-align: center; 
            line-height: 50px; 
            transition: background-color 0.3s ease; 
        } 
        .pdf-btn:hover { 
            background-color: #2563eb; 
        } 
 
        /* تصميم الرسم البياني */ 
        #scoreChart { 
            max-width: 600px; 
            margin: 0 auto; 
        } 
        .chart-container {
        width: 100%;
        max-width: 100%;
        height: 100%;
        padding: 20px;
        box-sizing: border-box;
        overflow: hidden;
        display: flex;
        justify-content: center; /* توسيط الشكل البياني */
    }

    canvas {
        width: 100% !important;
        height: auto !important;
    }

    /* Media Queries */
    @media (max-width: 768px) {
        .stat-box {
            flex-direction: column;
            align-items: center;
        }

        .stat-box div {
            min-width: auto;
            margin-bottom: 10px;
        }

        table {
            font-size: 14px;
        }

        .container {
            padding: 15px;
        }

        .chart-container {
            padding: 10px;
        }

        /* توسيط الزر على الشاشات الصغيرة */
        .pdf-btn {
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
        }
    }

    @media (max-width: 480px) {
        h2, h3 {
            font-size: 18px;
        }

        th, td {
            padding: 8px;
            font-size: 12px;
        }

        .pdf-btn {
            width: 100%;
            font-size: 16px;
        }
    }
    </style> 
</head> 
<body> 
<div class="sidebar">
        <h3>Instructor Dashboard</h3>
        <a href="instructor_dashboard.php">Home</a>
        <a href="add_exam.php">Add Exam</a>
        <a href="manage_exam.php">Manage Exams</a>
        <a href="view_results.php">View Results</a>
        <a href="reports.php">Reports</a>
        <a href="grant_enrollment.php">Enrollment Requests</a>
        <a href="feedbacks.php">Feedbacks</a>
        <a href="logout.php">Logout</a>
    </div>

   <div class="dashboard-content">
    <div class="container"> 
        <h2>Course Report</h2> 
 
        <!-- Course Information --> 
        <div class="card"> 
            <div class="card-title">Course Information</div> 
            <div class="card-description"> 
                <p><strong>Course Name:</strong> <?= $course['name'] ?></p> 
                <p><strong>Instructor:</strong> <?= $instructor['username'] ?></p> 
                <p><strong>Total Students Enrolled:</strong> <?= $students['total_students'] ?></p> 
            </div> 
        </div> 
 
        <!-- Exam Information --> 
        <div class="card"> 
            <div class="card-title">Exam Information</div> 
            <div class="card-description"> 
                <p><strong>Exam Name:</strong> <?= $exam['name'] ?></p> 
                <p><strong>Total Students Attended:</strong> <?= count($results) ?></p> 
            </div> 
        </div> 
 
        <!-- Stats Box --> 
        <div class="stat-box"> 
            <div> 
                <p><strong>Highest Score:</strong> <?= $maxScore ?>%</p> 
                <p><strong>Lowest Score:</strong> <?= $minScore ?>%</p> 
            </div> 
            <div> 
                <p><strong>Average Score:</strong> <?= round($averageScore, 2) ?>%</p> 
            </div> 
        </div> 
 
        <!-- Student Performance Table --> 
        <h3>Student Performance:</h3> 
        <table> 
            <thead> 
                <tr> 
                    <th>Name</th> 
                    <th>Score</th> 
                    <th>Rank</th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php  
                usort($results, function($a, $b) { 
                    return $b['score'] - $a['score']; 
                }); 
                foreach ($results as $index => $result): ?> 
                    <tr> 
                        <td><?= $result['username'] ?></td> 
                        <td><?= $result['score'] ?>%</td> 
                        <td class="rank"><?= $index + 1 ?></td> 
                    </tr> 
                <?php endforeach; ?> 
            </tbody> 
        </table> 
 
        <!-- Chart.js Graph --> 
        <h3>Scores Chart:</h3> 
        <canvas id="scoreChart" width="400" height="200"></canvas> <!-- مكان الرسم البياني --> 
    </div> 
    </div>
 
    <!-- Script for generating Chart.js graph --> 
    <script> 
        var ctx = document.getElementById('scoreChart').getContext('2d'); 
        var scoreChart = new Chart(ctx, { 
            type: 'line', // تغيير الشكل البياني إلى Line 
            data: { 
                labels: <?php echo json_encode(array_column($results, 'username')); ?>, // أسماء الطلاب 
                datasets: [{ 
                    label: 'Scores',
data: <?php echo json_encode(array_column($results, 'score')); ?>, // الدرجات 
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',  
                    borderColor: 'rgba(75, 192, 192, 1)',  
                    borderWidth: 1 
                }] 
            }, 
            options: { 
                scales: { 
                    y: { 
                        beginAtZero: true 
                    } 
                } 
            } 
        }); 
    </script> 
 
    <!-- Button to generate PDF --> 
    <button class="pdf-btn" onclick="generatePDF()">Download PDF</button> 
 
    <!-- Script for generating PDF --> 
    <!-- Script for generating PDF -->
<!-- Script for generating PDF -->
<!-- Script for generating PDF -->
<script>
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
  
    // Add title and course details
    doc.setFontSize(16);
    doc.text("Course Report", 10, 10);
    doc.setFontSize(12);
    doc.text("Course Name: " + '<?= $course['name'] ?>', 10, 20);
    doc.text("Instructor: " + '<?= $instructor['username'] ?>', 10, 30);
    doc.text("Highest Score: " + '<?= $maxScore ?>%', 10, 40);
    doc.text("Lowest Score: " + '<?= $minScore ?>%', 10, 50);

    // Add the chart image inside the PDF
    var imgData = document.getElementById('scoreChart').toDataURL('image/png');
    doc.addImage(imgData, 'PNG', 10, 60, 180, 100);

    // Add a title for student performance
    doc.setFontSize(14);
    doc.text("Student Performance:", 10, 170);

    // Create table headers
    const startY = 180;
    const margin = 10;
    const cellWidth = 50;
    const cellHeight = 10;

    // Headers for the table
    doc.setFontSize(12);
    doc.setTextColor(255, 255, 255);  // White text for header
    doc.setFillColor(0, 0, 0);  // Black background for header
    doc.rect(margin, startY, cellWidth * 3, cellHeight, 'F');  // Table header background color
    doc.text("Name", margin + 10, startY + 7);
    doc.text("Score", margin + cellWidth + 10, startY + 7);
    doc.text("Rank", margin + 2 * cellWidth + 10, startY + 7);

    // Set font size for table data
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);  // Black text for data
    let currentY = startY + cellHeight;

    // Loop through the student results and add them to the table
    <?php foreach ($results as $index => $result): ?>
        // Add borders and background colors for each row
        doc.rect(margin, currentY, cellWidth, cellHeight); // Left cell border
        doc.rect(margin + cellWidth, currentY, cellWidth, cellHeight); // Middle cell border
        doc.rect(margin + 2 * cellWidth, currentY, cellWidth, cellHeight); // Right cell border
        
        // Add data
        doc.text("<?= $result['username'] ?>", margin + 10, currentY + 7);
        doc.text("<?= $result['score'] ?>%", margin + cellWidth + 10, currentY + 7);
        doc.text("<?= $index + 1 ?>", margin + 2 * cellWidth + 10, currentY + 7);
        currentY += cellHeight;
    <?php endforeach; ?>

    // Save the PDF file
    doc.save("Course_Report.pdf");
    }
</script>



</script>


 
</body> 
</html>