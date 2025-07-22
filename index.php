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
  </style>
</head>
<body>
  <div class="welcome">
    <img src="ethio-telecom-logo.png" alt="Ethio Telecom Logo" class="logo" />
    <h1>Welcome to Ethio Telecom Guest Approval System</h1>
    <a href="signup.html">Sign Up</a>

    <!-- Login Form embedded here -->
    <form class="login-form" id="loginForm" action="login.php" method="POST">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>

  <!-- Removed JavaScript that prevented form submission for proper PHP login handling -->
</body>
</html>

