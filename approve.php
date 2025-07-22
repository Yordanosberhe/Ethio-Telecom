<?php
include 'config.php'; // ✅ Connect to DB

$message = '';
if (!$conn) {
    die("<p style='color:red;'>Database connection failed: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}

// Handle approval POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guest_id'])) {
    $guest_id = intval($_POST['guest_id']);
    $approve_sql = "UPDATE guests SET approved = 1, approval_date = NOW() WHERE id = $guest_id";
    if ($conn->query($approve_sql) === TRUE) {
        $message = "<p style='color:green;'>Guest approved successfully.</p>";
    } else {
        $message = "<p style='color:red;'>Error approving guest: " . htmlspecialchars($conn->error) . "</p>";
    }
}

$sql = "SELECT * FROM guests WHERE approved = 0";
$result = $conn->query($sql);
if (!$result) {
    die("<p style='color:red;'>Query failed: " . htmlspecialchars($conn->error) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Approve Guests</title>
  <style>
    body { font-family: Arial; background: #8BC43F; padding: 20px; }
    table { width: 80%; margin: auto; border-collapse: collapse; background: #fff; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background:green; color: #fff; }
    form { display: inline; }
    button { padding: 5px 10px; background: #28a745; color: #fff; border: none; cursor: pointer; }
    button:hover { background: #218838; }
    .reject { background: #dc3545; }
    .reject:hover { background: #c82333; }
    .logo { width: 150px; max-width: 100%; display: block; margin: auto; }
  </style>
</head>
<body>
  <img src="ethio-telecom-logo.png" alt="Ethio Telecom Logo" class="logo">
  <h2 style="text-align:center;">Pending Approvals</h2>

  <?php if ($message) echo $message; ?>

  <table>
    <tr>
      <th>Full Name</th>
      <th>ID Number</th>
      <th>Action</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['id_number']) . "</td>";
        echo "<td>
          <form action='approve.php' method='POST' style='display:inline;'>
            <input type='hidden' name='guest_id' value='" . $row['id'] . "'>
            <button type='submit'>Approve</button>
          </form>
          <form action='reject.php' method='POST' style='display:inline;'>
            <input type='hidden' name='guest_id' value='" . $row['id'] . "'>
            <button type='submit' class='reject'>Reject</button>
          </form>
        </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='3'>No pending approvals.</td></tr>";
    }
    ?>

  </table>
</body>
</html>
