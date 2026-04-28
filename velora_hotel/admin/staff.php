<?php
require('include/essentials.php');
require('include/db_config.php');
adminLogin();

if (isset($_POST['add_staff'])) {
  $d = filteration($_POST);
  $q = "INSERT INTO staff (name, phone, email, role, department, salary, join_date, status) VALUES (?,?,?,?,?,?,?,?)";
  $v = [$d['name'], $d['phone'], $d['email'], $d['role'], $d['department'], (float)$d['salary'], $d['join_date'], $d['status']];
  insert($q, $v, "sssssdss") ? $success = "Staff member added!" : $error = "Failed.";
}

if (isset($_POST['update_staff'])) {
  $d = filteration($_POST);
  $q = "UPDATE staff SET name=?, phone=?, email=?, role=?, department=?, salary=?, join_date=?, status=? WHERE sr_no=?";
  $v = [$d['name'], $d['phone'], $d['email'], $d['role'], $d['department'], (float)$d['salary'], $d['join_date'], $d['status'], (int)$d['sr_no']];
  update($q, $v, "sssssdssl") ? $success = "Staff updated!" : $error = "Failed.";
}

if (isset($_GET['delete'])) {
  mysqli_query($con, "DELETE FROM staff WHERE sr_no=".(int)$_GET['delete']);
  $success = "Staff member removed.";
}

if (isset($_GET['get_staff'])) {
  echo json_encode(mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM staff WHERE sr_no=".(int)$_GET['get_staff'])));
  exit;
}

$dept   = isset($_GET['dept']) ? mysqli_real_escape_string($con,$_GET['dept']) : '';
$where  = $dept ? "WHERE department='$dept'" : "";
$staff  = mysqli_query($con, "SELECT * FROM staff $where ORDER BY name");
$total  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM staff"))['c'];
$active = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM staff WHERE status='Active'"))['c'];
$total_salary = mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(salary),0) as s FROM staff WHERE status='Active'"))['s'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Velora Admin - Staff</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>
<div id="main-content">
  <div class="page-title h-font">Staff Management</div>
  <div class="page-subtitle">Manage hotel staff and departments</div>
  <?php if (!empty($success)) alert('success', $success); ?>
  <?php if (!empty($error))   alert('error',   $error);   ?>

  <div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="stat-label">Total Staff</div><div class="stat-value"><?= $total ?></div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-label">Active Staff</div><div class="stat-value text-success"><?= $active ?></div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-label">Monthly Payroll</div><div class="stat-value">Rs.<?= number_format($total_salary) ?></div></div></div>
  </div>

  <div class="section-card">
    <div class="section-header">
      <h5><i class="bi bi-person-badge me-2"></i>All Staff</h5>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="GET">
          <select name="dept" class="form-select form-select-sm" onchange="this.form.submit()" style="width:170px;">
            <option value="">All Departments</option>
            <option value="Front Desk"    <?= $dept=='Front Desk'   ?'selected':'' ?>>Front Desk</option>
            <option value="Housekeeping"  <?= $dept=='Housekeeping' ?'selected':'' ?>>Housekeeping</option>
            <option value="Restaurant"    <?= $dept=='Restaurant'   ?'selected':'' ?>>Restaurant</option>
            <option value="Security"      <?= $dept=='Security'     ?'selected':'' ?>>Security</option>
            <option value="Maintenance"   <?= $dept=='Maintenance'  ?'selected':'' ?>>Maintenance</option>
            <option value="Management"    <?= $dept=='Management'   ?'selected':'' ?>>Management</option>
          </select>
        </form>
        <button class="btn custom-bg text-white btn-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
          <i class="bi bi-person-plus me-1"></i>Add Staff
        </button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table admin-table mb-0">
        <thead><tr><th>Name</th><th>Role</th><th>Department</th><th>Phone</th><th>Salary</th><th>Join Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if (mysqli_num_rows($staff) > 0): while($s = mysqli_fetch_assoc($staff)): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div style="width:34px;height:34px;border-radius:50%;background:#2ec1ac;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;">
                  <?= strtoupper(substr($s['name'],0,1)) ?>
                </div>
                <div>
                  <div><?= htmlspecialchars($s['name']) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($s['email']??'') ?></small>
                </div>
              </div>
            </td>
            <td><?= htmlspecialchars($s['role']) ?></td>
            <td><span class="badge bg-secondary"><?= $s['department'] ?></span></td>
            <td><?= htmlspecialchars($s['phone']) ?></td>
            <td>Rs.<?= number_format($s['salary']) ?></td>
            <td><?= $s['join_date'] ? date('d M Y',strtotime($s['join_date'])) : '-' ?></td>
            <td><span class="status-badge badge-<?= strtolower($s['status']) ?>"><?= $s['status'] ?></span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editStaff(<?= $s['sr_no'] ?>)"><i class="bi bi-pencil"></i></button>
              <a href="?delete=<?= $s['sr_no'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove staff member?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="8" class="text-center text-muted py-4">No staff records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ADD STAFF MODAL -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><form method="POST"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Add Staff Member</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="row g-3">
      <div class="col-md-6"><label class="form-label">Full Name *</label><input name="name" type="text" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Phone *</label><input name="phone" type="text" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Role *</label><input name="role" type="text" class="form-control" placeholder="e.g. Receptionist" required></div>
      <div class="col-md-6"><label class="form-label">Department *</label>
        <select name="department" class="form-select" required>
          <option>Front Desk</option><option>Housekeeping</option><option>Restaurant</option>
          <option>Security</option><option>Maintenance</option><option>Management</option>
        </select></div>
      <div class="col-md-6"><label class="form-label">Salary (Rs.)</label><input name="salary" type="number" step="0.01" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Join Date</label><input name="join_date" type="date" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Status</label>
        <select name="status" class="form-select"><option>Active</option><option>Inactive</option></select></div>
    </div></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" name="add_staff" class="btn custom-bg text-white">Add Staff</button>
    </div>
  </div></form></div>
