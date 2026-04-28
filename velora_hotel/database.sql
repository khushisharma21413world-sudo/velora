CREATE DATABASE IF NOT EXISTS vlwebsite;
USE vlwebsite;

CREATE TABLE IF NOT EXISTS admin_cred (
  sr_no INT AUTO_INCREMENT PRIMARY KEY,
  admin_name VARCHAR(50) NOT NULL UNIQUE,
  admin_pass VARCHAR(255) NOT NULL
);
INSERT INTO admin_cred (admin_name, admin_pass) VALUES ('admin','admin123') ON DUPLICATE KEY UPDATE admin_name=admin_name;

CREATE TABLE IF NOT EXISTS setting (
  sr_no INT AUTO_INCREMENT PRIMARY KEY,
  site_title VARCHAR(100) DEFAULT 'Velora Hotel',
  site_about TEXT,
  site_phone VARCHAR(20),
  site_email VARCHAR(100),
  site_address TEXT
);
INSERT INTO setting (sr_no,site_title,site_phone,site_email,site_address) VALUES (1,'Velora Hotel','+91-9876543210','info@velorahotel.com','Prayagraj, UP') ON DUPLICATE KEY UPDATE site_title=site_title;

CREATE TABLE IF NOT EXISTS users (
  sr_no INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone VARCHAR(15),
  password VARCHAR(255) NOT NULL,
  address TEXT,
  dob DATE,
  pincode VARCHAR(10),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
  sr_no INT AUTO_INCREMENT PRIMARY KEY,
  room_name VARCHAR(100) NOT NULL,
  room_type ENUM('Standard','Deluxe','Suite','Presidential') NOT NULL,
  room_number VARCHAR(10) NOT NULL UNIQUE,
  floor_number INT DEFAULT 1,
  price_per_night DECIMAL(10,2) NOT NULL,
  max_adults INT DEFAULT 2,
  max_children INT DEFAULT 1,
  max_capacity INT DEFAULT 3,
  features TEXT COMMENT 'comma separated: 2 Rooms, 1 Bathroom',
  facilities TEXT COMMENT 'comma separated: WiFi, AC, TV',
  description TEXT,
  room_image VARCHAR(255),
  rating INT DEFAULT 4,
  status ENUM('Available','Occupied','Maintenance') DEFAULT 'Available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO rooms (room_name,room_type,room_number,floor_number,price_per_night,max_adults,max_children,max_capacity,features,facilities,description,room_image,rating,status) VALUES
('Standard Comfort','Standard','101',1,2500,2,1,3,'1 Bedroom,1 Bathroom,1 Balcony','WiFi,TV,AC','A comfortable standard room with all basic amenities.','assets/rooms/room.1.jpg',4,'Available'),
('Deluxe Haven','Deluxe','201',2,4500,2,2,4,'2 Rooms,1 Bathroom,1 Balcony,Sofa','WiFi,TV,AC,Room Heater','Spacious deluxe room with premium furnishings.','assets/rooms/room.4.jpg',5,'Available'),
('Suite Royale','Suite','301',3,8000,3,2,5,'3 Rooms,2 Bathrooms,1 Balcony,Jacuzzi','WiFi,TV,AC,Mini Bar,Room Heater','Luxurious suite with panoramic city views.','assets/rooms/room.2.png',5,'Available'),
('Classic Twin','Standard','102',1,2800,2,0,2,'1 Bedroom,1 Bathroom','WiFi,TV,AC','Cosy twin-bed room, perfect for business stays.','assets/rooms/room.3.jpg',4,'Available')
ON DUPLICATE KEY UPDATE room_number=room_number;

CREATE TABLE IF NOT EXISTS bookings (
  sr_no INT AUTO_INCREMENT PRIMARY KEY,
  booking_number VARCHAR(25) NOT NULL UNIQUE,
  user_id INT,
  guest_name VARCHAR(100) NOT NULL,
  guest_phone VARCHAR(15) NOT NULL,
  guest_email VARCHAR(100),
  id_proof_type ENUM('Aadhar','Passport','PAN Card','Driving License') NOT NULL,
  id_proof_number VARCHAR(50) NOT NULL,
  room_id INT NOT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  num_adults INT DEFAULT 1,
  num_children INT DEFAULT 0,
  total_amount DECIMAL(10,2) NOT NULL,
  special_requests TEXT,
  status ENUM('Confirmed','Occupied','Checkout','Cancelled') DEFAULT 'Confirmed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (room_id) REFERENCES rooms(sr_no)
);

CREATE TABLE IF NOT EXISTS invoices (
  sr_no INT AUTO_INCREMENT PRIMARY KEY,
  invoice_number VARCHAR(25) NOT NULL UNIQUE,
  booking_id INT NOT NULL,
  room_charges DECIMAL(10,2) NOT NULL,
  extra_charges DECIMAL(10,2) DEFAULT 0,
  discount DECIMAL(10,2) DEFAULT 0,
  tax_percent DECIMAL(5,2) DEFAULT 12.00,
  total_amount DECIMAL(10,2) NOT NULL,
  paid_amount DECIMAL(10,2) DEFAULT 0,
  payment_method ENUM('Cash','Card','UPI','Bank Transfer') DEFAULT 'Cash',
  payment_status ENUM('Pending','Partial','Paid') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(sr_no)
);

CREATE TABLE IF NOT EXISTS staff (
  sr_no INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(15),
  email VARCHAR(100),
  role VARCHAR(50),
  department ENUM('Front Desk','Housekeeping','Restaurant','Security','Maintenance','Management') DEFAULT 'Front Desk',
  salary DECIMAL(10,2),
  join_date DATE,
  status ENUM('Active','Inactive') DEFAULT 'Active'
);
INSERT INTO staff (name,phone,role,department,salary) VALUES ('Vikram Singh','9811223344','Manager','Front Desk',45000),('Kavita Rao','9922334455','Supervisor','Housekeeping',25000) ON DUPLICATE KEY UPDATE name=name;

CREATE TABLE IF NOT EXISTS guests (
  sr_no INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  phone VARCHAR(15),
  email VARCHAR(100),
  address TEXT,
  id_proof_type ENUM('Aadhar','Passport','PAN Card','Driving License'),
  id_proof_number VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
