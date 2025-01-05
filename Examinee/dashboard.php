<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in and their role is set in the session
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
  header('Location: login.php');
  exit();
}
$username = $_SESSION['username']; // Get the user's role (e.g., Student)
// if (isset($_SESSION['username'])) {
// } else {
//     header('login.php');
// }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white
            ;
            margin: 0;
            padding: 0;
            /* background-image: url(dashboardbackground.jpg); */

        }
        .open-feedback-btn {
            background-color: #3b5998;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            border-radius: 5px;
            margin: 20px;
        }
        .feedback-popup {
            display: none;
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            width: 400px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        .feedback-popup.active {
            display: block;
        }
        .feedback-popup label {
            display: block;
            margin: 10px 0 5px;
        }
        .feedback-popup select,
        .feedback-popup textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .feedback-popup textarea {
            height: 100px;
            resize: vertical;
        }
        .feedback-popup button {
            background-color: #3b5998;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .close-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-size: 20px;
        }

      /* Reset and base styles */
      body {
        margin: 0;
        font-family: "Arial", sans-serif;
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
      }
      a {
        text-decoration: none;
        color: inherit;
      }
      /* Sidebar styling */
      .sidebar {
        width: 250px;
        background: linear-gradient(180deg, #6c63ff, #3f3d8f);
        color: #fff;
        height: 100vh;
        position: fixed;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        transform: translateX(0);
        transition: transform 0.3s ease-in-out;
      }
      .sidebar.hidden {
        transform: translateX(-100%);
      }
      .sidebar h2 {
        font-size: 22px;
        margin-bottom: 30px;
        text-align: center;
        color: #f8f9fc;
      }
      .menu-item {
        margin-bottom: 15px;
      }
      .menu-item a {
        color: #f8f9fc;
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
      }
      .menu-item a:hover {
        background-color: #4e4b9b;
      }
      .menu-item i {
        margin-right: 10px;
        font-size: 18px;
      }
      /* Sidebar toggle button */
      .toggle-btn {
        position: fixed;
        top: 20px;
        left: 90%;
        background-color: #6c63ff;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        cursor: pointer;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      .toggle-btn:hover {
        background-color: #4e4b9b;
      }
      /* Dashboard content */
      .dashboard-content {
        margin-left: 270px;
        padding: 40px;
        width: calc(100% - 350px);
        transition: margin-left 0.3s ease-in-out;
      }
      .dashboard-content.full-width {
        margin-left: 20px;
        width: calc(100% - 100px);
      }
      .dashboard-content h1 {
        color: #333;
        margin-bottom: 20px;
      }
      /* Cards */
      .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        margin-bottom: 20px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.3s, box-shadow 0.3s;
      }
      .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      }
      .card-title {
        font-size: 18px;
        color: #6c63ff;
        margin-bottom: 10px;
      }
      .card-text {
        color: #555;
        margin-bottom: 15px;
      }
      .btn {
        padding: 10px 15px;
        background-color: #6c63ff;
        color: #fff;
        border-radius: 5px;
        text-align: center;
        display: inline-block;
        font-weight: bold;
        transition: background-color 0.3s;
      }
      .btn:hover {
        background-color: #4e4b9b;
      }
      
      /* Grid */
      .row {
        display: flex;
    flex-wrap: wrap;
    gap: 20px;
    height: fit-content;
    justify-content: center;
      }
      .col-md-6 {
        flex: 1;
        min-width: 280px;
      }
      /* Responsive design */
      @media (max-width: 768px) {
        .sidebar {
          transform: translateX(0);
        }
        .dashboard-content {
          margin-left: 20px;
          width: calc(100% - 100px);
        }
      }
      .backimg{
        text-align: center;
    width: 85%;
    height: 490px;
      }
      .backimg img{
        width: 80%;
        height: 100%;
      }
    </style>
  </head>
  <body>
    <!-- Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>
    <div class="sidebar hidden" id="sidebar">
      <h2>Student Dashboard</h2>
      <div class="menu-item">
        <a href="dashboard.php"><i class="fas fa-home"></i>Home</a>
      </div>
      <div class="menu-item">
        <a href="avaliable_course.php"><i class="fa-solid fa-graduation-cap"></i>Courses Catalog</a>
      </div>
      <div class="menu-item">
        <a href="allexams.php"><i class="fas fa-book"></i>View Exam</a>
      </div>

      <div class="menu-item">
        <a onclick="openFeedbackForm()" class="Add-feedback"><i class="fa-solid fa-comment"></i></i>Give Feedback</a>
      </div>
      <div class="menu-item">
        <a href="Result.php" class="Add-feedback"><i class="fa-solid fa-square-poll-vertical"></i>Result </a>
      </div>
      <div class="menu-item">
        <a href="view_feedback.php" class="Add-feedback"><i class="fa-solid fa-comments"></i>view feedback </a>
      </div>
      <div class="menu-item">
        <a href="logout.php" class="logout"><i class="fa-solid fa-comments"></i>logout </a>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content full-width" id="dashboardContent">
      <div class="user-info">
        <h3>Welcome, <?php echo htmlspecialchars($username); ?> </h3>
        
        <p>Explore your dashboard and manage your learning journey.</p>
      </div>
      <h1>Dashboard Overview</h1>
      <div class="row">
        <div class="backimg">
          <img src="dashboardbackground.jpg" alt="lol">

        </div>
        <div class="feedback-popup" id="feedbackPopup">
    <span class="close-btn" onclick="closeFeedbackForm()">&times;</span>
    <h2>Feedback Form</h2>
    <form id="feedbackForm">
        <label for="receiver_type">Send feedback to:</label>
        <select id="receiver_type" name="receiver_type" onchange="showInstructorList()">
            <option value="1">Admin</option>
            <option value="2">Instructor</option>
        </select>

        <div id="instructorList" style="display: none;">
            <label for="instructor">Select Instructor:</label>
            <select id="instructor" name="instructor"></select>
        </div>

        <!-- Changed these to have default values for admin -->
        <input type="hidden" id="receiver_id" name="receiver_id" value="1">
        <input type="hidden" id="course_id" name="course_id" value="1">

        <label for="feedbackText">Your Feedback:</label>
        <textarea id="feedbackText" name="feedback_text" required></textarea>

        <button type="submit">Submit Feedback</button>
    </form>
