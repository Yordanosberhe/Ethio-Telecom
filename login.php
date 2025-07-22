<?php
session_start();
include 'config.php';

if (!$conn) {
    die("<p style='color:red;'>Database connection failed: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['logged_in'] = true;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<p style='color:red;'>Invalid password.</p>";
        }
    } else {
        echo "<p style='color:red;'>User not found.</p>";
    }
    $stmt->close();
}
?>

