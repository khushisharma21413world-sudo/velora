<?php
require('include/essentials.php');
require('include/db_config.php');
adminLogin();
$id = (int)($_GET['id']??0);
// $inv = mysqli_fetch_assoc(mysqli_query($con,
//   "SELECT i.*, b.booking_number, b.check_in, b.check_out, b.num_guests,
//           g.name as guest_name, g.phone, g.email, g.address,
//           r.room_number, r.room_type,
//           s.site_title, s.site_phone, s.site_email, s.site_address
//    FROM invoices i
//    JOIN bookings b ON i.booking_id=b.sr_no
//    JOIN guests g ON b.user_id=g.sr_no
//    JOIN rooms r ON b.room_id=r.sr_no
//    LEFT JOIN setting s ON s.sr_no=1
//    WHERE i.sr_no=$id"));
   $inv = mysqli_fetch_assoc(mysqli_query($con,
  "SELECT i.*, 
          b.booking_number, b.check_in, b.check_out,
          (b.num_adults + b.num_children) AS num_guests,
          b.guest_name, b.guest_phone as phone, b.guest_email as email,
          r.room_number, r.room_type,
          s.site_title, s.site_phone, s.site_email, s.site_address
   FROM invoices i
   JOIN bookings b ON i.booking_id=b.sr_no
   JOIN rooms r ON b.room_id=r.sr_no
   LEFT JOIN setting s ON s.sr_no=1
   WHERE i.sr_no=$id"));
if (!$inv) die("Invoice not found.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice <?= $inv['invoice_number'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; padding: 30px; }
    .inv-header { background: #0f1a2c; color: #fff; padding: 24px 30px; border-radius: 8px 8px 0 0; }
    .inv-header h2 { color: #f6ac0f; margin: 0; font-size: 24px; }
    .inv-body { border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 8px 8px; padding: 24px 30px; }
    .label { color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
    .value { font-size: 14px; font-weight: 500; color: #0f1a2c; }
    .total-row { background: #0f1a2c; color: #fff; }
    .total-row td { color: #f6ac0f; font-size: 16px; font-weight: 700; }
    @media print { .no-print { display: none; } body { padding: 0; } }
  </style>
</head>
<body>
  <div style="max-width:720px;margin:auto;">
    <div class="inv-header d-flex justify-content-between align-items-start">
      <div>
        <h2><?= htmlspecialchars($inv['site_title']??'Velora Hotel') ?></h2>
        <small style="color:#94a3b8;"><?= htmlspecialchars($inv['site_address']??'') ?></small><br>
        <small style="color:#94a3b8;">Ph: <?= $inv['site_phone']??'' ?> | <?= $inv['site_email']??'' ?></small>
      </div>
      <div class="text-end">
        <div style="font-size:20px;font-weight:700;color:#f6ac0f;">INVOICE</div>
        <div style="color:#94a3b8;"><?= $inv['invoice_number'] ?></div>
        <div style="color:#94a3b8;font-size:12px;"><?= date('d M Y', strtotime($inv['created_at'])) ?></div>
      </div>
    </div>
    <div class="inv-body">
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="label mb-1">Billed To</div>
          <div class="value"><?= htmlspecialchars($inv['guest_name']) ?></div>
          <div style="font-size:13px;color:#64748b;"><?= $inv['phone'] ?></div>
          <div style="font-size:13px;color:#64748b;"><?= $inv['email']??'' ?></div>
          <div style="font-size:13px;color:#64748b;"><?= $inv['address']??'' ?></div>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="label mb-1">Booking Details</div>
          <div class="value"><?= $inv['booking_number'] ?></div>
          <div style="font-size:13px;color:#64748b;">Room <?= $inv['room_number'] ?> &mdash; <?= $inv['room_type'] ?></div>
          <div style="font-size:13px;color:#64748b;">Check-in: <?= date('d M Y', strtotime($inv['check_in'])) ?></div>
          <div style="font-size:13px;color:#64748b;">Check-out: <?= date('d M Y', strtotime($inv['check_out'])) ?></div>
          <div style="font-size:13px;color:#64748b;">Guests: <?= $inv['num_guests'] ?></div>
        </div>
      </div>
      <table class="table" style="font-size:14px;">
        <thead style="background:#f8fafc;">
          <tr><th>Description</th><th class="text-end">Amount</th></tr>
        </thead>
        <tbody>
          <tr><td>Room Charges</td><td class="text-end">Rs.<?= number_format($inv['room_charges'],2) ?></td></tr>
          <?php if ($inv['extra_charges'] > 0): ?><tr><td>Extra Charges</td><td class="text-end">Rs.<?= number_format($inv['extra_charges'],2) ?></td></tr><?php endif; ?>
          <?php if ($inv['discount'] > 0): ?><tr><td>Discount</td><td class="text-end text-success">- Rs.<?= number_format($inv['discount'],2) ?></td></tr><?php endif; ?>
          <tr><td>Tax (<?= $inv['tax_percent'] ?>%)</td><td class="text-end">Rs.<?= number_format(($inv['room_charges']+$inv['extra_charges']-$inv['discount'])*$inv['tax_percent']/100,2) ?></td></tr>
          <tr class="total-row"><td><strong>TOTAL</strong></td><td class="text-end">Rs.<?= number_format($inv['total_amount'],2) ?></td></tr>
        </tbody>
      </table>
      <div class="row mt-3">
        <div class="col-md-6">
          <div class="label">Payment Method</div>
          <div class="value"><?= $inv['payment_method'] ?></div>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="label">Amount Paid</div>
          <div class="value text-success">Rs.<?= number_format($inv['paid_amount'],2) ?></div>
          <?php if ($inv['total_amount'] - $inv['paid_amount'] > 0): ?>
          <div class="label mt-1">Balance Due</div>
          <div class="value text-danger">Rs.<?= number_format($inv['total_amount']-$inv['paid_amount'],2) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-dark px-5"><i class="bi bi-printer me-2"></i>Print Invoice</button>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</body>
</html>