</div>
    </div>
      
      </div>
    </div>

    <script>
      const sidebar = document.getElementById("sidebar");
      const dashboardContent = document.getElementById("dashboardContent");

      function toggleSidebar() {
        sidebar.classList.toggle("hidden");
        dashboardContent.classList.toggle("full-width");
      }
      toggleSidebar();
   
        function openFeedbackForm() {
            document.getElementById('feedbackPopup').classList.add('active');
        }

        function closeFeedbackForm() {
            document.getElementById('feedbackPopup').classList.remove('active');
            document.getElementById('feedbackForm').reset();
        }

        function showInstructorList() {
        const receiverType = document.getElementById('receiver_type').value;
        const instructorList = document.getElementById('instructorList');
        const receiverIdInput = document.getElementById('receiver_id');
        const courseIdInput = document.getElementById('course_id');

        if (receiverType === '2') {
            instructorList.style.display = 'block';
            fetchInstructors();
        } else {
            // If Admin is selected
            instructorList.style.display = 'none';
            receiverIdInput.value = '1';  // Set admin's ID
            courseIdInput.value = '1';    // Set default course ID for admin
        }
    }

    function fetchInstructors() {
        fetch('get_instructor_name.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'receiver_type=instructor'
        })
        .then(response => response.json())
        .then(data => {
            const instructorSelect = document.getElementById('instructor');
            instructorSelect.innerHTML = '';
            
            if (data.instructors && data.instructors.length > 0) {
                data.instructors.forEach(instructor => {
                    const option = document.createElement('option');
                    option.value = JSON.stringify({
                        instructor_id: instructor.instructor_id,
                        course_id: instructor.course_id
                    });
                    option.textContent = `${instructor.instructor_name} - ${instructor.course_name}`;
                    instructorSelect.appendChild(option);
                });
                updateHiddenFields();
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No instructors found';
                instructorSelect.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Error fetching instructors:', error);
        });
    }

    function updateHiddenFields() {
        const instructorSelect = document.getElementById('instructor');
        const receiverIdInput = document.getElementById('receiver_id');
        const courseIdInput = document.getElementById('course_id');

        if (instructorSelect.value) {
            const selectedValue = JSON.parse(instructorSelect.value);
            receiverIdInput.value = selectedValue.instructor_id;
            courseIdInput.value = selectedValue.course_id;
        }
    }

    document.getElementById('feedbackForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Debug log to see what's being submitted
        const formData = new FormData(this);
        console.log('Submitting form with data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        fetch('submit_feedback.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                closeFeedbackForm();
            } else {
                alert(data.message || 'Error submitting feedback');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting feedback');
        });
    });
    </script>
  </body>
</html>
