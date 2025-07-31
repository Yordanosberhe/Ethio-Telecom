<?php
session_start();
include 'config.php';

// --- Officer role check ---
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'officer') {
  header("Location: index.php");
  exit();
}

// --- Logout ---
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: index.php");
  exit();
}

$message = '';
$message_type = '';

// --- Approve / Reject ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guest_id'], $_POST['action'])) {
  $guest_id = intval($_POST['guest_id']);
  $action = $_POST['action'];
  $sql = '';

  if ($action === 'approve') {
    $sql = "UPDATE guests SET approved = 1, approval_date = CURDATE() WHERE id = ?";
  } elseif ($action === 'reject') {
    $sql = "UPDATE guests SET approved = -1 WHERE id = ?";
  }

  if ($sql) {
    $stmt = $conn->prepare($sql);
    if ($stmt) {
      $stmt->bind_param("i", $guest_id);
      if ($stmt->execute()) {
        header("Location: dashboard_officer.php?filter=pending&status=success&action=" . urlencode($action));
      } else {
        header("Location: dashboard_officer.php?filter=pending&status=error");
      }
      $stmt->close();
      exit();
    }
  }
}

// --- Message after redirect ---
if (isset($_GET['status'])) {
  if ($_GET['status'] === 'success' && isset($_GET['action'])) {
    $action = htmlspecialchars($_GET['action']);
    $message = "Guest successfully " . ucfirst($action) . "d.";
    $message_type = 'success';
  } elseif ($_GET['status'] === 'error') {
    $message = "An error occurred while processing the request. Please try again.";
    $message_type = 'error';
  }
}

// --- Stats query ---
$stats_sql = "SELECT 
  COUNT(CASE WHEN approved = 0 THEN 1 END) AS total_pending, 
  COUNT(CASE WHEN approved = 1 AND DATE(approval_date) = CURDATE() THEN 1 END) AS approved_today, 
  COUNT(CASE WHEN approved = 1 THEN 1 END) AS total_approved, 
  COUNT(CASE WHEN approved = -1 THEN 1 END) AS total_rejected 
  FROM guests";
$stats = $conn->query($stats_sql)->fetch_assoc();

// --- Filter ---
$allowed_filters = ['pending', 'approved_today', 'approved', 'rejected'];
$filter = in_array($_GET['filter'] ?? '', $allowed_filters) ? $_GET['filter'] : 'pending';

switch ($filter) {
  case 'approved_today':
    $sql = "SELECT * FROM guests WHERE approved = 1 AND DATE(approval_date) = CURDATE()";
    break;
  case 'approved':
    $sql = "SELECT * FROM guests WHERE approved = 1";
    break;
  case 'rejected':
    $sql = "SELECT * FROM guests WHERE approved = -1";
    break;
  default:
    $sql = "SELECT * FROM guests WHERE approved = 0";
    $filter = 'pending';
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Officer Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; margin: 0; }
    .dashboard-container { max-width: 1200px; margin: 0 auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .header { background: #8BC43F; color: white; padding: 25px; display: flex; justify-content: space-between; align-items: center; }
    .logo { width: 150px; }
    .header-text { flex: 1; margin-left: 20px; }
    .user-profile { position: relative; }
    .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #fff; color: green; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; }
    .dropdown-menu { display: none; position: absolute; right: 0; top: 50px; background: white; min-width: 160px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); border-radius: 4px; overflow: hidden; z-index: 999; }
    .dropdown-menu a { color: #333; padding: 12px 16px; display: block; text-decoration: none; }
    .dropdown-menu a:hover { background: #f0f0f0; }
    .show { display: block; }
    .content { padding: 30px; }
    .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
    .stat-card { background: #fff; border-radius: 8px; padding: 20px; text-align: center; border-top: 4px solid #2E7D32; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.2s; cursor: pointer; text-decoration: none; color: inherit; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .stat-card.active { border-top: 4px solid #F44336; }
    table { width: 100%; border-collapse: collapse; margin-top: 30px; font-size: 14px; }
    th, td { padding: 12px; border-bottom: 1px solid #eee; }
    th { background: green; color: white; }
    tr:hover { background: #f9f9f9; }
    .action-btns { display: flex; gap: 5px; }
    .action-btn { padding: 6px 12px; font-size: 13px; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .approve-btn { background: #4CAF50; }
    .reject-btn { background: #F44336; }
    .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; color: #fff; }
    .message.success { background-color: #4CAF50; }
    .message.error { background-color: #F44336; }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="header">
      <img src="ethio-telecom-logo.png" class="logo">
      <div class="header-text">
        <h1>Officer Dashboard</h1>
      </div>
      <div class="user-profile">
        <div class="user-avatar" id="avatar"><?php echo htmlspecialchars(strtoupper(substr($_SESSION['username'], 0, 2))); ?></div>
        <div class="dropdown-menu" id="dropdown">
          <a href="guest_entry.html"><i class="fas fa-user-plus"></i> Add Guest</a>
          <a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="stats-container">
        <a href="?filter=pending" class="stat-card <?php echo $filter == 'pending' ? 'active' : ''; ?>"><h3>Pending</h3><p><?php echo $stats['total_pending']; ?></p></a>
        <a href="?filter=approved_today" class="stat-card <?php echo $filter == 'approved_today' ? 'active' : ''; ?>"><h3>Approved Today</h3><p><?php echo $stats['approved_today']; ?></p></a>
        <a href="?filter=approved" class="stat-card <?php echo $filter == 'approved' ? 'active' : ''; ?>"><h3>Approved</h3><p><?php echo $stats['total_approved']; ?></p></a>
        <a href="?filter=rejected" class="stat-card <?php echo $filter == 'rejected' ? 'active' : ''; ?>"><h3>Rejected</h3><p><?php echo $stats['total_rejected']; ?></p></a>
      </div>

      <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
      <?php endif; ?>

      <table>
        <thead><tr><th>Name</th><th>ID</th><th>Visit Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php
          if ($result && $result->num_rows) {
            while($row = $result->fetch_assoc()) {
              echo "<tr><td>".htmlspecialchars($row['full_name'])."</td>
              <td>".htmlspecialchars($row['id_number'])."</td>
              <td>".(isset($row['visit_date']) && $row['visit_date'] ? htmlspecialchars($row['visit_date']) : '-')."</td>
              <td>".($row['approved']==1?'Approved':($row['approved']==-1?'Rejected':'Pending'))."</td><td>";
              if ($filter == 'pending') {
                echo "<div class='action-btns'>
                <form method='POST'><input type='hidden' name='guest_id' value='".intval($row['id'])."'><input type='hidden' name='action' value='approve'><button type='submit' class='action-btn approve-btn'>Approve</button></form>
                <form method='POST'><input type='hidden' name='guest_id' value='".intval($row['id'])."'><input type='hidden' name='action' value='reject'><button type='submit' class='action-btn reject-btn'>Reject</button></form>
                </div>";
              } else { echo "-"; }
              echo "</td></tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No records found.</td></tr>";
          }
        ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    const avatar = document.getElementById('avatar');
    const dropdown = document.getElementById('dropdown');
    avatar.addEventListener('click', () => dropdown.classList.toggle('show'));
    window.addEventListener('click', e => { if (!avatar.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove('show'); });
  </script>
</body>
</html>
<?php $conn->close(); ?>