</div>

<!-- EDIT STAFF MODAL -->
<div class="modal fade" id="editStaffModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><form method="POST"><input type="hidden" name="sr_no" id="es_id"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Staff</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="row g-3">
      <div class="col-md-6"><label class="form-label">Full Name</label><input name="name" id="es_name" type="text" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" id="es_phone" type="text" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Email</label><input name="email" id="es_email" type="email" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Role</label><input name="role" id="es_role" type="text" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Department</label>
        <select name="department" id="es_dept" class="form-select">
          <option>Front Desk</option><option>Housekeeping</option><option>Restaurant</option>
          <option>Security</option><option>Maintenance</option><option>Management</option>
        </select></div>
      <div class="col-md-6"><label class="form-label">Salary</label><input name="salary" id="es_salary" type="number" step="0.01" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Join Date</label><input name="join_date" id="es_join" type="date" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Status</label>
        <select name="status" id="es_status" class="form-select"><option>Active</option><option>Inactive</option></select></div>
    </div></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" name="update_staff" class="btn custom-bg text-white">Update</button>
    </div>
  </div></form></div>
</div>

<?php require('include/scripts.php'); ?>
<script>
function editStaff(id){
  fetch('staff.php?get_staff='+id).then(r=>r.json()).then(d=>{
    document.getElementById('es_id').value=d.sr_no;
    document.getElementById('es_name').value=d.name;
    document.getElementById('es_phone').value=d.phone;
    document.getElementById('es_email').value=d.email||'';
    document.getElementById('es_role').value=d.role;
    document.getElementById('es_dept').value=d.department;
    document.getElementById('es_salary').value=d.salary;
    document.getElementById('es_join').value=d.join_date||'';
    document.getElementById('es_status').value=d.status;
    new bootstrap.Modal(document.getElementById('editStaffModal')).show();
  });
}
</script>
</body>
</html>
