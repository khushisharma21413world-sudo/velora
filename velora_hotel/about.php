<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VELORA HOTEL- ABOUT US</title>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/>
 <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"rel="stylesheet"/>
 <?php require('include/links.php');?>
  
</head>
<body class="bg-light">
     
   <?php require('include/header.php');?>

<div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">ABOUT US</h2>
    <div class="h-line"></div>
    <p class="text-center mt-3">
        We redefine comfort with a touch of Luxury and Elegance and ensure every guest enjoys a memorable and comfortable stay. 
        <br> Our hotel is thoughtfully designed to offer a perfect balance of modern amenities and a warm, inviting atmosphere.
         Whether you are here for relaxation <br> or business, Velora Hotel promises delightful stay where quality meets excellence.
        At Velora, it's not just about rooms—it's about quality, care, and luxury in every detail.
    </p>
   </div>

   <div class="container">
      <div class="row justify-content-between align-items-center">
         <div class="col-lg-6 col-md-5 mb-4 order-2 order-md-1">
             <h3 class="mb-3"> Anthony Edward Stark</h3>
             <p>
                Founded with a vision to deliver exceptional hospitality, Velora Hotel reflects the passion and dedication of its owner.
                 With a focus on quality, comfort, and guest satisfaction, the owner ensures every detail meets the highest standards.
                 Their commitment to excellence creates a welcoming space where every guest feels valued and cared for.
             </p>
         </div>
         <div class="col-lg-5 col-md-5 mb-4 order-1 order-md-2">
             <img src="assets/about/about1.jpg" class="w-100 rounded">
         </div>
      </div>
   </div>

   <div class="container mt-5">
       <div class="row">
        <div class="col-lg-3 col-md-6 mb-4 px-4">
            <div class="d-flex align-items-center slide-box">
                <img src="assets/about/experience.jpg" width="80px" class="me-3">
                <h5 class="m-0">10+ YEARS OF EXPERIENCE</h5>
            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4 px-4">
            <div class="d-flex align-items-center slide-box">
                <img src="assets/about/guests.png" width="80px" class="me-3">
                <h5 class="m-0">200+ GUESTS</h5>
            </div>
       </div>

         <div class="col-lg-3 col-md-6 mb-4 px-4">
            <div class="d-flex align-items-center slide-box">
                <img src="assets/about/feedback.jpg" width="80px" class="me-3">
                <h5 class="m-0">POSITIVE FEEDBACK</h5>
            </div>

        </div>

         <div class="col-lg-3 col-md-6 mb-4 px-4">
            <div class="d-flex align-items-center slide-box">
                <img src="assets/about/services.png" width="80px" class="me-3">
                <h5 class="m-0">24/7 SUPPORT</h5>
            </div>

        </div>
       </div>
   </div>
   
   <h3 class="my-5 fw-bold h-font text-center"> MANAGEMENT TEAM</h3>
    <div class="container px-4">
        <div class="swiper mySwiper">
    <div class="swiper-wrapper mb-5">
      <div class="swiper-slide bg-white text-center overflow-hidden rounded">
        <img src="assets/about/about.jpg" class="w-100 rounded mb-4">
        <h5 class="mt-2">John Doe</h5>
      </div>

      <div class="swiper-slide bg-white text-center overflow-hidden rounded">
        <img src="assets/about/manager1.jpg" class="w-100 rounded mb-4">
        <h5 class="mt-2">Chris Hemsworth</h5>
      </div>

      <div class="swiper-slide bg-white text-center overflow-hidden rounded">
        <img src="assets/about/manager2.jpg" class="w-100 rounded mb-4">
        <h5 class="mt-2">Bruce Banner</h5>
      </div>

      <div class="swiper-slide bg-white text-center overflow-hidden rounded">
        <img src="assets/about/manager3.png" class="w-100 rounded mb-4">
        <h5 class="mt-2">Stephen Strange </h5>
      </div>

      <div class="swiper-slide bg-white text-center overflow-hidden rounded">
        <img src="assets/about/about1.jpg" class="w-100 rounded mb-4">
        <h5 class="mt-2">John Doe</h5>
      </div>

      <div class="swiper-slide bg-white text-center overflow-hidden rounded">
        <img src="assets/about/manager4.jpg" class="w-100 rounded mb-4">
        <h5 class="mt-2">Tom Holland</h5>
      </div>

      <div class="swiper-slide bg-white text-center overflow-hidden rounded">
        <img src="assets/about/manager6.jpg" class="w-100 rounded mb-4">
        <h5 class="mt-2">Tom Hiddleston</h5>
      </div>

    </div>
    <div class="swiper-pagination"></div>
   </div>
 </div>






 
 
    <!---------FOOTER LAST----------------->
 <?php require('include/footer.php');?>
  <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const boxes = document.querySelectorAll('.slide-box');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        boxes.forEach((box, index) => {
          setTimeout(() => {
            box.classList.add('show');
          }, index * 300);
        });
        observer.disconnect(); // ek hi baar chale
      }
    });
  }, {
    threshold: 0.3
  });

  observer.observe(document.querySelector('.container'));
});

  <!-- Initialize Swiper -->
  
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: true, 
      pagination: {
        el: ".swiper-pagination",
        dynamicBullets: true,
      },
        breakpoints: {
            320: {
            slidesPerView: 1,
            },
            640: {
            slidesPerView: 2,
            },
            992: {
            slidesPerView: 3,
            },
        },
    });
  
</script>
</body>
</html>

