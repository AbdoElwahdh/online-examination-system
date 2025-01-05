
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
  header('Location: login.php');
  exit();
}



if(isset($_POST['submit']))
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "examdb";

    $name=$_POST['examTitle'];
    $description=$_POST['examDescription'];
    $created_by=$_SESSION['user_id'];
    $course_id=$_POST['Course'];
    $count=(int)$_POST['count'];
    $q_type=$_POST['questionType'];
    $questions=$_POST['questions'];
    $option=$_POST['options'];
    $ans=$_POST['answer'];
    $ans_count=count($ans);
    $opt_count=count($option)/4;
    $h_c=0;
    
    
    $conn = mysqli_connect($servername, $username, $password, $database);
    if(!$conn)
    {
       echo mysqli_connect_error; 
    }
    $q="INSERT INTO `exams` (`name`,`description`,`course_id`,`created_by`) VALUES ('$name','$description','$course_id','$created_by')";
    mysqli_query($conn,$q);
    $q2="select id from `exams`  order by id desc limit 1";
    $result=mysqli_query($conn,$q2);
    if ($result) {
      // Check if the query returned any rows
      if (mysqli_num_rows($result) > 0) {
          // Fetch the row as an associative array
          $ids = mysqli_fetch_assoc($result);
          $exam_id=$ids['id'];
      }
    }  
    for ($i=0;$i<$count; $i++) {
      if ($ans_count>0)
      {
      $question_text = mysqli_real_escape_string($conn, $questions[$i]);
      $correct_answer = mysqli_real_escape_string($conn, $ans[$i]);  
      $q="INSERT INTO `questions` (`exam_id`,`question_text`,`question_type`,`correct_answer`) VALUES ('$exam_id','$question_text','$q_type[$i]','$correct_answer')";
      $ans_count--;
      }
      else{
        $question_text = mysqli_real_escape_string($conn, $questions[$i]);
        $q="INSERT INTO `questions` (`exam_id`,`question_text`,`question_type`) VALUES ('$exam_id','$question_text','$q_type[$i]')";
      }
      mysqli_query($conn,$q);
      $q3="select id from `questions`  order by id desc limit 1";
    $result2=mysqli_query($conn,$q3);
    if ($result2) {
      // Check if the query returned any rows
      if (mysqli_num_rows($result2) > 0) {
          // Fetch the row as an associative array
          $ids2 = mysqli_fetch_assoc($result2);
          $question_id=$ids2['id'];
          // echo $q_type[$i];
      }
    }  
      if($q_type[$i] == "mcq")
      {
        $x=0;
        while($x<4 && $opt_count > 0)
        { 
          $j=$x+$h_c;
          $corr=1;
          $ncorr=0;
          if($option[$j] == $ans[$i]){
           $options_query="INSERT INTO `options` (`question_id`,`option_text`,`is_correct`) VALUES ('$question_id','$option[$j]','$corr')";}
           else
           {$options_query="INSERT INTO `options` (`question_id`,`option_text`,`is_correct`) VALUES ('$question_id','$option[$j]','$ncorr')";}
           mysqli_query($conn,$options_query);
          $x++;
        }
        $opt_count--;
        $h_c=$h_c+4;
      }
      if ($q_type[$i] == "truefalse")
      { if($ans[$i]=='True'){$t=1;$f=0;}else{$t='0';$f='1';}
        $tq=$options_query="INSERT INTO `options` (`question_id`,`option_text`,`is_correct`) VALUES ('$question_id','True','$t')";
        $fq=$options_query="INSERT INTO `options` (`question_id`,`option_text`,`is_correct`) VALUES ('$question_id','False','$f')";
        mysqli_query($conn,$tq);
        mysqli_query($conn,$fq);
      }


    }
    

    

}    
header("Location: add_exam.php");
exit();
?>

