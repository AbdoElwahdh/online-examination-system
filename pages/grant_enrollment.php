<?php

include '../db_connection.php';
session_start();

// Check if the user is logged in and is an instructor (role_id = 2)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
}
        $user=$_SESSION['user_id'];
        $q = "SELECT e.user_id as id, e.course_id as cid, u.username as uname, u.email as mail , c.name as cname FROM enrollment e
               join users u on u.id=e.user_id
               join courses c on c.id=e.course_id 
               WHERE course_id in (SELECT id FROM courses WHERE created_by = $user)
               and accepted =0
               ";
                 
            $result = mysqli_query($conn, $q);

                 // Fetch and display the results
            if ($result) {
                $requesters = mysqli_fetch_all($result, MYSQLI_ASSOC);
            } else {
                echo "Error: " . mysqli_error($conn);
            }
           ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/assets/sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Enrollment Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .approve {
            background-color: #4caf50;
            color: white;
        }
        .reject {
            background-color: #f44336;
            color: white;
        }
        button:hover {
            opacity: 0.9;
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
        <h1>Enrollment Requests</h1>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example row, replace with PHP-generated rows -->
                <?php if (!empty($requesters)): ?>
                        <?php foreach ($requesters as $requester): ?>
                            <tr id="row-<?= $requester['id'] ?>">
                              <td><?= htmlspecialchars($requester['uname']) ?></td>
                              <td><?= htmlspecialchars($requester['mail']) ?></td>
                              <td><?= htmlspecialchars($requester['cname']) ?></td>
                              <td>
                              <button class="approve"  onclick="handleRequest(<?= $requester['id'] ?>, 'approve')">Approve</button>
                              <button class="reject" onclick="handleRequest(<?= $requester['id'] ?>, 'reject')">Reject</button>
                              </td>
                            </tr>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No requests available.</td>
                        </tr>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
   </div> 

    <script>
       function handleRequest(requesterId, action) {
    fetch('handlerequest.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ requesterId, action })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json(); // Parse only if response is valid JSON
    })
    .then(data => {
        if (data.success) {
            if (action === 'approve') {
                alert(data.message);
                document.querySelector(`#row-${requesterId}`).remove();

            } else if (action === 'reject') {
                alert(data.message);
                document.querySelector(`#row-${requesterId}`).remove();
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        // console.error('Error', error);
        alert('An error occurred while processing your request.');
    });
}

    </script>
</body>
</html>
