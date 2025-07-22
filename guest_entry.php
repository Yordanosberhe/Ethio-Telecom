<?php
session_start();
include 'config.php';

if (!$conn) {
    die("<p style='color:red;'>Database connection failed: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $id_number = trim($_POST['id_number']);
    $phone = trim($_POST['phone']);
    $office_number = trim($_POST['office_number']);
    $has_laptop = isset($_POST['has_laptop']) ? 1 : 0;
    $created_by = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : NULL;

    $stmt = $conn->prepare("INSERT INTO guests (full_name, id_number, phone, office_number, has_laptop, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssii", $full_name, $id_number, $phone, $office_number, $has_laptop, $created_by);
        if ($stmt->execute()) {
            echo "<script>alert('Guest added successfully.'); window.location.href='dashboard.php';</script>";
            exit();
        } else {
            echo "<p style='color:red;'>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>Prepare failed: " . htmlspecialchars($conn->error) . "</p>";
    }
}
?>
