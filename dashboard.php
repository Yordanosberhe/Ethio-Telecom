<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: index.php");
  exit();
}

// Redirect based on role.
// The role should already be set and in lowercase from login.php
$role = $_SESSION['role'] ?? '';

if ($normalized_role === 'officer') {
   header("Location: dashboard_officer.php");
} elseif ($normalized_role === 'employee') {
   header("Location: dashboard_employee.php");
} else {
    // Fallback for unknown roles.
    // Log the problematic role for debugging.
    error_log("Invalid or unset role detected: '" . $role . "'. Forcing logout.");

    // Destroy the session to prevent a redirect loop or access to a broken state.
    session_unset();
    session_destroy();

    // Redirect to login with a user-friendly error.
    header("Location: index.php?error=invalid_role");
    exit();
}
?>
