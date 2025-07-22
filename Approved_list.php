
<?php
include 'config.php';
if (!$conn) {
    die("<p style='color:red;'>Database connection failed: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}
$date = date('Y-m-d');
$sql = "SELECT * FROM guests WHERE approved = 1 AND approval_date = '$date' ORDER BY approval_date DESC";
$result = $conn->query($sql);
if (!$result) {
    die("<p style='color:red;'>Query failed: " . htmlspecialchars($conn->error) . "</p>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, inital-scale=1.0">
  <title>Approved Guests</title>
  <style>
    body { font-family: Arial; background: #8BC43F; padding: 20px; }
    table { width: 80%; margin: auto; border-collapse: collapse; background: #fff; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background: green; color: #fff; }
    .logo { width: 150px; max-width: 100%; }
  </style>
</head>
<body>
  <img src="ethio-telecom-logo.png" alt="Ethio Telecom Logo" class="logo">
  <h2 style="text-align:center;">Approved Guests for Today</h2>
  <table>
    <tr>
      <th>Full Name</th>
      <th>ID Number</th>
      <th>Office Number</th>
      <th>Laptop</th>
    </tr>
    <?php
    if ($result && $result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['id_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['office_number']) . "</td>";
        echo "<td>" . ($row['has_laptop'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='4'>No approved guests for today.</td></tr>";
    }
    ?>
  </table>
</body>
</html>
