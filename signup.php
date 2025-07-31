<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
  $first_name = trim($_POST['first_name']);
  $last_name = trim($_POST['last_name']);
  $email = trim($_POST['email']);
  $role = strtolower(trim($_POST['role']));

  // Validate allowed roles
  $allowed_roles = ['officer', 'employee'];
  if (!in_array($role, $allowed_roles)) {
    die("Invalid role selected.");
  }

  $stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, email, role) VALUES (?, ?, ?, ?, ?, ?)");
  if ($stmt) {
    $stmt->bind_param("ssssss", $username, $password, $first_name, $last_name, $email, $role);

    if ($stmt->execute()) {
      header('Location: index.php?signup=success');
      exit();
    } else {
      echo "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <style>
    body { font-family: Arial; background: #8BC43F; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .signup-container { background: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc; width: 300px; }
    .signup-container h2 { text-align: center; }
    .signup-container input, .signup-container select { width: 100%; padding: 10px; margin: 8px 0; }
    .signup-container button { width: 100%; padding: 10px; background: green; color: #fff; border: none; margin-top: 10px; cursor: pointer; }
    .signup-container button:hover { background: greenyellow; }
    .logo { width: 150px; max-width: 100%; }
  </style>
</head>
<body>
  <div class="signup-container">
    <img src="ethio-telecom-logo.png" alt="Ethio Telecom Logo" class="logo">
    <h2>Sign Up</h2>
    <form action="signup.php" method="POST">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Username" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Password" required>

      <label for="first_name">First Name</label>
      <input type="text" id="first_name" name="first_name" placeholder="First Name">

      <label for="last_name">Last Name</label>
      <input type="text" id="last_name" name="last_name" placeholder="Last Name">

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Email">

      <label for="role">Role</label>
      <select name="role">
        <option value="officer">Approval Officer</option>
        <option value="employee">Ethio-telecom Employee</option>
      </select>

      <button type="submit">Register</button>
    </form>
  </div>
</body>
</html>
