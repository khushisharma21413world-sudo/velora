<?php
require('include/essentials.php');
require('include/db_config.php');
adminLogin();

$total_rooms     = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) c FROM rooms"))['c'];
// $available_rooms = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) c FROM rooms WHERE status='Available'"))['c'];
$available_rooms = mysqli_fetch_assoc(mysqli_query($con,
"SELECT COUNT(*) c FROM rooms r
WHERE NOT EXISTS (
  SELECT 1 FROM bookings b 
  WHERE b.room_id = r.sr_no 
  AND b.status IN ('Occupied','Confirmed')
)"))['c'];
$total_bookings  = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) c FROM bookings"))['c'];
$today_bookings  = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) c FROM bookings WHERE DATE(created_at)=CURDATE()"))['c'];
$total_revenue   = mysqli_fetch_assoc(mysqli_query($con,"SELECT COALESCE(SUM(paid_amount),0) r FROM invoices"))['r'];
$pending_bills   = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) c FROM invoices WHERE payment_status='Pending'"))['c'];
$total_guests    = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) c FROM guests"))['c'];

// Recent bookings from FRONTEND customers
$recent = mysqli_query($con,
  "SELECT b.*, r.room_name, r.room_number, r.room_type, r.room_image
   FROM bookings b
   JOIN rooms r ON b.room_id=r.sr_no
   ORDER BY b.created_at DESC LIMIT 8");

// Room status
// $rooms_list = mysqli_query($con,"SELECT room_number,room_name,room_type,price_per_night,status FROM rooms ORDER BY room_number LIMIT 6");
 $rooms_list = mysqli_query($con,
"SELECT r.room_number, r.room_name, r.room_type, r.price_per_night,
CASE 
  WHEN EXISTS (
    SELECT 1 FROM bookings b 
    WHERE b.room_id = r.sr_no 
    AND b.status IN ('Occupied','Confirmed')
  )
  THEN 'Occupied'
  ELSE 'Available'
END AS final_status
FROM rooms r
ORDER BY r.room_number LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Velora Admin - Dashboard</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>

<div id="main-content">
  <div class="page-title h-font">Dashboard</div>
  <div class="page-subtitle">Welcome back! Here's what's happening at Velora today.</div>

  <!-- STAT CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-label">Total Rooms</div>
            <div class="stat-value"><?= $total_rooms ?></div>
            <div class="stat-change text-success"><i class="bi bi-check-circle"></i> <?= $available_rooms ?> Available</div>
          </div>
          <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;font-size:20px;width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-door-open"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value"><?= $total_bookings ?></div>
            <div class="stat-change text-success"><i class="bi bi-calendar-plus"></i> <?= $today_bookings ?> today</div>
          </div>
          <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;font-size:20px;width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-calendar-check"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">&#8377;<?= number_format($total_revenue) ?></div>
            <div class="stat-change text-warning"><i class="bi bi-receipt"></i> <?= $pending_bills ?> pending</div>
          </div>
          <div class="stat-icon" style="background:#fdf4ff;color:#9333ea;font-size:20px;width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-currency-rupee"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-label">Total Guests</div>
            <div class="stat-value"><?= $total_guests ?></div>
            <div class="stat-change text-muted"><i class="bi bi-people"></i> Registered</div>
          </div>
          <div class="stat-icon" style="background:#fff7ed;color:#ea580c;font-size:20px;width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-people"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- RECENT BOOKINGS (from frontend customers) + ROOMS -->
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="section-card">
        <div class="section-header">
          <h5><i class="bi bi-calendar-check me-2"></i>Customer Bookings <span class="badge bg-warning text-dark ms-2" style="font-size:11px;">Live from Website</span></h5>
          <a href="bookings.php" class="btn btn-sm custom-bg text-white">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table admin-table mb-0">
            <thead>
              <tr><th>Booking #</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Amount</th><th>Status</th></tr>
            </thead>
            <tbody>
              <?php if(mysqli_num_rows($recent)>0): while($b=mysqli_fetch_assoc($recent)): ?>
              <tr>
                <td><small class="fw-semibold text-muted"><?= htmlspecialchars($b['booking_number']) ?></small></td>
                <td>
                  <div style="font-size:13px;"><?= htmlspecialchars($b['guest_name']) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($b['guest_phone']) ?></small>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <img src="../<?= htmlspecialchars($b['room_image']) ?>" class="room-thumb">
                    <div>
                      <div style="font-size:13px;"><?= htmlspecialchars($b['room_name']) ?></div>
                      <small class="text-muted">Room <?= $b['room_number'] ?></small>
                    </div>
                  </div>
                </td>
                <td style="font-size:13px;"><?= date('d M Y',strtotime($b['check_in'])) ?></td>
                <td style="font-size:13px;"><?= date('d M Y',strtotime($b['check_out'])) ?></td>
                <td><strong>&#8377;<?= number_format($b['total_amount']) ?></strong></td>
                  <td><span class="status-badge badge-<?= strtolower($b['status']) ?>"><?= $b['status'] ?></span></td>
              </tr>
              <?php endwhile; else: ?>
              <tr><td colspan="7" class="text-center text-muted py-4">No bookings yet. Customers will appear here when they book from the website.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="section-card">
        <div class="section-header">
          <h5><i class="bi bi-door-open me-2"></i>Room Status</h5>
          <a href="rooms.php" class="btn btn-sm btn-outline-secondary">Manage</a>
        </div>
        <div class="table-responsive">
          <table class="table admin-table mb-0">
            <thead><tr><th>Room</th><th>Type</th><th>Price</th><th>Status</th></tr></thead>
            <tbody>
              <?php while($r=mysqli_fetch_assoc($rooms_list)): ?>
              <tr>
                <td><strong><?= $r['room_number'] ?></strong></td>
                <td style="font-size:12px;"><?= $r['room_type'] ?></td>
                <td style="font-size:12px;">&#8377;<?= number_format($r['price_per_night']) ?></td>
              <td>
                 <span class="status-badge badge-<?= strtolower($r['final_status']) ?>">
                   <?= $r['final_status'] ?>
                 </span>
               </td> 


              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require('include/scripts.php'); ?>
</body>
</html>
