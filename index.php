<?php
session_start(); // Start session to check if user is already logged in

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';
$username_value = '';

if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials') {
    $error_message = 'Invalid username or password.';
    $username_value = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
} elseif (isset($_GET['error']) && $_GET['error'] == 'invalid_role') {
    $error_message = 'Your user role is not configured correctly. Please contact an administrator.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ethio Telecom Guest Approval System</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #8BC43F;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .welcome {
      background: rgba(255, 255, 255, 0.9);
      padding: 40px;
      border-radius: 10px;
      text-align: center;
      max-width: 400px;
      width: 100%;
    }
    .welcome h1 {
      margin-bottom: 20px;
    }
    .welcome a {
      display: inline-block;
      padding: 10px 20px;
      background: green;
      color: #fff;
      margin: 10px 0;
      text-decoration: none;
      border-radius: 5px;
    }
    .welcome a:hover {
      background: greenyellow;
      color: #333;
    }
    .logo {
      width: 150px;
      max-width: 100%;
    }
    .login-form input {
      width: 90%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .login-form button {
      width: 95%;
      padding: 10px;
      background: green;
      color: #fff;
      border: none;
      border-radius: 5px;
      margin-top: 10px;
      cursor: pointer;
    }
    .login-form button:hover {
      background: greenyellow;
      color: #333;
    }
    .error-message {
      color: #D8000C;
      background-color: #FFD2D2;
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 15px;
      border: 1px solid #D8000C;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="welcome">
    <img src="ethio-telecom-logo.png" alt="Ethio Telecom Logo" class="logo" />
    <h1>Welcome to Ethio Telecom Guest Approval System</h1>
    <a href="signup.php">Sign Up</a>

    <!-- Login Form embedded here -->
    <form class="login-form" id="loginForm" action="login.php" method="POST">
      <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
      <?php endif; ?>
      <input type="text" name="username" placeholder="Username" value="<?php echo $username_value; ?>" required />
      <input type="password" name="password" placeholder="Password" required autofocus />
      <button type="submit">Login</button>
    </form>
  </div>

 
</body>
</html>
