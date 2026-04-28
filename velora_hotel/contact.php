<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VELORA HOTEL- CONTACT</title>
 <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/> -->
 <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"rel="stylesheet"/>
 <?php require('include/links.php');?>
</head>
<body class="bg-light">
     
   <?php require('include/header.php');?>


   <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">CONTACT INFORMATION</h2>
    <div class="h-line"></div>
    <p class="text-center mt-3">
       We provide world-class facilities and exceptional services to ensure a comfortable and memorable stay for our guests. <br>Our dedicated team is committed to delivering quality, convenience, and personalized care at every step. <br> We focus on creating a relaxing environment where guests can enjoy modern amenities, warm hospitality, and a seamless experience throughout their stay.
    </p>
   </div>

   <div class="container">
      <div class="row">
         <div class="col-lg-6 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 ">
               <!-- index.php ma map ka link ko copy karna hai ya delhi ka map hai -->
               <iframe class="w-100 rounded mb-4" height="320px" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d219248.55925437977!2d81.48973096370942!3d25.401934885076752!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x398534c9b20bd49f%3A0xa2237856ad4041a!2sPrayagraj%2C%20Uttar%20Pradesh!5e1!3m2!1sen!2sin!4v1776546142990!5m2!1sen!2sin" loading="lazy"></iframe>
               <h5>Address</h5>
               <a href="https://www.google.com/maps/place/Delhi,+India/@28.7040603,77.1024938,11z/data=!3m1!4b1!4m5!3m4!1s0x390cfd5b347eb62d:0x3030e9fbbadf90!8m2!3d28.7040592!4d77.1024902" 
               target="_blank" class="d-inline-block mb-2 text-decoration-none text-dark">
                <i class="bi bi-geo-alt-fill"></i> 123 Velora Hotel, Prayagraj, India
               </a>
             <h5 class="mt-4">Contact Details</h5>
            <a href="tel: +91 1234567890" class="d-inline-block mb-2 text-decoration-none text-dark">
              <i class="bi bi-telephone-fill"></i> +91 1234567890
            </a>
            <br>
            <a href="tel: +91 1234567890" class="d-inline-block mb-2 text-decoration-none text-dark">
              <i class="bi bi-telephone-fill"></i> +91 1234567890
            </a>
            <br>
            <a href="mailto:info@velorahotel.com" class="d-inline-block mb-2 text-decoration-none text-dark">
              <i class="bi bi-envelope-fill"></i> info@velorahotel.com
            </a>
            <br>
            <a href="https://www.velorahotel.com" target="_blank" class="d-inline-block mb-2 text-decoration-none text-dark">
              <i class="bi bi-globe"></i> www.velorahotel.com
            </a>
           </div>
         </div>

         <div class="col-lg-6 col-md-6 mb-4 px-4">
            <div class="bg-white rounded shadow p-4 ">
               
               <!-- <form>
                  <h5>Send a Message</h5>
                  <div class="mt-3">
                    <label class="form-label" style="font-weight: 500;">Name</label>
                    <input type="text" class="form-control shadow-none">
                  </div>
                  <div class="mt-3">
                    <label class="form-label" style="font-weight: 500;">Email</label>
                    <input type="email" class="form-control shadow-none">
                  </div>
                   <div class="mt-3">
                    <label class="form-label" style="font-weight: 500;">phone number</label>
                    <input type="tel" class="form-control shadow-none">
                  </div>

                   <div class="mt-3">
                    <label class="form-label" style="font-weight: 500;">Subject</label>
                    <input type="text" class="form-control shadow-none">
                  </div>

                  <div class="mt-3">
                    <label class="form-label" style="font-weight: 500;">Message</label>
                    <textarea class="form-control shadow-none" rows="3" style="resize:  none"></textarea>
                  </div>
                  <button type="submit" class="btn text-white custom-bg mt-3">Send Message</button>
                </form> -->
                <form id="contact-form">
                   <h5>Send a Message</h5>
                   <div class="mt-3">
                      <label class="form-label fw-500">Name</label>
                      <input type="text" id="name" name="name" class="form-control shadow-none" required>
                   </div>

                    <div class="mt-3">
                       <label class="form-label sfw-500">Email</label>
                       <input type="email" id="email" name="email" class="form-control shadow-none" required>
                    </div>

                     <div class="mt-3">
                       <label class="form-label fw-500">Phone Number</label>
                       <input type="tel" pattern="[0-9]{10}" id="phone" name="phone" class="form-control shadow-none" required>
                     </div>
                    
                     <div class="mt-3">
                       <label class="form-label fw-500">Subject</label>
                       <input type="text" id="subject" name="subject" class="form-control shadow-none">
                     </div>

                     <div class="mt-3">
                       <label class="form-label fw-500">Message</label>
                       <textarea id="message" name="message" class="form-control shadow-none" rows="3" style="resize:none" required></textarea>
                     </div>

                     <button type="submit" class="btn btn-dark mt-3">Send Message</button>
                 </form>
            </div>
         </div>

      </div>
   </div>
 
 
    <!---------FOOTER LAST----------------->
 <?php require('include/footer.php');?>
 <!-- <script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js">
</script> -->
 <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>

<script>
console.log("email.js loaded ✅");

(function () {
    emailjs.init("c62EaII8_GpuGuHxE");
})();

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("contact-form");

    if (!form) {
        console.log("Form NOT found ❌");
        return;
    }

    console.log("Form found ✅");

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        console.log("Submit triggered ✅");

        const templateparams = {
            name: document.getElementById("name").value,
            email: document.getElementById("email").value,
            phone: document.getElementById("phone").value, // added
            subject: document.getElementById("subject").value,
            message: document.getElementById("message").value,
        };

        emailjs.send("service_lv95eup", "template_v3ga9cf", templateparams)
            .then(function (response) {
                alert("Email sent successfully ✅");
                console.log("SUCCESS", response.status, response.text);
                form.reset();
            })
            .catch(function (error) {
                alert("Failed ❌");
                console.error("FAILED...", error);
            });
    });
});
</script>

</body>
</html>

