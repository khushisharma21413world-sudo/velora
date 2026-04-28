<?php
require('include/essentials.php');
require('include/db_config.php');
adminLogin();

if (isset($_POST['add_invoice'])) {
  $d = filteration($_POST);
  $room_charges = (float)$d['room_charges'];
  $extra        = (float)$d['extra_charges'];
  $discount     = (float)$d['discount'];
  $tax_pct      = (float)$d['tax_percent'];
  $subtotal     = $room_charges + $extra - $discount;
  $tax_amt      = $subtotal * $tax_pct / 100;
  $total        = $subtotal + $tax_amt;
  $inv_num      = 'INV-' . date('Ymd') . '-' . rand(100,999);
  $q = "INSERT INTO invoices (invoice_number, booking_id, room_charges, extra_charges, discount, tax_percent, total_amount, paid_amount, payment_method, payment_status) VALUES (?,?,?,?,?,?,?,?,?,?)";
  $v = [$inv_num, (int)$d['booking_id'], $room_charges, $extra, $discount, $tax_pct, $total, (float)$d['paid_amount'], $d['payment_method'], $d['payment_status']];
  insert($q, $v, "siddddddss") ? $success = "Invoice $inv_num created!" : $error = "Failed to create invoice.";
}

if (isset($_POST['update_payment'])) {
  $id   = (int)$_POST['inv_id'];
  $paid = (float)$_POST['paid_amount'];
  $meth = mysqli_real_escape_string($con, $_POST['payment_method']);
  $stat = mysqli_real_escape_string($con, $_POST['payment_status']);
  mysqli_query($con, "UPDATE invoices SET paid_amount=$paid, payment_method='$meth', payment_status='$stat' WHERE sr_no=$id");
  $success = "Payment updated!";
}

if (isset($_GET['delete'])) {
  mysqli_query($con, "DELETE FROM invoices WHERE sr_no=".(int)$_GET['delete']);
  $success = "Invoice deleted.";
}

if (isset($_GET['get_inv'])) {
  $row = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT i.*, b.booking_number, g.name as guest_name, r.room_number
     FROM invoices i
     JOIN bookings b ON i.booking_id=b.sr_no
     JOIN guests g ON b.guest_id=g.sr_no
     JOIN rooms r ON b.room_id=r.sr_no
     WHERE i.sr_no=".(int)$_GET['get_inv']));
  echo json_encode($row); exit;
}

