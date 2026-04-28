<?php
require('include/essentials.php');
require('include/db_config.php');
adminLogin();

if (isset($_POST['add_guest'])) {
  $d = filteration($_POST);
  $q = "INSERT INTO guests (name, phone, email, address, id_proof_type, id_proof_number) VALUES (?,?,?,?,?,?)";
  $v = [$d['name'], $d['phone'], $d['email'], $d['address'], $d['id_proof_type'], $d['id_proof_number']];
  insert($q, $v, "ssssss") ? $success = "Guest added!" : $error = "Failed.";
}

if (isset($_POST['update_guest'])) {
  $d = filteration($_POST);
  $q = "UPDATE guests SET name=?, phone=?, email=?, address=?, id_proof_type=?, id_proof_number=? WHERE sr_no=?";
  $v = [$d['name'], $d['phone'], $d['email'], $d['address'], $d['id_proof_type'], $d['id_proof_number'], $d['sr_no']];
  update($q, $v, "ssssssi") ? $success = "Guest updated!" : $error = "Failed.";
}

if (isset($_GET['delete'])) {
  mysqli_query($con, "DELETE FROM guests WHERE sr_no=".(int)$_GET['delete']);
  $success = "Guest deleted.";
}

if (isset($_GET['get_guest'])) {
  echo json_encode(mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM guests WHERE sr_no=".(int)$_GET['get_guest'])));
  exit;
}

$search = isset($_GET['s']) ? filteration(['q'=>$_GET['s']])['q'] : '';
$where  = $search ? "WHERE name LIKE '%$search%' OR phone LIKE '%$search%'" : "";
$guests = mysqli_query($con, "SELECT g.*, (SELECT COUNT(*) FROM bookings WHERE user_id=g.sr_no) as visits FROM guests g $where ORDER BY g.name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Velora Admin - Guests</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>

<div id="main-content">
  <div class="page-title h-font">Guest Records</div>
  <div class="page-subtitle">Manage all registered guests</div>

  <?php if (!empty($success)) alert('success', $success); ?>
  <?php if (!empty($error))   alert('error', $error); ?>

  <div class="section-card">
    <div class="section-header">
      <h5><i class="bi bi-people me-2"></i>All Guests</h5>
      <div class="d-flex gap-2">
        <form method="GET">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input name="s" type="text" class="form-control" placeholder="Search by name/phone..." value="<?= htmlspecialchars($search) ?>">
          </div>
        </form>
        <button class="btn custom-bg text-white btn-sm" data-bs-toggle="modal" data-bs-target="#addGuestModal">
          <i class="bi bi-person-plus me-1"></i>Add Guest
        </button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table admin-table mb-0">
        <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>ID Proof</th><th>Total Visits</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if (mysqli_num_rows($guests) > 0): ?>
            <?php while($g = mysqli_fetch_assoc($guests)): ?>
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div style="width:34px;height:34px;border-radius:50%;background:#0f1a2c;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;">
                    <?= strtoupper(substr($g['name'],0,1)) ?>
                  </div>
                  <div>
                    <div><?= htmlspecialchars($g['name']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($g['address'] ?? '') ?></small>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($g['phone']) ?></td>
              <td><?= htmlspecialchars($g['email'] ?? '-') ?></td>
              <td>
                <span class="status-badge badge-confirmed"><?= $g['id_proof_type'] ?></span><br>
                <small class="text-muted"><?= htmlspecialchars($g['id_proof_number']) ?></small>
              </td>
              <td><strong><?= $g['visits'] ?></strong> booking(s)</td>
              <td>
                <button class="btn btn-sm btn-outline-primary" onclick="editGuest(<?= $g['sr_no'] ?>)"><i class="bi bi-pencil"></i></button>
                <a href="?delete=<?= $g['sr_no'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete guest?')"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted py-4">No guests found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ADD GUEST MODAL -->
<div class="modal fade" id="addGuestModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add New Guest</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12"><label class="form-label">Full Name *</label><input name="name" type="text" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Phone *</label><input name="phone" type="text" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control"></div>
            <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
            <div class="col-md-6">
              <label class="form-label">ID Proof Type *</label>
              <select name="id_proof_type" class="form-select" required>
                <option>Aadhar</option><option>Passport</option><option>PAN Card</option><option>Driving License</option>
              </select>
            </div>
            <div class="col-md-6"><label class="form-label">ID Proof Number *</label><input name="id_proof_number" type="text" class="form-control" required></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_guest" class="btn custom-bg text-white">Add Guest</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- EDIT GUEST MODAL -->
<div class="modal fade" id="editGuestModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST">
      <input type="hidden" name="sr_no" id="eg_id">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Edit Guest</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12"><label class="form-label">Full Name</label><input name="name" id="eg_name" type="text" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" id="eg_phone" type="text" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input name="email" id="eg_email" type="email" class="form-control"></div>
            <div class="col-12"><label class="form-label">Address</label><textarea name="address" id="eg_address" class="form-control" rows="2"></textarea></div>
            <div class="col-md-6">
              <label class="form-label">ID Proof Type</label>
              <select name="id_proof_type" id="eg_idtype" class="form-select">
                <option>Aadhar</option><option>Passport</option><option>PAN Card</option><option>Driving License</option>
              </select>
            </div>
            <div class="col-md-6"><label class="form-label">ID Proof Number</label><input name="id_proof_number" id="eg_idnum" type="text" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="update_guest" class="btn custom-bg text-white">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php require('include/scripts.php'); ?>
<script>
function editGuest(id){
  fetch('guests.php?get_guest='+id).then(r=>r.json()).then(d=>{
    document.getElementById('eg_id').value=d.sr_no;
    document.getElementById('eg_name').value=d.name;
    document.getElementById('eg_phone').value=d.phone;
    document.getElementById('eg_email').value=d.email||'';
    document.getElementById('eg_address').value=d.address||'';
    document.getElementById('eg_idtype').value=d.id_proof_type;
    document.getElementById('eg_idnum').value=d.id_proof_number;
    new bootstrap.Modal(document.getElementById('editGuestModal')).show();
  });
}
</script>
</body>
</html>
