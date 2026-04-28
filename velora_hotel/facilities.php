<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VELORA HOTEL- FACILITIES</title>
 <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/> -->
 <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"rel="stylesheet"/>
 <?php require('include/links.php');?>
 <style>
   .pop{
    transition: all 0.4s ease;
    border-top: 4px solid transparent;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.pop:hover{
    transform: translateY(-12px) scale(1.02);
    border-top-color: #c59d5f; /* gold luxury color */
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

/* subtle shine effect */
.pop::before{
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(120deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: 0.5s;
}

.pop:hover::before{
    left: 100%;
}

/* icon styling */
.pop img{
    transition: 0.4s;
}

.pop:hover img{
    transform: scale(1.2) rotate(5deg);
}

/* heading */
.pop h5{
    font-weight: 600;
    margin-top: 15px;
}

/* paragraph */
.pop p{
    font-size: 14px;
    color: #666;
}

/* optional: icon circle background */
.pop img{
    background: #f8f8f8;
    padding: 10px;
    border-radius: 50%;
}
 </style>
</head>
<body class="bg-light">
     
   <?php require('include/header.php');?>


   <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">OUR FACILITIES</h2>
    <div class="h-line"></div>
    <p class="text-center mt-3">
        We provide world class facilities to our customers Lorem ipsum dolor sit amet consectetur adipisicing elit. <br>Nihil tempore itaque autem id officiis aut iusto quod. 
        Odit eveniet nemo doloribus velit, id quas maxime dicta dolorem, ex animi dignissimos.
    </p>
   </div>

   <div class="container">
      <div class="row">
         <div class="col-lg-4 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop ">
             <div class="d-flex align-items-center mb-3">
                 
                <img src="assets/facilities/wifi.svg" width="50px">
                <h5 class="m-0 ms-3"> free Wi-Fi</h5>
             </div>
             <p>Enjoy high-speed WiFi access throughout the hotel for both work and entertainment. 
              Enjoy high-speed WiFi throughout the hotel. 
               Perfect for business as well as leisure guests.</p>
            </div>
         </div>

         <div class="col-lg-4 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop ">
             <div class="d-flex align-items-center mb-3">
                 
                <img src="assets/facilities/swimming.webp" width="50px">
                <h5 class="m-0 ms-3"> Swimming Pool</h5>
             </div>
             <p>Take a refreshing dip in our clean and well maintained swimming pool.
               A perfect place to relax and unwind during your stay. 
               Designed to provide comfort and a peaceful environment.</p>
            </div>
         </div>

         <div class="col-lg-4 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop ">
             <div class="d-flex align-items-center mb-3">
                 
                <img src="assets/facilities/restaurant.jpg" width="50px">
                <h5 class="m-0 ms-3"> Restaurant</h5>
             </div>
             <p>Enjoy a delightful dining experience with quality food and great ambiance.
               Quality food served in a pleasant ambiance.
               Perfect for both casual and fine dining.</p>
            </div>
         </div>

         <div class="col-lg-4 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop ">
             <div class="d-flex align-items-center mb-3">
                 
                <img src="assets/facilities/car.png" width="50px">
                <h5 class="m-0 ms-3"> Free Parking</h5>
             </div>
             <p>We provide secure and spacious parking facilities for all our guests. 
               Enjoy the convenience of hassle-free parking at no extra cost. 
               Your vehicle's safety is our top priority.</p>
            </div>
         </div>

         <div class="col-lg-4 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop ">
             <div class="d-flex align-items-center mb-3">
                 
                <img src="assets/facilities/spa.svg" width="50px">
                <h5 class="m-0 ms-3"> Spa & Wellness</h5>
             </div>
             <p>Refresh your body and mind with our Premium relaxing spa services.
                Experience true comfort and peace that refresh your body and mind.
                A perfect escape for stress relief and relaxation.</p>
            </div>
         </div>

         <div class="col-lg-4 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop ">
             <div class="d-flex align-items-center mb-3">
                 
                <img src="assets/facilities/room-service.svg" width="50px">
                <h5 class="m-0 ms-3"> 24/7 Room Service</h5>
             </div>
             <p>Enjoy round-the-clock room service for your convenience. 
               Whether a late-night meal or early morning request, our staff is always ready.
               Experience quick and reliable service anytime.</p>
            </div>
         </div>

      </div>
   </div>
 
 
    <!---------FOOTER LAST----------------->
 <?php require('include/footer.php');?>

</body>
</html>