$filter   = isset($_GET['filter']) ? mysqli_real_escape_string($con,$_GET['filter']) : '';
$where    = $filter ? "WHERE i.payment_status='$filter'" : "";
// $invoices = mysqli_query($con,
//   "SELECT i.*, b.booking_number, g.name as guest_name, r.room_number
//    FROM invoices i
//    JOIN bookings b ON i.booking_id=b.sr_no
//    JOIN guests g ON b.user_id=g.sr_no
//    JOIN rooms r ON b.room_id=r.sr_no
//    $where ORDER BY i.created_at DESC");
 $invoices = mysqli_query($con,
  "SELECT i.*, b.booking_number, b.guest_name as guest_name, r.room_number
   FROM invoices i
   JOIN bookings b ON i.booking_id=b.sr_no
   JOIN rooms r ON b.room_id=r.sr_no
   $where ORDER BY i.created_at DESC");

// $pending_bookings = mysqli_query($con,
//   "SELECT b.sr_no, b.booking_number, g.name, r.room_number, r.room_type, b.total_amount
//    FROM bookings b JOIN guests g ON b.user_id=g.sr_no JOIN rooms r ON b.room_id=r.sr_no
//    WHERE b.status IN ('Occupied','Checkout')
//    AND b.sr_no NOT IN (SELECT booking_id FROM invoices)
//    ORDER BY b.created_at DESC");
$pending_bookings = mysqli_query($con,
  "SELECT b.sr_no, b.booking_number, b.guest_name as name, r.room_number, r.room_type, b.total_amount
   FROM bookings b 
   JOIN rooms r ON b.room_id=r.sr_no
   WHERE 1
   ORDER BY b.created_at DESC");

$total_revenue  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(paid_amount),0) as t FROM invoices"))['t'];
$pending_amount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(total_amount-paid_amount),0) as t FROM invoices WHERE payment_status!='Paid'"))['t'];
$paid_count     = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM invoices WHERE payment_status='Paid'"))['c'];
$pending_count  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM invoices WHERE payment_status='Pending'"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Velora Admin - Billing</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>
<div id="main-content">
  <div class="page-title h-font">Billing & Invoices</div>
  <div class="page-subtitle">Manage payments and generate invoices</div>
  <?php if (!empty($success)) alert('success', $success); ?>
  <?php if (!empty($error))   alert('error',   $error);   ?>

  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Total Revenue</div><div class="stat-value">Rs.<?= number_format($total_revenue) ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Pending Amount</div><div class="stat-value text-warning">Rs.<?= number_format($pending_amount) ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Paid Invoices</div><div class="stat-value text-success"><?= $paid_count ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Pending Invoices</div><div class="stat-value text-danger"><?= $pending_count ?></div></div></div>
  </div>

  <div class="section-card">
    <div class="section-header">
      <h5><i class="bi bi-receipt me-2"></i>All Invoices</h5>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="GET">
          <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()" style="width:140px;">
            <option value="">All Status</option>
            <option value="Pending" <?= $filter=='Pending'?'selected':'' ?>>Pending</option>
            <option value="Partial" <?= $filter=='Partial'?'selected':'' ?>>Partial</option>
            <option value="Paid"    <?= $filter=='Paid'   ?'selected':'' ?>>Paid</option>
          </select>
        </form>
        <button class="btn custom-bg text-white btn-sm" data-bs-toggle="modal" data-bs-target="#addInvModal">
          <i class="bi bi-plus-circle me-1"></i>Generate Invoice
        </button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table admin-table mb-0">
        <thead><tr><th>Invoice #</th><th>Booking</th><th>Guest</th><th>Room</th><th>Total</th><th>Paid</th><th>Balance</th><th>Method</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if (mysqli_num_rows($invoices) > 0): while($inv = mysqli_fetch_assoc($invoices)):
            $balance = $inv['total_amount'] - $inv['paid_amount']; ?>
          <tr>
            <td><small class="fw-semibold text-muted"><?= $inv['invoice_number'] ?></small></td>
            <td><small><?= $inv['booking_number'] ?></small></td>
            <td><?= htmlspecialchars($inv['guest_name']) ?></td>
            <td><?= $inv['room_number'] ?></td>
            <td><strong>Rs.<?= number_format($inv['total_amount'],2) ?></strong></td>
            <td class="text-success">Rs.<?= number_format($inv['paid_amount'],2) ?></td>
            <td class="<?= $balance>0?'text-danger':'text-success' ?>">Rs.<?= number_format($balance,2) ?></td>
            <td><span class="badge bg-secondary"><?= $inv['payment_method'] ?></span></td>
            <td><span class="status-badge badge-<?= strtolower($inv['payment_status']) ?>"><?= $inv['payment_status'] ?></span></td>
            <td>
              <button class="btn btn-sm btn-outline-success" onclick="updatePayment(<?= $inv['sr_no'] ?>,<?= $inv['paid_amount'] ?>,'<?= $inv['payment_method'] ?>','<?= $inv['payment_status'] ?>')"><i class="bi bi-cash-coin"></i></button>
              <a href="print_invoice.php?id=<?= $inv['sr_no'] ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-printer"></i></a>
              <a href="?delete=<?= $inv['sr_no'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="10" class="text-center text-muted py-4">No invoices found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ADD INVOICE MODAL -->
