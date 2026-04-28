=========================================
  VELORA HOTEL - COMPLETE SETUP GUIDE
=========================================

STEP 1 - IMPORT DATABASE
--------------------------
1. Open phpMyAdmin
2. Import: velora_hotel/database.sql
3. Default admin login: admin / admin123

STEP 2 - DATABASE CONFIG
--------------------------
Open: velora_hotel/admin/include/db_config.php
      velora_hotel/include/db.php
Update: $uname (MySQL username) and $pass (password)

STEP 3 - UPLOAD FOLDER PERMISSIONS
-------------------------------------
Make writable: velora_hotel/assets/rooms/uploads/
  chmod 777 velora_hotel/assets/rooms/uploads/

STEP 4 - RUN PROJECT
----------------------
Place velora_hotel/ folder in:
  XAMPP: C:/xampp/htdocs/
  WAMP:  C:/wamp64/www/

Frontend: http://localhost/velora_hotel/
Admin:    http://localhost/velora_hotel/admin/
Login:    admin / admin123

=========================================
  HOW FRONTEND + ADMIN ARE CONNECTED
=========================================

1. ADMIN adds a room (with image, features,
   price, adults, children) -> instantly
   shows on frontend website

2. CUSTOMER books a room from frontend ->
   instantly appears in Admin > Bookings

3. ADMIN can update booking status
   (Confirmed -> Occupied -> Checkout)

4. When checkout/cancelled -> room
   automatically becomes Available again

=========================================
  FILE STRUCTURE
=========================================
velora_hotel/
  index.php          <- Homepage (dynamic)
  rooms.php          <- Rooms with filters
  booking.php        <- Booking form
  about.php
  contact.php
  facilities.php
  database.sql       <- Import this first!
  include/
    db.php           <- Frontend DB connection
    header.php
    footer.php
    links.php
  css/style.css
  assets/rooms/uploads/  <- Room images saved here
  admin/
    index.php        <- Admin login
    dashboard.php    <- Live booking dashboard
    rooms.php        <- Add/edit rooms + images
    bookings.php     <- Customer bookings
    guests.php
    billing.php
    staff.php
    settings.php
    print_invoice.php
    logout.php
    include/
    css/
=========================================
