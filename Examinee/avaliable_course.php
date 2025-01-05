<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Previous head content remains the same -->
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

        .courses-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            color: #2d3748;
            opacity: 0;
            transform: translateY(20px);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .search-section {
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(20px);
        }

        .search-container {
            display: flex;
            gap: 1rem;
            background: white;
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .search-input {
            flex: 1;
            padding: 0.8rem 1.2rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .course-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            position: relative;
            overflow: hidden;
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .course-card:hover::before {
            opacity: 1;
        }

        .course-title {
            font-size: 1.4rem;
            color: #2d3748;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .course-description {
            color: #4a5568;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .instructor {
            color: #718096;
            font-size: 0.9rem;
        }

        .enroll-btn {
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .enroll-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.25);
        }

        .no-courses {
            text-align: center;
            padding: 2rem;
            color: #718096;
            grid-column: 1 / -1;
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

        .new-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #48bb78;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            animation: pulse 2s infinite;
        }
  
        
        .view-toggle {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(20px);
        }

        .toggle-btn {
            padding: 0.8rem 1.5rem;
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .toggle-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: white;
        }

        .status-pending {
            background: #ed8936;
            animation: pulse 2s infinite;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Courses</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    </head>
<body>
    <?php
    require_once('config.php');

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $view_type = isset($_GET['view']) ? $_GET['view'] : 'available';

    // Query for available courses (not enrolled and not requested)
    $available_sql = "
        SELECT c.id, c.name, c.description, c.created_by, u.username as instructor_name, 
               DATEDIFF(CURRENT_DATE, c.created_at) as days_old
        FROM courses c
        LEFT JOIN users u ON c.created_by = u.id
        WHERE c.id NOT IN (
            SELECT course_id FROM enrollment WHERE user_id = ?
        )
    ";

    // Query for requested courses
    $requested_sql = "
        SELECT c.id, c.name, c.description, c.created_by, u.username as instructor_name,
               e.enroll_at, e.accepted
        FROM courses c
        JOIN enrollment e ON c.id = e.course_id
        LEFT JOIN users u ON c.created_by = u.id
        WHERE e.user_id = ? AND e.accepted = 0
    ";
    ?>

    <div class="courses-container">
        <div class="header">
            <h1>Course Catalog</h1>
            <p>Discover new learning opportunities and expand your knowledge</p>
        </div>

        <div class="view-toggle">
            <button class="toggle-btn <?php echo $view_type === 'available' ? 'active' : ''; ?>" 
                    onclick="window.location.href='?view=available'">
                Available Courses
            </button>
            <button class="toggle-btn <?php echo $view_type === 'requested' ? 'active' : ''; ?>"
                    onclick="window.location.href='?view=requested'">
                Requested Courses
            </button>
        </div>

        <div class="search-section">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search courses..." id="searchInput">
            </div>
        </div>

        <div class="courses-grid">
            <?php
            $stmt = $conn->prepare($view_type === 'available' ? $available_sql : $requested_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0):
                while ($course = $result->fetch_assoc()):
            ?>
                <div class="course-card" data-course-name="<?php echo strtolower($course['name']); ?>">
                    <?php if ($view_type === 'available'): ?>
                        <?php if ($course['days_old'] <= 7): ?>
                            <span class="new-badge">New</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="status-badge status-pending">Pending Approval</span>
                    <?php endif; ?>
                    
                    <h2 class="course-title"><?php echo htmlspecialchars($course['name']); ?></h2>
                    <p class="course-description"><?php echo htmlspecialchars($course['description']); ?></p>
                    
                    <div class="course-meta">
                        <span class="instructor">By <?php echo htmlspecialchars($course['instructor_name']); ?></span>
                        <?php if ($view_type === 'available'): ?>
                            <button class="enroll-btn" onclick="enrollCourse(<?php echo $course['id']; ?>)">Enroll Now</button>
                        <?php else: ?>
                            <span>Requested on <?php echo date('M d, Y', strtotime($course['enroll_at'])); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="no-courses">
                    <h3>No <?php echo $view_type; ?> courses found</h3>
                    <p><?php echo $view_type === 'available' ? 'Check back later for new courses!' : 'You haven\'t requested any courses yet.'; ?></p>
                </div>
            <?php endif; ?>
        </div>
        <div style="text-align: center;">
            <a href="dashboard.php" class="btn-back">Return to Home</a>
        </div>
    </div>
    <div id="enrollModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div style="background: white; padding: 2rem; border-radius: 15px; max-width: 400px; text-align: center; position: relative;">
        <h3 id="modalTitle" style="margin-bottom: 1rem; color: #2d3748;"></h3>
        <p id="modalMessage" style="margin-bottom: 1.5rem; color: #4a5568;"></p>
        <button onclick="closeModal()" style="padding: 0.8rem 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; cursor: pointer;">Close</button>
        </div>
    </div>
    </div><!-- end of courses-container -->

    <!-- Previous modal and script code remains the same -->
    <script>
        // Animate elements on load
        anime({
            targets: '.header',
            opacity: [0, 1],
            translateY: [20, 0],
            duration: 1000,
            easing: 'easeOutExpo'
        });

        anime({
            targets: '.search-section',
            opacity: [0, 1],
            translateY: [20, 0],
            duration: 1000,
            delay: 200,
            easing: 'easeOutExpo'
        });

        anime({
            targets: '.course-card',
            opacity: [0, 1],
            translateY: [20, 0],
            delay: anime.stagger(100, {start: 400}),
            duration: 800,
            easing: 'easeOutExpo'
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.course-card').forEach(card => {
                const courseName = card.dataset.courseName;
                if (courseName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Enrollment function
        function showModal(title, message) {
    const modal = document.getElementById('enrollModal');
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    modal.style.display = 'flex';
    
    // Add animation to modal
    anime({
        targets: modal.children[0],
        scale: [0.9, 1],
        opacity: [0, 1],
        duration: 300,
        easing: 'easeOutExpo'
    });
}

function closeModal() {
    const modal = document.getElementById('enrollModal');
    
    anime({
        targets: modal.children[0],
        scale: [1, 0.9],
        opacity: [1, 0],
        duration: 300,
        easing: 'easeInExpo',
        complete: function() {
            modal.style.display = 'none';
            // Refresh the page after closing the success modal
            if (document.getElementById('modalTitle').textContent === 'Success!') {
                location.reload();
            }
        }
    });
}

function enrollCourse(courseId) {
    // Show loading state
    showModal('Processing...', 'Sending enrollment request...');
    
    fetch('enroll_course.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            course_id: courseId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('Success!', 'Your enrollment request has been sent. Please wait for instructor approval.');
            
            // Animate the enrolled course card
            const card = document.querySelector(`[data-course-name*="${courseId}"]`);
            if (card) {
                anime({
                    targets: card,
                    translateX: [0, -20],
                    opacity: [1, 0],
                    duration: 800,
                    easing: 'easeOutExpo'
                });
            }
        } else {
            showModal('Error', data.message || 'Failed to enroll in course. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'An error occurred. Please try again.');
    });
}

// Add event listener to close modal when clicking outside
document.getElementById('enrollModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
        // Add animation for the view toggle buttons
        anime({
            targets: '.view-toggle',
            opacity: [0, 1],
            translateY: [20, 0],
            duration: 1000,
            delay: 100,
            easing: 'easeOutExpo'
        });

        // Previous animation and functionality code remains the same
    </script>
</body>
</html>