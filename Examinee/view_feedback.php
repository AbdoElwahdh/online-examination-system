<?php   
    if (session_status() == PHP_SESSION_NONE) {
        session_start();  // Only start the session if it hasn't been started already
    }
    
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Feedback History</title>
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

        .feedback-container {
            max-width: 1000px;
            margin: 0 auto;
            opacity: 0;
            transform: translateY(20px);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 0.5rem;
        }

        .feedback-item {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            opacity: 0;
            transform: translateX(-20px);
        }

        .feedback-item:hover {
            transform: translateX(5px);
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .course-info {
            font-size: 1.1rem;
            color: #764ba2;
            font-weight: bold;
        }

        .timestamp {
            color: #666;
            font-size: 0.9rem;
        }

        .feedback-content {
            color: #333;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .response {
            background: #f8f9fc;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
            border-left: 4px solid #764ba2;
        }

        .response-label {
            color: #764ba2;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .no-response {
            color: #666;
            font-style: italic;
        }

        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .filter-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 30px;
            background: white;
            color: #764ba2;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: #764ba2;
            color: white;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .new-feedback {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <?php
   
    include('config.php');

    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Get total feedback count
    $query = "SELECT COUNT(*) as total FROM feedbacks WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $total_count = $stmt->get_result()->fetch_assoc()['total'];

    // Get responded feedback count
    $query = "SELECT COUNT(*) as responded FROM feedbacks WHERE user_id = ? AND response IS NOT NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $responded_count = $stmt->get_result()->fetch_assoc()['responded'];

    // Get feedbacks with course information
    $query = "SELECT f.*, c.name as course_name, u.username as receiver_name 
              FROM feedbacks f 
              JOIN courses c ON f.course_id = c.id 
              JOIN users u ON f.receiver_id = u.id 
              WHERE f.user_id = ? 
              ORDER BY f.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $feedbacks = $stmt->get_result();
    ?>

    <div class="feedback-container">
        <div class="header">
            <h1>Your Feedback History</h1>
            <p>Track your course feedback and responses</p>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_count; ?></div>
                <div class="stat-label">Total Feedbacks</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $responded_count; ?></div>
                <div class="stat-label">Responses Received</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_count > 0 ? round(($responded_count / $total_count) * 100) : 0; ?>%</div>
                <div class="stat-label">Response Rate</div>
            </div>
        </div>

        <div class="filters">
            <button class="filter-btn active" data-filter="all">All Feedback</button>
            <button class="filter-btn" data-filter="responded">With Response</button>
            <button class="filter-btn" data-filter="pending">Pending Response</button>
        </div>

        <?php while ($feedback = $feedbacks->fetch_assoc()): ?>
            <div class="feedback-item" data-status="<?php echo $feedback['response'] ? 'responded' : 'pending'; ?>">
                <div class="feedback-header">
                    <div class="course-info">
                        <?php echo htmlspecialchars($feedback['course_name']); ?>
                    </div>
                    <div class="timestamp">
                        <?php echo date('F j, Y, g:i a', strtotime($feedback['created_at'])); ?>
                    </div>
                </div>
                <div class="feedback-content">
                    <?php echo htmlspecialchars($feedback['feedback_text']); ?>
                </div>
                <?php if ($feedback['response']): ?>
                    <div class="response">
                        <div class="response-label">Response from <?php echo htmlspecialchars($feedback['receiver_name']); ?>:</div>
                        <?php echo htmlspecialchars($feedback['response']); ?>
                    </div>
                <?php else: ?>
                    <div class="response">
                        <div class="no-response">Awaiting response...</div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        <div style="text-align: center;">
            <a href="dashboard.php" class="btn-back">Return to Home</a>
        </div>
    </div>

    <script>
        // Animate container on load
        anime({
            targets: '.feedback-container',
            opacity: [0, 1],
            translateY: [20, 0],
            duration: 1000,
            easing: 'easeOutExpo'
        });

        // Animate feedback items
        anime({
            targets: '.feedback-item',
            opacity: [0, 1],
            translateX: [-20, 0],
            delay: anime.stagger(100),
            duration: 800,
            easing: 'easeOutExpo'
        });

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Update active button
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                // Filter feedback items
                const filter = button.dataset.filter;
                document.querySelectorAll('.feedback-item').forEach(item => {
                    if (filter === 'all' || item.dataset.status === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>