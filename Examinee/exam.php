<?php
session_start();
include('config.php');

// Only Admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

include('get_exams_questions.php');
$current_date = date('Y-m-d H:i:s'); // Format: Year-Month-Day Hours:Minutes:Seconds

// Store the current date and time in a session variable
$_SESSION['start_time'] = $current_date;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $exam_id = $_POST['exam_id'];  // Retrieve the exam_id from the POST request
  echo "The selected exam ID is: " . $exam_id;
  // Process the exam (e.g., retrieve exam details, start the exam, etc.)
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Exam Page</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f8f9fc;
      }
      .exam-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      .timer {
        font-size: 20px;
        color: #ff4d4f;
        text-align: right;
      }
      .question {
        margin-bottom: 20px;
      }
      .btn-submit {
        padding: 10px 15px;
        background-color: #6c63ff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }
      .btn-submit:hover {
        background-color: #4e4b9b;
      }
      .answer {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
      }
      .answer label {
        width: 45%;
      }
    </style>
  </head>
  <body>
  <div class="exam-container">
    <div class="timer" id="timer">Time left: 10:00</div>
    <h2>Exam</h2>
    <form method="post" action="check_answers.php?exam_id=<?php echo $exam_id; ?>">
        <?php
        foreach ($questions as $index => $q): 
            $options = get_options($q['id']); // Fetch options for the question
        ?>
            <div class="question">
                <p><?php echo ($index + 1) . ". " . $q['question_text']; ?></p>
                <div class="answer">
                    <?php while ($row = mysqli_fetch_assoc($options)): ?>
                        <label>
                            <input type="radio" name="q<?php echo $q['id']; ?>" value="<?php echo $row['option_text']; ?>" />
                            <?php echo $row['option_text']; ?>
                        </label>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-submit">Submit</button>
    </form>
</div>

    <script>
      // Timer logic
      let timeLeft = 1000; // 10 minutes in seconds
      const timerElement = document.getElementById("timer");

      function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `Time left: ${minutes}:${
          seconds < 10 ? "0" : ""
        }${seconds}`;

        if (timeLeft != 0) {
          timeLeft--;
        } else {
          alert("exam will submit ");
          submitExam(); // Submit automatically when time is up
        }
      }

      function submitExam() {
        const question1 = document.querySelector(
          'input[name="question1"]:checked'
        )?.value;
        const question2 = document.querySelector(
          'input[name="question2"]:checked'
        )?.value;

        // If answers are missing, we still submit, but no alert is shown
        const answers = {
          question1: question1 || "No answer",
          question2: question2 || "No answer",
        };

        // Here you can send the answers to the server if needed, or log them
        console.log("Exam submitted! Answers:", answers);

        // Redirect to the Dashboard after submission
        window.location.href = "dashboard.php"; // Assuming /dashboard is your dashboard URL
      }

      setInterval(updateTimer, 1000);
    </script>
  </body>
</html>




