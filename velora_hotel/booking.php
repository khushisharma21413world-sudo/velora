<?php
session_start();
require('include/db.php');

$room_id   = (int)($_GET['room_id']??0);
$check_in  = $_GET['check_in']??'';
$check_out = $_GET['check_out']??'';

if(!$room_id) { header("Location: rooms.php"); exit(); }

$room = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM rooms WHERE sr_no=$room_id AND status='Available'"));
if(!$room) { header("Location: rooms.php"); exit(); }

$success = $error = '';

if(isset($_POST['book_now'])) {
  $name     = trim($_POST['guest_name']);
  $phone    = trim($_POST['guest_phone']);
  $email    = trim($_POST['guest_email']);
  $idtype   = trim($_POST['id_proof_type']);
  $idnum    = trim($_POST['id_proof_number']);
  $cin      = trim($_POST['check_in']);
  $cout     = trim($_POST['check_out']);
  $adults   = (int)$_POST['num_adults'];
  $children = (int)$_POST['num_children'];
  $special  = trim($_POST['special_requests']);

  $days   = max(1, (strtotime($cout)-strtotime($cin))/86400);
  $total  = $room['price_per_night'] * $days;
  $bknum  = 'BK-'.date('Ymd').'-'.rand(1000,9999);

  // Save guest
  $gq = "INSERT INTO guests (name,phone,email,id_proof_type,id_proof_number) VALUES (?,?,?,?,?)";
  mysqli_query($con, "INSERT INTO guests (name,phone,email,id_proof_type,id_proof_number) VALUES ('".mysqli_real_escape_string($con,$name)."','".mysqli_real_escape_string($con,$phone)."','".mysqli_real_escape_string($con,$email)."','".mysqli_real_escape_string($con,$idtype)."','".mysqli_real_escape_string($con,$idnum)."')");

  // Save booking
  $bq = "INSERT INTO bookings (booking_number,guest_name,guest_phone,guest_email,id_proof_type,id_proof_number,room_id,check_in,check_out,num_adults,num_children,total_amount,special_requests,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
  $bstmt = mysqli_prepare($con,$bq);
  $status = 'Confirmed';
  mysqli_stmt_bind_param($bstmt,"sssssssissidss",$bknum,$name,$phone,$email,$idtype,$idnum,$room_id,$cin,$cout,$adults,$children,$total,$special,$status);

  if(mysqli_stmt_execute($bstmt)) {
    mysqli_query($con,"UPDATE rooms SET status='Reserved' WHERE sr_no=$room_id");
    $success = $bknum;
    $_SESSION['last_booking'] = $bknum;
  } else {
    $error = "Booking failed. Please try again.";
  }
}

$days  = ($check_in && $check_out) ? max(1,(strtotime($check_out)-strtotime($check_in))/86400) : 1;
$total = $room['price_per_night'] * $days;
$features   = explode(',', $room['features']??'');
$facilities = explode(',', $room['facilities']??'');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VELORA HOTEL - Book <?= htmlspecialchars($room['room_name']) ?></title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>

<div class="container py-5">

<?php if($success): ?>
<!-- SUCCESS -->
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="bg-white rounded shadow p-5 text-center">
      <div style="width:70px;height:70px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
        <i class="bi bi-check-lg text-success" style="font-size:32px;"></i>
      </div>
      <h3 class="text-success mb-2">Booking Confirmed!</h3>
      <p class="text-muted mb-3">Your booking has been received successfully.</p>
      <div class="bg-light rounded p-3 mb-4">
        <h5 class="fw-bold"><?= htmlspecialchars($success) ?></h5>
        <p class="text-muted mb-0">Please save this booking number for reference.</p>
      </div>
      <div class="text-start" style="font-size:14px;">
        <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Room</span><strong><?= htmlspecialchars($room['room_name']) ?></strong></div>
        <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-in</span><strong><?= $_POST['check_in'] ?></strong></div>
        <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-out</span><strong><?= $_POST['check_out'] ?></strong></div>
        <div class="d-flex justify-content-between py-2"><span class="text-muted">Total Amount</span><strong class="text-success">₹<?= number_format($total) ?></strong></div>
      </div>
      <a href="index.php" class="btn custom-bg text-white mt-4 w-100">Back to Home</a>
    </div>
  </div>
