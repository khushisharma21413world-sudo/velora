<?php require('include/db.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VELORA HOTEL - ROOMS</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet"/>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">
<?php require('include/header.php'); ?>

<div class="my-5 px-4">
  <h2 class="fw-bold h-font text-center">OUR ROOMS</h2>
  <div class="h-line"></div>
</div>

<div class="container">
  <div class="row">

    <!-- FILTER SIDEBAR -->
    <div class="col-lg-3 col-md-12 mb-4">
      <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow mb-3">
        <div class="container-fluid flex-lg-column align-items-stretch">
          <h4 class="mt-2">FILTERS</h4>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#filtersdropdown">
            <span class="navbar-toggler-icon"></span>
          </button>
          <form method="GET" id="filterForm">
            <div class="collapse navbar-collapse flex-column mt-2 align-items-stretch show" id="filtersdropdown">

              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size:16px;">CHECK AVAILABILITY</h5>
                <label class="form-label" style="font-size:13px;">Check-in</label>
                <input type="date" name="check_in" class="form-control shadow-none mb-2" value="<?= htmlspecialchars($_GET['check_in']??'') ?>" min="<?= date('Y-m-d') ?>">
                <label class="form-label" style="font-size:13px;">Check-out</label>
                <input type="date" name="check_out" class="form-control shadow-none" value="<?= htmlspecialchars($_GET['check_out']??'') ?>">
              </div>

              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size:16px;">ROOM TYPE</h5>
                <?php foreach(['Standard','Deluxe','Suite','Presidential'] as $type): ?>
                <div class="mb-2">
                  <input type="checkbox" name="type[]" value="<?= $type ?>" id="t<?= $type ?>" class="form-check-input shadow-none"
                    <?= (isset($_GET['type']) && in_array($type,$_GET['type'])) ? 'checked' : '' ?>>
                  <label class="form-label mb-0 ms-1" for="t<?= $type ?>"><?= $type ?></label>
                </div>
                <?php endforeach; ?>
              </div>

              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size:16px;">GUESTS</h5>
                <div class="d-flex gap-2">
                  <div class="flex-fill">
                    <label class="form-label" style="font-size:13px;">Adults</label>
                    <input type="number" name="adults" class="form-control shadow-none" min="1" value="<?= htmlspecialchars($_GET['adults']??1) ?>">
                  </div>
                  <div class="flex-fill">
                    <label class="form-label" style="font-size:13px;">Children</label>
                    <input type="number" name="children" class="form-control shadow-none" min="0" value="<?= htmlspecialchars($_GET['children']??0) ?>">
                  </div>
                </div>
              </div>

              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-2" style="font-size:16px;">MAX PRICE / NIGHT</h5>
                <input type="range" name="max_price" class="form-range" min="1000" max="20000" step="500"
                  value="<?= $_GET['max_price']??20000 ?>" oninput="document.getElementById('price_show').textContent='₹'+this.value">
                <small id="price_show">₹<?= number_format($_GET['max_price']??20000) ?></small>
              </div>

              <button type="submit" class="btn custom-bg text-white w-100 shadow-none mb-2">Apply Filters</button>
              <a href="rooms.php" class="btn btn-outline-secondary w-100 shadow-none">Clear</a>
            </div>
          </form>
        </div>
      </nav>
    </div>

    <!-- ROOMS LIST -->
    <div class="col-lg-9">
      <?php
      $where = "WHERE status='Available'";
      if(!empty($_GET['type'])) {
        $types = array_map(fn($t)=>"'".mysqli_real_escape_string($con,$t)."'", $_GET['type']);
        $where .= " AND room_type IN(".implode(',',$types).")";
      }
      if(!empty($_GET['adults'])) $where .= " AND max_adults >= ".(int)$_GET['adults'];
      if(!empty($_GET['children'])) $where .= " AND max_children >= ".(int)$_GET['children'];
      if(!empty($_GET['max_price'])) $where .= " AND price_per_night <= ".(float)$_GET['max_price'];

      $rooms = mysqli_query($con,"SELECT * FROM rooms $where ORDER BY price_per_night");
      $count = mysqli_num_rows($rooms);
      ?>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0"><?= $count ?> room(s) found</p>
      </div>

      <?php if($count == 0): ?>
      <div class="alert alert-info">No rooms available matching your filters. <a href="rooms.php">Clear filters</a></div>
      <?php endif; ?>

      <?php while($room = mysqli_fetch_assoc($rooms)):
        $features   = explode(',', $room['features']??'');
        $facilities = explode(',', $room['facilities']??'');
        $check_in  = $_GET['check_in']??'';
        $check_out = $_GET['check_out']??'';
        $days = ($check_in && $check_out) ? max(1, (strtotime($check_out)-strtotime($check_in))/86400) : 0;
      ?>
      <div class="card mb-4 border-0 shadow">
        <div class="row g-0 p-3 align-items-center">
          <div class="col-md-4 px-2">
            <div style="overflow:hidden;border-radius:10px;height:180px;">
              <img src="<?= htmlspecialchars($room['room_image']) ?>" class="img-fluid w-100 h-100" style="object-fit:cover;">
            </div>
          </div>
          <div class="col-md-5 px-3 py-2">
            <div class="d-flex align-items-center gap-2 mb-1">
              <h5 class="mb-0"><?= htmlspecialchars($room['room_name']) ?></h5>
              <span class="badge" style="background:#e8f5e9;color:#1b5e20;font-size:11px;"><?= $room['room_type'] ?></span>
            </div>
            <p class="text-muted mb-2" style="font-size:13px;">Room <?= $room['room_number'] ?> &bull; Floor <?= $room['floor_number'] ?></p>

            <div class="mb-2">
              <span class="fw-semibold" style="font-size:13px;">Features: </span>
              <?php foreach($features as $f): if(trim($f)): ?>
              <span class="badge rounded-pill bg-light text-dark"><?= trim(htmlspecialchars($f)) ?></span>
              <?php endif; endforeach; ?>
            </div>
            <div class="mb-2">
              <span class="fw-semibold" style="font-size:13px;">Facilities: </span>
              <?php foreach($facilities as $f): if(trim($f)): ?>
              <span class="badge rounded-pill bg-light text-dark"><?= trim(htmlspecialchars($f)) ?></span>
              <?php endif; endforeach; ?>
            </div>
            <div class="mb-2">
              <i class="bi bi-people me-1 text-muted"></i>
              <small class="text-muted"><?= $room['max_adults'] ?> Adults, <?= $room['max_children'] ?> Children</small>
            </div>
            <div>
              <?php for($s=1;$s<=5;$s++): ?>
              <i class="bi bi-star<?= $s<=$room['rating']?'-fill':'' ?> text-warning" style="font-size:13px;"></i>
              <?php endfor; ?>
            </div>
          </div>
          <div class="col-md-3 text-center border-start py-3">
            <div class="mb-1 text-success fw-bold" style="font-size:20px;">₹<?= number_format($room['price_per_night']) ?></div>
            <div class="text-muted mb-2" style="font-size:12px;">per night</div>
            <?php if($days > 0): ?>
            <div class="badge bg-light text-dark mb-3"><?= $days ?> nights = ₹<?= number_format($room['price_per_night']*$days) ?></div><br>
            <?php endif; ?>
            <a href="booking.php?room_id=<?= $room['sr_no'] ?><?= $check_in?'&check_in='.$check_in:'' ?><?= $check_out?'&check_out='.$check_out:'' ?>" class="btn btn-sm text-white custom-bg w-75 shadow-none mb-2">Book Now</a><br>
            <span class="badge <?= $room['status']=='Available'?'bg-success':'bg-danger' ?>"><?= $room['status'] ?></span>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<?php require('include/footer.php'); ?>
</body>
</html>
