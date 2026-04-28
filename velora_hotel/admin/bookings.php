<?php
require('include/essentials.php');
require('include/db_config.php');
adminLogin();

// Update booking status
if(isset($_POST['update_status'])){
  $id = (int)$_POST['bid'];
  $st = mysqli_real_escape_string($con,$_POST['status']);
  mysqli_query($con,"UPDATE bookings SET status='$st' WHERE sr_no=$id");
  if($st=='Checkout'||$st=='Cancelled'){
    $rid = mysqli_fetch_assoc(mysqli_query($con,"SELECT room_id FROM bookings WHERE sr_no=$id"))['room_id'];
    mysqli_query($con,"UPDATE rooms SET status='Available' WHERE sr_no=$rid");
  }
  $success="Status updated!";
}

// Delete booking
if(isset($_GET['delete'])){
  $id  = (int)$_GET['delete'];
  $rid = mysqli_fetch_assoc(mysqli_query($con,"SELECT room_id FROM bookings WHERE sr_no=$id"))['room_id'];
  mysqli_query($con,"DELETE FROM bookings WHERE sr_no=$id");
  mysqli_query($con,"UPDATE rooms SET status='Available' WHERE sr_no=$rid");
  $success="Booking deleted.";
}

$filter=$_GET['f']??'';
$where=$filter?"WHERE b.status='".mysqli_real_escape_string($con,$filter)."'":'';
$bookings=mysqli_query($con,
  "SELECT b.*, r.room_name, r.room_number, r.room_type, r.room_image
   FROM bookings b JOIN rooms r ON b.room_id=r.sr_no
   $where ORDER BY b.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Velora Admin - Bookings</title><?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>
<div id="main-content">
  <div class="page-title h-font">Bookings</div>
  <div class="page-subtitle">All customer bookings from the website</div>
  <?php if(!empty($success)) alert('success',$success); ?>

  <div class="section-card">
    <div class="section-header">
      <h5><i class="bi bi-calendar-check me-2"></i>All Bookings</h5>
      <form method="GET">
        <select name="f" class="form-select form-select-sm" onchange="this.form.submit()" style="width:140px;">
          <option value="">All Status</option>
          <option value="Confirmed" <?=$filter=='Confirmed'?'selected':''?>>Confirmed</option>
          <option value="Occupied"  <?=$filter=='Occupied' ?'selected':''?>>Occupied</option>
          <option value="Checkout"  <?=$filter=='Checkout' ?'selected':''?>>Checkout</option>
          <option value="Cancelled" <?=$filter=='Cancelled'?'selected':''?>>Cancelled</option>
        </select>
      </form>
    </div>
    <div class="table-responsive">
      <table class="table admin-table mb-0">
        <thead><tr><th>Booking #</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Guests</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if(mysqli_num_rows($bookings)>0): while($b=mysqli_fetch_assoc($bookings)): ?>
          <tr>
            <td><small class="fw-semibold text-muted"><?=$b['booking_number']?></small><br><small class="text-muted"><?=date('d M Y, h:i A',strtotime($b['created_at']))?></small></td>
            <td>
              <div class="fw-semibold" style="font-size:13px;"><?=htmlspecialchars($b['guest_name'])?></div>
              <small class="text-muted"><?=$b['guest_phone']?></small><br>
              <small class="text-muted"><?=htmlspecialchars($b['guest_email']??'')?></small>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <img src="../<?=htmlspecialchars($b['room_image'])?>" class="room-thumb">
                <div>
                  <div style="font-size:13px;"><?=htmlspecialchars($b['room_name'])?></div>
                  <small class="text-muted">Room <?=$b['room_number']?> &bull; <?=$b['room_type']?></small>
                </div>
              </div>
            </td>
            <td><?=date('d M Y',strtotime($b['check_in']))?></td>
            <td><?=date('d M Y',strtotime($b['check_out']))?></td>
            <td><small><?=$b['num_adults']?>A / <?=$b['num_children']?>C</small></td>
            <td><strong>&#8377;<?=number_format($b['total_amount'])?></strong></td>
            <td>
              <form method="POST" class="d-inline">
                <input type="hidden" name="bid" value="<?=$b['sr_no']?>">
                <select name="status" class="form-select form-select-sm" style="width:110px;" onchange="this.form.submit()">
                  <option value="Confirmed" <?=$b['status']=='Confirmed'?'selected':''?>>Confirmed</option>
                  <option value="Occupied"  <?=$b['status']=='Occupied' ?'selected':''?>>Occupied</option>
                  <option value="Checkout"  <?=$b['status']=='Checkout' ?'selected':''?>>Checkout</option>
                  <option value="Cancelled" <?=$b['status']=='Cancelled'?'selected':''?>>Cancelled</option>
                </select>
                <input type="hidden" name="update_status" value="1">
              </form>
            </td>
            <td>
              <a href="?delete=<?=$b['sr_no']?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete booking?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="9" class="text-center text-muted py-4">No bookings yet. Bookings from your website will appear here.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require('include/scripts.php'); ?>
</body></html>