</div>

<?php else: ?>
<h2 class="fw-bold h-font mb-4">Complete Your Booking</h2>
<div class="row g-4">

  <!-- BOOKING FORM -->
  <div class="col-lg-7">
    <div class="bg-white rounded shadow p-4">
      <h5 class="mb-4 border-bottom pb-3"><i class="bi bi-person-fill me-2"></i>Guest Details</h5>

      <?php if($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Full Name *</label>
            <input type="text" name="guest_name" class="form-control shadow-none" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Phone Number *</label>
            <input type="text" name="guest_phone" class="form-control shadow-none" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="guest_email" class="form-control shadow-none">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">ID Proof Type *</label>
            <select name="id_proof_type" class="form-select shadow-none" required>
              <option value="">Select ID Proof</option>
              <option>Aadhar</option><option>Passport</option>
              <option>PAN Card</option><option>Driving License</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">ID Proof Number *</label>
            <input type="text" name="id_proof_number" class="form-control shadow-none" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Check-in Date *</label>
            <input type="date" name="check_in" class="form-control shadow-none" value="<?= htmlspecialchars($check_in) ?>" min="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Check-out Date *</label>
            <input type="date" name="check_out" class="form-control shadow-none" value="<?= htmlspecialchars($check_out) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Adults</label>
            <select name="num_adults" class="form-select shadow-none">
              <?php for($i=1;$i<=$room['max_adults'];$i++): ?>
              <option value="<?= $i ?>"><?= $i ?> Adult<?= $i>1?'s':'' ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Children</label>
            <select name="num_children" class="form-select shadow-none">
              <?php for($i=0;$i<=$room['max_children'];$i++): ?>
              <option value="<?= $i ?>"><?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Special Requests</label>
            <textarea name="special_requests" class="form-control shadow-none" rows="3" placeholder="Any special requirements..."></textarea>
          </div>

          <div class="col-12">
            <div class="alert alert-warning py-2 mb-0" style="font-size:13px;">
              <i class="bi bi-info-circle me-1"></i>Your ID proof details must match during check-in.
            </div>
          </div>
        </div>
        <button type="submit" name="book_now" class="btn custom-bg text-white w-100 mt-4 py-3 fw-bold">
          <i class="bi bi-calendar-check me-2"></i>Confirm Booking
        </button>
      </form>
    </div>
  </div>

  <!-- ROOM SUMMARY -->
  <div class="col-lg-5">
    <div class="bg-white rounded shadow overflow-hidden sticky-top" style="top:80px;">
      <div style="height:220px;overflow:hidden;">
        <img src="<?= htmlspecialchars($room['room_image']) ?>" class="w-100 h-100" style="object-fit:cover;">
      </div>
      <div class="p-4">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h5 class="mb-0"><?= htmlspecialchars($room['room_name']) ?></h5>
          <span class="badge bg-success">Available</span>
        </div>
        <p class="text-muted mb-3" style="font-size:13px;">Room <?= $room['room_number'] ?> &bull; Floor <?= $room['floor_number'] ?></p>

        <div class="mb-2">
          <?php foreach($features as $f): if(trim($f)): ?>
          <span class="badge rounded-pill bg-light text-dark me-1 mb-1"><?= trim(htmlspecialchars($f)) ?></span>
          <?php endif; endforeach; ?>
        </div>
        <div class="mb-3">
          <?php foreach($facilities as $f): if(trim($f)): ?>
          <span class="badge rounded-pill bg-light text-dark me-1 mb-1"><?= trim(htmlspecialchars($f)) ?></span>
          <?php endif; endforeach; ?>
        </div>

        <div class="border-top pt-3">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted" style="font-size:14px;">Price / Night</span>
            <span>₹<?= number_format($room['price_per_night']) ?></span>
          </div>
          <?php if($days > 1): ?>
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted" style="font-size:14px;">Duration</span>
            <span><?= $days ?> nights</span>
          </div>
          <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2">
            <span>Estimated Total</span>
            <span class="text-success">₹<?= number_format($total) ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</div>
<?php endif; ?>
</div>

<?php require('include/footer.php'); ?>
</body>
</html>