<div class="modal fade" id="addInvModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><form method="POST"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Generate Invoice</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="row g-3">
      <div class="col-12">
        <label class="form-label">Select Booking *</label>
        <select name="booking_id" id="booking_sel" class="form-select" onchange="fillAmount()" required>
          <option value="">-- Select Booking --</option>
          <?php while($pb = mysqli_fetch_assoc($pending_bookings)): ?>
          <option value="<?= $pb['sr_no'] ?>" data-amt="<?= $pb['total_amount'] ?>">
            <?= $pb['booking_number'] ?> | <?= htmlspecialchars($pb['name']) ?> | Room <?= $pb['room_number'] ?> | Rs.<?= number_format($pb['total_amount']) ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-4"><label class="form-label">Room Charges *</label><input name="room_charges" id="room_charges_inp" type="number" step="0.01" class="form-control" oninput="calcTotal()" required></div>
      <div class="col-md-4"><label class="form-label">Extra Charges</label><input name="extra_charges" type="number" step="0.01" class="form-control" value="0" oninput="calcTotal()"></div>
      <div class="col-md-4"><label class="form-label">Discount</label><input name="discount" type="number" step="0.01" class="form-control" value="0" oninput="calcTotal()"></div>
      <div class="col-md-4"><label class="form-label">Tax (%)</label><input name="tax_percent" id="tax_inp" type="number" step="0.01" class="form-control" value="12" oninput="calcTotal()"></div>
      <div class="col-md-4"><label class="form-label">Total Amount</label><input type="text" id="total_disp" class="form-control bg-light fw-bold" readonly></div>
      <div class="col-md-4"><label class="form-label">Amount Paid</label><input name="paid_amount" type="number" step="0.01" class="form-control" value="0"></div>
      <div class="col-md-6"><label class="form-label">Payment Method</label>
        <select name="payment_method" class="form-select"><option>Cash</option><option>Card</option><option>UPI</option><option>Bank Transfer</option></select></div>
      <div class="col-md-6"><label class="form-label">Payment Status</label>
        <select name="payment_status" class="form-select"><option>Pending</option><option>Partial</option><option>Paid</option></select></div>
    </div></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" name="add_invoice" class="btn custom-bg text-white">Generate Invoice</button>
    </div>
  </div></form></div>
</div>

<!-- UPDATE PAYMENT MODAL -->
<div class="modal fade" id="updatePayModal" tabindex="-1">
  <div class="modal-dialog"><form method="POST"><input type="hidden" name="inv_id" id="up_id"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Update Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="row g-3">
      <div class="col-12"><label class="form-label">Paid Amount</label><input name="paid_amount" id="up_paid" type="number" step="0.01" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Method</label>
        <select name="payment_method" id="up_method" class="form-select"><option>Cash</option><option>Card</option><option>UPI</option><option>Bank Transfer</option></select></div>
      <div class="col-md-6"><label class="form-label">Status</label>
        <select name="payment_status" id="up_status" class="form-select"><option>Pending</option><option>Partial</option><option>Paid</option></select></div>
    </div></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" name="update_payment" class="btn custom-bg text-white">Update</button>
    </div>
  </div></form></div>
</div>

<?php require('include/scripts.php'); ?>
<script>
function fillAmount(){
  const sel=document.getElementById('booking_sel');
  const amt=sel.options[sel.selectedIndex].dataset.amt||0;
  document.getElementById('room_charges_inp').value=parseFloat(amt).toFixed(2);
  calcTotal();
}
function calcTotal(){
  const rc=parseFloat(document.querySelector('[name=room_charges]').value)||0;
  const ex=parseFloat(document.querySelector('[name=extra_charges]').value)||0;
  const dc=parseFloat(document.querySelector('[name=discount]').value)||0;
  const tx=parseFloat(document.getElementById('tax_inp').value)||0;
  const sub=rc+ex-dc;
  const tot=sub+(sub*tx/100);
  document.getElementById('total_disp').value='Rs.'+tot.toFixed(2);
}
function updatePayment(id,paid,method,status){
  document.getElementById('up_id').value=id;
  document.getElementById('up_paid').value=paid;
  document.getElementById('up_method').value=method;
  document.getElementById('up_status').value=status;
  new bootstrap.Modal(document.getElementById('updatePayModal')).show();
}
</script>
</body>
</html>
