<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $role = $_POST['role'];

  $sql = "INSERT INTO users (username, password, first_name, last_name, email, role)
          VALUES ('$username', '$password', '$first_name', '$last_name', '$email', '$role')";

  if ($conn->query($sql) === TRUE) {
    header('Location: index.php');
    exit();
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
?>
