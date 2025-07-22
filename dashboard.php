<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}


if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: index.php");
  exit();
}


$sql = "SELECT * FROM guests WHERE approved = 0";
$result = $conn->query($sql);


$total_pending = $result->num_rows;

$sql_approved_today = "SELECT COUNT(*) AS count FROM guests WHERE approved = 1 AND approval_date = CURDATE()";
$approved_today = $conn->query($sql_approved_today)->fetch_assoc()['count'];

$sql_total_approved = "SELECT COUNT(*) AS count FROM guests WHERE approved = 1";
$total_approved = $conn->query($sql_total_approved)->fetch_assoc()['count'];

$sql_rejected = "SELECT COUNT(*) AS count FROM guests WHERE approved = -1";
$total_rejected = $conn->query($sql_rejected)->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Officer Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
  <style>
   
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; padding: 20px; margin: 0; color: #333; }
    .dashboard-container { max-width: 1200px; margin: 0 auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; }
    .header { background: #8BC43F; color: white; padding: 25px; text-align: center; display: flex; align-items: center; justify-content: space-between; position: relative; }
    .logo { width: 150px; max-width: 100%; }
    .header-text { flex-grow: 1; padding: 0 20px; }
    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
    .header p { margin: 8px 0 0; font-size: 16px; opacity: 0.9; }
    .user-profile { display: flex; align-items: center; position: absolute; right: 20px; top: 20px; }
    .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #fff; color: green; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-left: 10px; }
    .content { padding: 30px; }
    .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: #fff; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-top: 4px solid #2E7D32; transition: transform 0.3s, box-shadow 0.3s; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .stat-card h3 { margin: 0; color: #666; font-size: 15px; font-weight: 500; }
    .stat-card p { margin: 12px 0 0; font-size: 28px; font-weight: 700; color: green; }
    .stat-card .trend { font-size: 14px; margin-top: 5px; display: flex; align-items: center; justify-content: center; }
    .trend.up { color: #4CAF50; }
    .trend.down { color: #F44336; }
    table { width: 100%; border-collapse: collapse; margin-top: 30px; font-size: 14px; }
    th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #eee; }
    th { background-color: green; color: white; font-weight: 500; position: sticky; top: 0; }
    tr:hover { background-color: #f9f9f9; }
    .action-btns { display: flex; gap: 8px; }
    .action-btn { padding: 6px 12px; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s; display: flex; align-items: center; }
    .action-btn i { margin-right: 5px; }
    .approve-btn { background: #4CAF50; }
    .approve-btn:hover { background: #3d8b40; box-shadow: 0 2px 5px rgba(76, 175, 80, 0.3); }
    .reject-btn { background: #F44336; }
    .reject-btn:hover { background: #d32f2f; box-shadow: 0 2px 5px rgba(244, 67, 54, 0.3); }
    .view-btn { background: #2196F3; }
    .view-btn:hover { background: #1976D2; }
    .footer-actions { display: flex; justify-content: space-between; margin-top: 30px; align-items: center; }
    .add-guest-btn { padding: 10px 20px; background: #2E7D32; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; transition: all 0.2s; display: inline-flex; align-items: center; }
    .add-guest-btn:hover { background: #1B5E20; box-shadow: 0 2px 8px rgba(46, 125, 50, 0.3); }
    .logout-btn { padding: 10px 20px; background: #F44336; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; transition: all 0.2s; }
    .logout-btn:hover { background: #d32f2f; box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3); }
    .pagination { display: flex; gap: 5px; }
    .pagination-btn { padding: 8px 12px; background: #f0f0f0; border: none; border-radius: 4px; cursor: pointer; transition: all 0.2s; }
    .pagination-btn.active { background: #2E7D32; color: white; }
    .pagination-btn:hover:not(.active) { background: #e0e0e0; }
    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500; }
    .status-pending { background: #FFF3E0; color: #E65100; }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="header">
      <img src="ethio-telecom-logo.png" alt="Ethio Telecom Logo" class="logo">
      <div class="header-text">
        <h1>Officer Dashboard</h1>
        <p>Guest Approval System</p>
      </div>
      <div class="user-profile">
        <span>Officer: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'],0,2)); ?></div>
      </div>
    </div>

    <div class="content">
      <div class="stats-container">
        <div class="stat-card">
          <h3>Pending Approvals</h3>
          <p><?php echo $total_pending; ?></p>
          <div class="trend up"><i class="fas fa-arrow-up"></i> New</div>
        </div>
        <div class="stat-card">
          <h3>Approved Today</h3>
          <p><?php echo $approved_today; ?></p>
          <div class="trend up"><i class="fas fa-arrow-up"></i> Today</div>
        </div>
        <div class="stat-card">
          <h3>Total Approved</h3>
          <p><?php echo $total_approved; ?></p>
          <div class="trend up"><i class="fas fa-arrow-up"></i> Total</div>
        </div>
        <div class="stat-card">
          <h3>Rejected</h3>
          <p><?php echo $total_rejected; ?></p>
          <div class="trend down"><i class="fas fa-arrow-down"></i> Total</div>
        </div>
      </div>

      <h2 style="color: #2E7D32; border-bottom: 2px solid #E0E0E0; padding-bottom: 8px;">
        <i class="fas fa-clock"></i> Pending Approvals
      </h2>

      <table>
        <thead>
          <tr>
            <th>Guest Name</th>
            <th>ID Number</th>
            <th>Visit Date</th>
            <th>Host Department</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($total_pending > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td><i class='fas fa-user'></i> " . htmlspecialchars($row['full_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['id_number']) . "</td>";
              echo "<td>" . (isset($row['visit_date']) ? htmlspecialchars($row['visit_date']) : '--') . "</td>";
              echo "<td>" . htmlspecialchars($row['office_number']) . "</td>";
              echo "<td><span class='status-badge status-pending'>Pending</span></td>";
              if (isset($_SESSION['role']) && $_SESSION['role'] === 'officer') {
                echo "<td>
                  <div class='action-btns'>
                    <form action='approve.php' method='POST'>
                      <input type='hidden' name='guest_id' value='{$row['id']}'>
                      <button type='submit' class='action-btn approve-btn'><i class='fas fa-check'></i> Approve</button>
                    </form>
                    <form action='reject.php' method='POST'>
                      <input type='hidden' name='guest_id' value='{$row['id']}'>
                      <button type='submit' class='action-btn reject-btn'><i class='fas fa-times'></i> Reject</button>
                    </form>
                  </div>
                </td>";
              } else {
                echo "<td>-</td>";
              }
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='6'>No pending guests.</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <div class="footer-actions">
        <a href="guest_entry.html" class="add-guest-btn">
          <i class="fas fa-plus"></i> Add New Guest
        </a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'officer') { ?>
        <a href="approve.php" class="add-guest-btn" style="background:#2196F3; margin-left:10px;">
          <i class="fas fa-user-check"></i> Approve Guests
        </a>
        <?php } ?>

        <a href="approved_list.php" class="add-guest-btn" style="background:#4CAF50; margin-left:10px;">
          <i class="fas fa-list"></i> Approved List
        </a>

        <div class="pagination">
          <button class="pagination-btn active">1</button>
          <button class="pagination-btn">2</button>
          <button class="pagination-btn">3</button>
        </div>

        <a href="dashboard.php?logout=1" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i> Log Out
        </a>
      </div>
    </div>
  </div>
</body>
</html>
