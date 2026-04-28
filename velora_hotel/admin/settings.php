<?php
require('include/essentials.php');
require('include/db_config.php');
adminLogin();

if (isset($_POST['update_general'])) {
  $d = filteration($_POST);
  $q = "UPDATE setting SET site_title=?, site_about=?, site_phone=?, site_email=?, site_address=? WHERE sr_no=1";
  $v = [$d['site_title'], $d['site_about'], $d['site_phone'], $d['site_email'], $d['site_address']];
  update($q, $v, "sssss") ? $success = "Settings updated!" : $error = "Failed.";
}

if (isset($_POST['change_password'])) {
  $d = filteration($_POST);
  $cur = $d['current_pass'];
  $new = $d['new_pass'];
  $con2 = $d['confirm_pass'];
  $admin_id = $_SESSION['adminId'];
  $res = select("SELECT * FROM admin_cred WHERE sr_no=? AND admin_pass=?", [$admin_id, $cur], "is");
  if ($res->num_rows == 1) {
    if ($new === $con2) {
      update("UPDATE admin_cred SET admin_pass=? WHERE sr_no=?", [$new, $admin_id], "si");
      $success = "Password changed successfully!";
    } else { $error = "New passwords do not match."; }
  } else { $error = "Current password is incorrect."; }
}

$settings = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM setting WHERE sr_no=1"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Velora Admin - Settings</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>
<div id="main-content">
  <div class="page-title h-font">Settings</div>
  <div class="page-subtitle">Manage hotel and account settings</div>
  <?php if (!empty($success)) alert('success', $success); ?>
  <?php if (!empty($error))   alert('error',   $error);   ?>

  <div class="row g-4">
    <!-- General Settings -->
    <div class="col-lg-7">
      <div class="section-card">
        <div class="section-header"><h5><i class="bi bi-gear me-2"></i>General Settings</h5></div>
        <div class="p-4">
          <form method="POST">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Site / Hotel Title</label>
                <input name="site_title" type="text" class="form-control" value="<?= htmlspecialchars($settings['site_title']??'') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">About Us</label>
                <textarea name="site_about" class="form-control" rows="3"><?= htmlspecialchars($settings['site_about']??'') ?></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Phone</label>
                <input name="site_phone" type="text" class="form-control" value="<?= htmlspecialchars($settings['site_phone']??'') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Email</label>
                <input name="site_email" type="email" class="form-control" value="<?= htmlspecialchars($settings['site_email']??'') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="site_address" class="form-control" rows="2"><?= htmlspecialchars($settings['site_address']??'') ?></textarea>
              </div>
              <div class="col-12">
                <button type="submit" name="update_general" class="btn custom-bg text-white">
                  <i class="bi bi-check-circle me-1"></i>Save Settings
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-5">
      <div class="section-card">
        <div class="section-header"><h5><i class="bi bi-shield-lock me-2"></i>Change Password</h5></div>
        <div class="p-4">
          <form method="POST">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Current Password</label>
                <input name="current_pass" type="password" class="form-control" required>
              </div>
              <div class="col-12">
                <label class="form-label">New Password</label>
                <input name="new_pass" type="password" class="form-control" required>
              </div>
              <div class="col-12">
                <label class="form-label">Confirm New Password</label>
                <input name="confirm_pass" type="password" class="form-control" required>
              </div>
              <div class="col-12">
                <button type="submit" name="change_password" class="btn btn-dark text-white w-100">
                  <i class="bi bi-key me-1"></i>Change Password
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Quick Info -->
      <div class="section-card mt-4">
        <div class="section-header"><h5><i class="bi bi-info-circle me-2"></i>System Info</h5></div>
        <div class="p-4">
          <table class="table table-sm mb-0" style="font-size:13px;">
            <tr><td class="text-muted">PHP Version</td><td><?= phpversion() ?></td></tr>
            <tr><td class="text-muted">MySQL</td><td><?= mysqli_get_server_info($con) ?></td></tr>
            <tr><td class="text-muted">Logged in as</td><td><?= htmlspecialchars($_SESSION['adminName']??'Admin') ?></td></tr>
            <tr><td class="text-muted">Date</td><td><?= date('d M Y, h:i A') ?></td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require('include/scripts.php'); ?>
</body>
</html>
