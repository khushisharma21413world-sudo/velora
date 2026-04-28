<?php
require('include/essentials.php');
require('include/db_config.php');

if (isset($_POST['login'])) {
  $frm_data = filteration($_POST);
  $query = "SELECT * FROM `admin_cred` WHERE `admin_name`=? AND `admin_pass`=?";
  $values = [$frm_data['admin_name'], $frm_data['admin_pass']];
  $res = select($query, $values, "ss");
  if ($res->num_rows == 1) {
    $row = mysqli_fetch_assoc($res);
    session_start();
    $_SESSION['adminLogin'] = true;
    $_SESSION['adminId']    = $row['sr_no'];
    $_SESSION['adminName']  = $row['admin_name'];
    redirect('dashboard.php');
  } else {
    $login_error = true;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Velora Hotel - Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: #0f1a2c; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { width: 400px; background: #162032; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
    .login-header { background: linear-gradient(135deg, #162032, #1e3a5f); padding: 30px 30px 20px; text-align: center; }
    .hotel-logo { width: 56px; height: 56px; background: #f6ac0f; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 24px; color: #000; font-weight: 700; font-family: 'Playfair Display', serif; }
    .login-header h3 { color: #f6ac0f; font-family: 'Playfair Display', serif; font-size: 22px; margin: 0 0 4px; }
    .login-header p { color: #94a3b8; font-size: 12px; margin: 0; }
    .login-body { padding: 28px 30px; }
    .form-label { color: #94a3b8; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control { background: #0f1a2c; border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; padding: 10px 14px 10px 40px; font-size: 14px; }
    .form-control:focus { background: #0f1a2c; border-color: #2ec1ac; color: #fff; box-shadow: 0 0 0 3px rgba(46,193,172,0.15); }
    .form-control::placeholder { color: #4a5568; }
    .input-icon { position: relative; }
    .input-icon i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #4a5568; font-size: 14px; }
    .btn-login { background: #2ec1ac; border: none; color: #fff; width: 100%; padding: 11px; border-radius: 8px; font-size: 14px; font-weight: 600; letter-spacing: 0.5px; transition: all 0.3s; }
    .btn-login:hover { background: #279e8c; color: #fff; transform: translateY(-1px); }
    .error-msg { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; }
    .divider { border-color: rgba(255,255,255,0.08); }
    .login-footer { text-align: center; padding: 14px 30px 24px; color: #4a5568; font-size: 12px; }
  </style>
</head>
<body>
  <div class="login-card shadow-lg">
    <div class="login-header">
      <div class="hotel-logo">V</div>
      <h3>VELORA HOTEL</h3>
      <p>Admin Management Panel</p>
    </div>
    <div class="login-body">
      <?php if (!empty($login_error)): ?>
        <div class="error-msg"><i class="bi bi-exclamation-circle me-2"></i>Invalid username or password.</div>
      <?php endif; ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <div class="input-icon">
            <i class="bi bi-person"></i>
            <input name="admin_name" type="text" class="form-control" placeholder="Enter admin name" required>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Password</label>
          <div class="input-icon">
            <i class="bi bi-lock"></i>
            <input name="admin_pass" type="password" class="form-control" placeholder="Enter password" required>
          </div>
        </div>
        <button name="login" type="submit" class="btn-login">LOGIN TO ADMIN PANEL</button>
      </form>
    </div>
    <div class="login-footer">Default: admin / admin123</div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
