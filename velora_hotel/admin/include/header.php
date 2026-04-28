<?php $current = basename($_SERVER['PHP_SELF'],'.php'); ?>
<div id="topbar">
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-sm d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
      <i class="bi bi-list fs-5"></i>
    </button>
    <h5 id="topbar-title"><?php $t=['dashboard'=>'Dashboard','rooms'=>'Room Management','bookings'=>'Bookings','guests'=>'Guest Records','billing'=>'Billing & Invoices','staff'=>'Staff Management','settings'=>'Settings']; echo $t[$current]??ucfirst($current); ?></h5>
  </div>
  <div class="d-flex align-items-center gap-3">
    <div class="d-flex align-items-center gap-2">
      <div class="admin-badge">A</div>
      <div>
        <div style="font-size:13px;font-weight:600;color:#0f1a2c;"><?= htmlspecialchars($_SESSION['adminName']??'Admin') ?></div>
        <div style="font-size:11px;color:#64748b;">Administrator</div>
      </div>
    </div>
    <a href="logout.php" class="btn btn-sm btn-outline-secondary" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</div>
<div id="sidebar">
  <div class="sidebar-brand">
    <h4 class="h-font">VELORA</h4>
    <small>Hotel Admin Panel</small>
  </div>
  <div class="pt-2">
    <div class="nav-section-title">Main</div>
    <a href="dashboard.php" class="nav-link <?= $current=='dashboard'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <div class="nav-section-title">Manage</div>
    <a href="rooms.php" class="nav-link <?= $current=='rooms'?'active':'' ?>"><i class="bi bi-door-open"></i> Rooms</a>
    <a href="bookings.php" class="nav-link <?= $current=='bookings'?'active':'' ?>"><i class="bi bi-calendar-check"></i> Bookings</a>
    <a href="guests.php" class="nav-link <?= $current=='guests'?'active':'' ?>"><i class="bi bi-people"></i> Guests</a>
    <a href="billing.php" class="nav-link <?= $current=='billing'?'active':'' ?>"><i class="bi bi-receipt"></i> Billing</a>
    <a href="staff.php" class="nav-link <?= $current=='staff'?'active':'' ?>"><i class="bi bi-person-badge"></i> Staff</a>
    <div class="nav-section-title">System</div>
    <a href="settings.php" class="nav-link <?= $current=='settings'?'active':'' ?>"><i class="bi bi-gear"></i> Settings</a>
    <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</div>
