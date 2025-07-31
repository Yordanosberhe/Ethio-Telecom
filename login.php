<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // IMPORTANT: use password_hash() when creating users!
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['logged_in'] = true;

            $role_from_db = strtolower(trim($row['role']));
            $normalized_role = '';

            switch ($role_from_db) {
                case 'officer':
                case 'approval officer':
                    $normalized_role = 'officer';
                    break;
                case 'employee':
                case 'ethio-telecom employee':
                    $normalized_role = 'employee';
                    break;
            }

            if ($normalized_role) {
                $_SESSION['role'] = $normalized_role;

                if ($normalized_role === 'officer') {
                    header("Location: dashboard_officer.php");
                } elseif ($normalized_role === 'employee') {
                    header("Location: dashboard_employee.php");
                } else {
                    header("Location: index.php?error=invalid_role");
                }
                exit();
            } else {
                error_log("Login failed: Unrecognized role '{$row['role']}' for user '{$username}'.");
                session_unset();
                session_destroy();
                header("Location: index.php?error=invalid_role");
                exit();
            }
        } else {
            header("Location: index.php?error=invalid_credentials&username=" . urlencode($username));
            exit();
        }
    } else {
        header("Location: index.php?error=invalid_credentials&username=" . urlencode($username));
        exit();
    }

    $stmt->close();
}
?>
