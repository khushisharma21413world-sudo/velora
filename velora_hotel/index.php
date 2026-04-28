<?php require('include/db.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VELORA HOTEL - HOME</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet"/>
  <?php require('include/links.php'); ?>
 <style>
    .team {
    /* background: #eae2d6; */
    padding: 60px 20px;
    /* padding: 40px 20px; */
   }

   /* Slider style (scrollable) */
   .team-container {
    display: flex;
    overflow-x: auto;
    gap: 70px;
    scroll-snap-type: x mandatory;
   }

   /* Hide scrollbar (optional) */
   .team-container::-webkit-scrollbar {
    display: none;
    }

   .member {
    min-width: 250px;
    flex: 0 0 auto;
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    background: transparent;
    scroll-snap-align: center;
    transition: 0.3s;
    }

   /* Image */
   .member img {
     width: 200px;
     height: 200px;
     border-radius: 50%;
     object-fit: cover;
     transition: 0.3s;
    }

   /* Text */
   .member h2 {
     margin-top: 15px;
     font-size: 20px;
   }
   
   .role {
     color: gray;
     font-size: 14px;
   }
   
   .desc {
     font-size: 14px;
     color: #444;
   }
   
   /* 🔥 Hover Effect */
   .member:hover {
     background: #fff;
     transform: translateY(-8px);
     box-shadow: 0 10px 20px rgba(0,0,0,0.1);
   }

   .member:hover img {
    transform: scale(1.08);
   }
 </style>
</head>
<body class="bg-light">

<?php require('include/header.php'); ?>

<!-- HERO CAROUSEL -->
<div class="container-fluid px-lg-4 mt-4">
  <div class="swiper swiper-container">
    <div class="swiper-wrapper">
      <?php for($i=1;$i<=6;$i++): ?>
      <div class="swiper-slide">
        <img src="assets/frontimage/assets.<?= $i ?>.png" class="w-100 d-block" style="border-radius:12px;max-height:520px;object-fit:cover;">
      </div>
      <?php endfor; ?>
    </div>
  </div>
</div>

<!-- AVAILABILITY FORM -->
<div class="container availability-form">
  <div class="row">
    <div class="col-lg-12 bg-white shadow p-4 rounded">
      <h5 class="mb-4">Check Booking Availability</h5>
      <form action="rooms.php" method="GET">
        <div class="row align-items-end">
          <div class="col-lg-3 mb-3">
            <label class="form-label fw-500">Check-in</label>
            <input type="date" name="check_in" class="form-control shadow-none" min="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-lg-3 mb-3">
            <label class="form-label fw-500">Check-out</label>
            <input type="date" name="check_out" class="form-control shadow-none">
          </div>
          <div class="col-lg-2 mb-3">
            <label class="form-label fw-500">Adults</label>
            <select name="adults" class="form-select shadow-none">
              <option value="1">1 Adult</option>
              <option value="2">2 Adults</option>
              <option value="3">3 Adults</option>
            </select>
          </div>
          <div class="col-lg-2 mb-3">
            <label class="form-label fw-500">Children</label>
            <select name="children" class="form-select shadow-none">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
            </select>
          </div>
          <div class="col-lg-2 mb-3">
            <button type="submit" class="btn text-white custom-bg w-100 shadow-none">
              <i class="bi bi-search me-1"></i>Search
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- OUR ROOMS (Dynamic from DB) -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR ROOMS</h2>
<div class="container">
  <div class="row">
    <?php
    $rooms = mysqli_query($con, "SELECT * FROM rooms WHERE status='Available' ORDER BY sr_no LIMIT 3");
    while($room = mysqli_fetch_assoc($rooms)):
      $features   = explode(',', $room['features'] ?? '');
      $facilities = explode(',', $room['facilities'] ?? '');
    ?>
    <div class="col-lg-4 col-md-6 mb-4">
      <div class="card border-0 shadow" style="max-width:350px;margin:auto;">
        <div style="overflow:hidden;height:200px;">
          <img src="<?= htmlspecialchars($room['room_image']) ?>" class="card-img-top w-100 h-100" style="object-fit:cover;">
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-1">
            <h5 class="mb-0"><?= htmlspecialchars($room['room_name']) ?></h5>
            <span class="badge bg-light text-dark"><?= $room['room_type'] ?></span>
          </div>
          <h6 class="text-success mb-3">₹<?= number_format($room['price_per_night']) ?> / night</h6>

          <div class="features mb-3">
            <h6 class="mb-1" style="font-size:13px;">Features</h6>
            <?php foreach($features as $f): if(trim($f)): ?>
            <span class="badge rounded-pill bg-light text-dark"><?= trim(htmlspecialchars($f)) ?></span>
            <?php endif; endforeach; ?>
          </div>

          <div class="facilities mb-3">
            <h6 class="mb-1" style="font-size:13px;">Facilities</h6>
            <?php foreach($facilities as $f): if(trim($f)): ?>
            <span class="badge rounded-pill bg-light text-dark"><?= trim(htmlspecialchars($f)) ?></span>
            <?php endif; endforeach; ?>
          </div>

          <div class="guest mb-3">
            <h6 class="mb-1" style="font-size:13px;">Guests</h6>
            <span class="badge rounded-pill bg-light text-dark"><?= $room['max_adults'] ?> Adults</span>
            <span class="badge rounded-pill bg-light text-dark"><?= $room['max_children'] ?> Children</span>
          </div>

          <div class="mb-3">
            <?php for($s=1;$s<=5;$s++): ?>
            <i class="bi bi-star<?= $s<=$room['rating']?'-fill':'' ?> text-warning" style="font-size:13px;"></i>
            <?php endfor; ?>
          </div>

          <div class="d-flex justify-content-evenly mb-2">
            <a href="booking.php?room_id=<?= $room['sr_no'] ?>" class="btn btn-sm text-white custom-bg shadow-none">Book Now</a>
            <a href="rooms.php?room_id=<?= $room['sr_no'] ?>" class="btn btn-sm btn-outline-dark shadow-none">More Details</a>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>

    <div class="col-lg-12 text-center mt-3 mb-5">
      <a href="rooms.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Rooms</a>
    </div>
  </div>
</div>

<!-- FACILITIES -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR FACILITIES</h2>
<div class="container">
  <div class="row justify-content-evenly px-lg-0 px-5">
    <?php
    $facs = [
      ['icon'=>'bi-wifi','name'=>'Free WiFi'],
      ['icon'=>'bi-water','name'=>'Swimming Pool'],
      ['icon'=>'bi-cup-hot','name'=>'Restaurant'],
      ['icon'=>'bi-car-front','name'=>'Free Parking'],
      ['icon'=>'bi-flower1','name'=>'Spa & Wellness'],
    ];
    foreach($facs as $fac): ?>
    <div class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
      <i class="bi <?= $fac['icon'] ?>" style="font-size:50px;color:#2ec1ac;"></i>
      <h5 class="mt-3"><?= $fac['name'] ?></h5>
    </div>
    <?php endforeach; ?>
    <div class="col-lg-12 text-center mt-4">
      <a href="facilities.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Facilities</a>
    </div>
  </div>
</div>

<!-- TESTIMONIALS -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">TESTIMONIALS</h2>
<div class="container mt-3">
  <div class="swiper swiper-testimonials">
    <div class="swiper-wrapper mb-5">
      <?php
      $tests = [
        ['name'=>'Rahul Sharma','review'=>'Amazing experience! The rooms were luxurious and the staff was very helpful. Will definitely come back again.','stars'=>5],
        ['name'=>'Priya Mehta','review'=>'Beautiful hotel with great amenities. The food at the restaurant was excellent. Highly recommended!','stars'=>5],
        ['name'=>'Amit Kumar','review'=>'Perfect location and very comfortable rooms. The check-in process was smooth and fast.','stars'=>4],
        ['name'=>'Sneha Patel','review'=>'Wonderful stay with family. Kids enjoyed the pool and the staff made us feel at home.','stars'=>5],
      ];
      foreach($tests as $t): ?>
      <div class="swiper-slide bg-white p-4 rounded shadow">
        <div class="profile d-flex align-items-center mb-3">
          <div style="width:40px;height:40px;border-radius:50%;background:#0f1a2c;color:#f6ac0f;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:16px;flex-shrink:0;">
            <?= strtoupper(substr($t['name'],0,1)) ?>
          </div>
          <h6 class="mb-0 ms-2"><?= $t['name'] ?></h6>
        </div>
        <p style="font-size:14px;color:#555;"><?= $t['review'] ?></p>
        <div>
          <?php for($s=1;$s<=5;$s++): ?>
          <i class="bi bi-star<?= $s<=$t['stars']?'-fill':'' ?> text-warning"></i>
          <?php endfor; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="swiper-pagination"></div>
  </div>
</div>

<!-- REACH US -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">REACH US</h2>
<div class="container mb-5">
  <div class="row">
    <div class="col-lg-8 col-md-8 mb-3 bg-white rounded shadow p-3">
      <iframe class="w-100 rounded" height="320px" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d219248.55925437977!2d81.48973096370942!3d25.401934885076752!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x398534c9b20bd49f%3A0xa2237856ad4041a!2sPrayagraj%2C%20Uttar%20Pradesh!5e1!3m2!1sen!2sin!4v1776546142990!5m2!1sen!2sin" loading="lazy"></iframe>
    </div>
    <div class="col-lg-4 col-md-4">
      <div class="bg-white p-4 rounded shadow mb-3">
        <h5>Contact Details</h5>
        <a href="tel:+911234567890" class="d-block mb-2 text-decoration-none text-dark"><i class="bi bi-telephone-fill me-2 text-success"></i>+91 1234567890</a>
        <a href="mailto:info@velorahotel.com" class="d-block mb-2 text-decoration-none text-dark"><i class="bi bi-envelope-fill me-2 text-primary"></i>info@velorahotel.com</a>
        <span class="d-block text-dark"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>123, Velora Hotel, Prayagraj</span>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h5>Follow Us</h5>
        <a href="#" class="d-block mb-2 text-decoration-none text-dark"><i class="bi bi-instagram me-2" style="color:#e1306c"></i>@velora_hotel</a>
        <a href="#" class="d-block mb-2 text-decoration-none text-dark"><i class="bi bi-facebook me-2 text-primary"></i>Velora Hotel</a>
        <a href="#" class="d-block text-decoration-none text-dark"><i class="bi bi-twitter me-2 text-info"></i>@velorahotel</a>
      </div>
    </div>
  </div>
</div>
          
               <!-- ------------------------------------------------ -->
             <section class="team">

  <div class="team-container">

    <div class="member">
       <img src="assets/simage/montil.png" alt="image">

      <!-- <img src="a/your.jpg" alt=""> -->
      <h2>Montil Chadhury </h2>
      <p class="role">Backend Developer</p>
      <p class="desc">He is the backbone of our system, 
        handling <br>backend development with precision and <br>efficiency to ensure smooth performance.
      With <br> strong technical expertise, 
      powers the backend <br> to deliver a seamless and flawless digital experience.</p>
    </div>

    <div class="member">
       <img src="assets/simage/khushi.jpg" alt="image">
      <h2>Khushi Sharma </h2>
      <p class="role">Frontend Developer</p>
      <p class="desc">she is the Frontend Developer for Velora Hotel,<br> creating elegant and user-friendly web experiences.
      <br>She focuses on design, responsiveness, and smooth <br> interaction to enhance every visitor's journey.<br>
      user-friendly experience for every visitor.</p>
    </div>

    <div class="member">
      <img src="assets/simage/aman.jpg" alt="">
      <h2>Aman Sharma </h2>
      <p class="role">Designer & Video editor</p>
      <p class="desc">He is the Designer and Video Editor, bringing <br>creativity and visual storytelling to life.
        He crafts <br>engaging designs and high-quality visuals that elevate <br>the brand's overall experience.
      brings imagination to life <br>through thoughtful design and dynamic video content.</p>
    </div>


  </div>

</section>

<?php require('include/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
<script>
new Swiper(".swiper-container",{effect:"fade",speed:1200,autoplay:{delay:3500,disableOnInteraction:false},loop:true});
new Swiper(".swiper-testimonials",{grabCursor:true,centeredSlides:true,loop:true,autoplay:{delay:2500},speed:1000,coverflowEffect:{rotate:50,stretch:0,depth:100,modifier:1,slideShadows:true},effect:"coverflow",pagination:{el:".swiper-pagination",clickable:true},breakpoints:{320:{slidesPerView:1},768:{slidesPerView:2},1024:{slidesPerView:2}}});
</script>
</body>
</html>
