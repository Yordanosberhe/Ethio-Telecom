<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config.php';

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

// ✅ Check session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
    exit;
}

$created_by = $_SESSION['user_id'];

// ✅ Clean input
$full_name = trim($_POST['full_name'] ?? '');
$id_type = trim($_POST['id_type'] ?? '');
$id_number = trim($_POST['id_number'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$office_number = !empty($_POST['office_number']) ? trim($_POST['office_number']) : null;
$reason = trim($_POST['reason'] ?? '');
$visit_date = !empty($_POST['visit_date']) ? trim($_POST['visit_date']) : date('Y-m-d');
$has_laptop = isset($_POST['has_laptop']) ? (int)$_POST['has_laptop'] : 0;

// ✅ Validate
if (empty($full_name) || empty($id_type) || empty($id_number) || empty($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// ✅ Insert
$stmt = $conn->prepare("
    INSERT INTO guests 
    (full_name, id_type, id_number, phone, office_number, reason, visit_date, has_laptop, created_by) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    "sssssssii",
    $full_name,
    $id_type,
    $id_number,
    $phone,
    $office_number,
    $reason,
    $visit_date,
    $has_laptop,
    $created_by
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Guest saved successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
exit;
