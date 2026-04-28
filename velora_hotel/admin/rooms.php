<?php
require('include/essentials.php');
require('include/db_config.php');
adminLogin();

// ADD ROOM with image upload
if(isset($_POST['add_room'])) {
  $d = filteration($_POST);
  $img = '';

  if(!empty($_FILES['room_image']['name'])) {
    $ext     = strtolower(pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if(in_array($ext,$allowed)) {
      $filename = 'room_'.time().'.'.$ext;
      $dest     = '../assets/rooms/uploads/'.$filename;
      if(move_uploaded_file($_FILES['room_image']['tmp_name'], $dest)) {
        $img = 'assets/rooms/uploads/'.$filename;
      }
    } else { $error = "Only JPG, PNG, WEBP allowed."; }
  }

  if(empty($error)) {
    $q = "INSERT INTO rooms (room_name,room_type,room_number,floor_number,price_per_night,max_adults,max_children,max_capacity,features,facilities,description,room_image,rating,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $v = [$d['room_name'],$d['room_type'],$d['room_number'],(int)$d['floor_number'],(float)$d['price_per_night'],(int)$d['max_adults'],(int)$d['max_children'],(int)$d['max_capacity'],$d['features'],$d['facilities'],$d['description'],$img,(int)$d['rating'],$d['status']];
    insert($q,$v,"sssidiiissssis") ? $success="Room added successfully!" : $error="Failed to add room.";
  }
}

// UPDATE ROOM
if(isset($_POST['update_room'])) {
  $d  = filteration($_POST);
  $id = (int)$d['sr_no'];
  $img = mysqli_fetch_assoc(mysqli_query($con,"SELECT room_image FROM rooms WHERE sr_no=$id"))['room_image'];

  if(!empty($_FILES['edit_image']['name'])) {
    $ext     = strtolower(pathinfo($_FILES['edit_image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if(in_array($ext,$allowed)) {
      $filename = 'room_'.time().'.'.$ext;
      $dest     = '../assets/rooms/uploads/'.$filename;
      if(move_uploaded_file($_FILES['edit_image']['tmp_name'], $dest)) {
        if($img && file_exists('../'.$img) && strpos($img,'uploads')!==false) @unlink('../'.$img);
        $img = 'assets/rooms/uploads/'.$filename;
      }
    }
  }

  $q = "UPDATE rooms SET room_name=?,room_type=?,room_number=?,floor_number=?,price_per_night=?,max_adults=?,max_children=?,max_capacity=?,features=?,facilities=?,description=?,room_image=?,rating=?,status=? WHERE sr_no=?";
  $v = [$d['room_name'],$d['room_type'],$d['room_number'],(int)$d['floor_number'],(float)$d['price_per_night'],(int)$d['max_adults'],(int)$d['max_children'],(int)$d['max_capacity'],$d['features'],$d['facilities'],$d['description'],$img,(int)$d['rating'],$d['status'],$id];
  update($q,$v,"sssidiiiisssssi") ? $success="Room updated!" : $error="Update failed.";
}

// DELETE
if(isset($_GET['delete'])) {
  $id  = (int)$_GET['delete'];
  $img = mysqli_fetch_assoc(mysqli_query($con,"SELECT room_image FROM rooms WHERE sr_no=$id"))['room_image'];
  if($img && strpos($img,'uploads')!==false) @unlink('../'.$img);
  mysqli_query($con,"DELETE FROM rooms WHERE sr_no=$id");
  $success = "Room deleted.";
}

// GET for edit AJAX
if(isset($_GET['get_room'])) {
  echo json_encode(mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM rooms WHERE sr_no=".(int)$_GET['get_room'])));
  exit;
}

$search = isset($_GET['s']) ? mysqli_real_escape_string($con,trim($_GET['s'])) : '';
$filter = isset($_GET['f']) ? mysqli_real_escape_string($con,$_GET['f']) : '';
$where  = "WHERE 1";
if($search) $where .= " AND (room_name LIKE '%$search%' OR room_number LIKE '%$search%' OR room_type LIKE '%$search%')";
// if($filter) $where .= " AND status='$filter'";
if($filter) $where .= " AND 
(
  CASE 
    WHEN EXISTS (
      SELECT 1 FROM bookings b 
      WHERE b.room_id = r.sr_no 
      AND b.status IN ('Occupied','Confirmed')
    )
    THEN 'Occupied'
    ELSE r.status
  END
) = '$filter'";
// $rooms = mysqli_query($con,"SELECT * FROM rooms $where ORDER BY FIELD(status,'Available','Reserved','Occupied','Maintenance'), room_number");
$rooms = mysqli_query($con,
"SELECT r.*, 
CASE 
  WHEN EXISTS (
    SELECT 1 FROM bookings b 
    WHERE b.room_id = r.sr_no 
    AND b.status IN ('Occupied','Confirmed')
  )
  THEN 'Occupied'
  ELSE r.status
END AS final_status
FROM rooms r
$where
ORDER BY FIELD(final_status,'Available','Reserved','Occupied','Maintenance'), room_number");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Velora Admin - Rooms</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>

<div id="main-content">
  <div class="page-title h-font">Room Management</div>
  <div class="page-subtitle">Add rooms with images — they appear live on your hotel website</div>

  <?php if(!empty($success)) alert('success',$success); ?>
  <?php if(!empty($error))   alert('error',$error); ?>

  <div class="section-card">
    <div class="section-header">
      <h5><i class="bi bi-door-open me-2"></i>All Rooms</h5>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="GET" class="d-flex gap-2">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input name="s" type="text" class="form-control" placeholder="Search rooms..." value="<?= htmlspecialchars($search) ?>">
          </div>
          <select name="f" class="form-select" style="width:140px;font-size:13px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="Available" <?= $filter=='Available'?'selected':'' ?>>Available</option>
            <option value="Occupied"  <?= $filter=='Occupied' ?'selected':'' ?>>Occupied</option>
            <option value="Maintenance" <?= $filter=='Maintenance'?'selected':'' ?>>Maintenance</option>
          </select>
        </form>
        <button class="btn custom-bg text-white btn-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal">
          <i class="bi bi-plus-circle me-1"></i>Add Room
        </button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table admin-table mb-0">
        <thead>
          <tr><th>Image</th><th>Room Name</th><th>Type</th><th>Room No</th><th>Floor</th><th>Guests</th><th>Price/Night</th><th>Rating</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if(mysqli_num_rows($rooms)>0): while($r=mysqli_fetch_assoc($rooms)): ?>
          <tr>
            <td>
              <?php if($r['room_image']): ?>
              <img src="../<?= htmlspecialchars($r['room_image']) ?>" class="room-thumb">
              <?php else: ?>
              <div class="room-thumb d-flex align-items-center justify-content-center bg-light rounded"><i class="bi bi-image text-muted"></i></div>
              <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($r['room_name']) ?></strong></td>
            <td><?= $r['room_type'] ?></td>
            <td><?= $r['room_number'] ?></td>
            <td>Floor <?= $r['floor_number'] ?></td>
            <td><?= $r['max_adults'] ?>A + <?= $r['max_children'] ?>C</td>
            <td>&#8377;<?= number_format($r['price_per_night']) ?></td>
            <td>
              <?php for($s=1;$s<=5;$s++): ?>
              <i class="bi bi-star<?= $s<=$r['rating']?'-fill':'' ?> text-warning" style="font-size:11px;"></i>
              <?php endfor; ?>
            </td>
               <td>
                  <span class="status-badge badge-<?= strtolower($r['final_status']) ?>">
                   <?= $r['final_status'] ?>
                   </span>
                </td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editRoom(<?= $r['sr_no'] ?>)"><i class="bi bi-pencil"></i></button>
              <a href="?delete=<?= $r['sr_no'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this room? This will also remove it from the website.')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="10" class="text-center text-muted py-4">No rooms found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ADD ROOM MODAL -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <form method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Room</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <!-- Image Upload -->
            <div class="col-md-4">
              <label class="form-label">Room Image *</label>
              <div class="img-preview-box" onclick="document.getElementById('add_img_input').click()">
                <img id="add_img_preview" src="" style="display:none;">
                <div id="add_img_placeholder" class="text-center text-muted">
                  <i class="bi bi-cloud-upload" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                  <span style="font-size:13px;">Click to upload room image</span><br>
                  <small>JPG, PNG, WEBP</small>
                </div>
              </div>
              <input type="file" id="add_img_input" name="room_image" accept="image/*" style="display:none;" onchange="previewImage(this,'add_img_preview','add_img_placeholder')">
            </div>

            <div class="col-md-8">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Room Name *</label>
                  <input name="room_name" type="text" class="form-control" placeholder="e.g. Deluxe Haven" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Room Type *</label>
                  <select name="room_type" class="form-select" required>
                    <option value="">Select</option>
                    <option>Standard</option><option>Deluxe</option><option>Suite</option><option>Presidential</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Room Number *</label>
                  <input name="room_number" type="text" class="form-control" placeholder="101" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Floor</label>
                  <input name="floor_number" type="number" class="form-control" value="1" min="1">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Price / Night (&#8377;) *</label>
                  <input name="price_per_night" type="number" step="0.01" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Max Adults</label>
                  <input name="max_adults" type="number" class="form-control" value="2" min="1">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Max Children</label>
                  <input name="max_children" type="number" class="form-control" value="1" min="0">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Total Capacity</label>
                  <input name="max_capacity" type="number" class="form-control" value="3" min="1">
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Features <small class="text-muted">(comma separated)</small></label>
              <input name="features" type="text" class="form-control" placeholder="2 Rooms, 1 Bathroom, 1 Balcony">
            </div>
            <div class="col-md-6">
              <label class="form-label">Facilities <small class="text-muted">(comma separated)</small></label>
              <input name="facilities" type="text" class="form-control" placeholder="WiFi, AC, TV, Room Heater">
            </div>
            <div class="col-md-8">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="2" placeholder="Brief room description shown on website..."></textarea>
            </div>
            <div class="col-md-2">
              <label class="form-label">Rating</label>
              <select name="rating" class="form-select">
                <option value="5">5 Stars</option><option value="4" selected>4 Stars</option>
                <option value="3">3 Stars</option><option value="2">2 Stars</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option>Available</option><option>Maintenance</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_room" class="btn custom-bg text-white">
            <i class="bi bi-plus-circle me-1"></i>Add Room to Website
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- EDIT ROOM MODAL -->
<div class="modal fade" id="editRoomModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="sr_no" id="er_id">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Edit Room</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Room Image</label>
              <div class="img-preview-box" onclick="document.getElementById('edit_img_input').click()">
                <img id="edit_img_preview" src="" style="width:100%;height:100%;object-fit:cover;">
              </div>
              <input type="file" id="edit_img_input" name="edit_image" accept="image/*" style="display:none;" onchange="previewEditImage(this)">
              <small class="text-muted">Click image to change</small>
            </div>
            <div class="col-md-8">
              <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Room Name</label><input name="room_name" id="er_name" type="text" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Room Type</label>
                  <select name="room_type" id="er_type" class="form-select">
                    <option>Standard</option><option>Deluxe</option><option>Suite</option><option>Presidential</option>
                  </select></div>
                <div class="col-md-4"><label class="form-label">Room Number</label><input name="room_number" id="er_num" type="text" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Floor</label><input name="floor_number" id="er_floor" type="number" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Price/Night</label><input name="price_per_night" id="er_price" type="number" step="0.01" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Max Adults</label><input name="max_adults" id="er_adults" type="number" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Max Children</label><input name="max_children" id="er_children" type="number" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Capacity</label><input name="max_capacity" id="er_cap" type="number" class="form-control"></div>
              </div>
            </div>
            <div class="col-md-6"><label class="form-label">Features</label><input name="features" id="er_features" type="text" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Facilities</label><input name="facilities" id="er_facilities" type="text" class="form-control"></div>
            <div class="col-md-8"><label class="form-label">Description</label><textarea name="description" id="er_desc" class="form-control" rows="2"></textarea></div>
            <div class="col-md-2"><label class="form-label">Rating</label>
              <select name="rating" id="er_rating" class="form-select">
                <option value="5">5 Stars</option><option value="4">4 Stars</option><option value="3">3 Stars</option>
              </select></div>
            <div class="col-md-2"><label class="form-label">Status</label>
              <select name="status" id="er_status" class="form-select">
                <option>Available</option><option>Occupied</option><option>Maintenance</option><option>Reserved</option>
              </select></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="update_room" class="btn custom-bg text-white">Update Room</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php require('include/scripts.php'); ?>
<script>
function previewImage(input,previewId,placeholderId){
  if(input.files&&input.files[0]){
    const r=new FileReader();
    r.onload=e=>{
      const img=document.getElementById(previewId);
      img.src=e.target.result; img.style.display='block';
      document.getElementById(placeholderId).style.display='none';
    };
    r.readAsDataURL(input.files[0]);
  }
}
function previewEditImage(input){
  if(input.files&&input.files[0]){
    const r=new FileReader();
    r.onload=e=>{ document.getElementById('edit_img_preview').src=e.target.result; };
    r.readAsDataURL(input.files[0]);
  }
}
function editRoom(id){
  fetch('rooms.php?get_room='+id).then(r=>r.json()).then(d=>{
    document.getElementById('er_id').value=d.sr_no;
    document.getElementById('er_name').value=d.room_name;
    document.getElementById('er_type').value=d.room_type;
    document.getElementById('er_num').value=d.room_number;
    document.getElementById('er_floor').value=d.floor_number;
    document.getElementById('er_price').value=d.price_per_night;
    document.getElementById('er_adults').value=d.max_adults;
    document.getElementById('er_children').value=d.max_children;
    document.getElementById('er_cap').value=d.max_capacity;
    document.getElementById('er_features').value=d.features||'';
    document.getElementById('er_facilities').value=d.facilities||'';
    document.getElementById('er_desc').value=d.description||'';
    document.getElementById('er_rating').value=d.rating;
    document.getElementById('er_status').value=d.status;
    const img=document.getElementById('edit_img_preview');
    img.src=d.room_image?'../'+d.room_image:'';
    new bootstrap.Modal(document.getElementById('editRoomModal')).show();
  });
}
</script>
</body>
</html>
