<?php
$servername = "localhost";
$username = "root";  // Your DB username
$password = "";  // Your DB password
$dbname = "examdb";  // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    echo mysqli_connect_error();
}
?>