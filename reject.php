<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guest_id'])) {
  $guest_id = intval($_POST['guest_id']);
  // Option 1: Delete the record
  // $sql = "DELETE FROM guests WHERE id = $guest_id";
  // Option 2: Mark as rejected
  $sql = "UPDATE guests SET approved = -1 WHERE id = $guest_id";

  if ($conn->query($sql) === TRUE) {
    // Redirect back to approve.php with a message
    header('Location: approve.php');
    exit();
  } else {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($conn->error) . "</p>";
  }
} else {
  echo "<p style='color:red;'>Invalid request.</p>";
}
?>
