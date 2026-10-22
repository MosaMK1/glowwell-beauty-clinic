-- =====================================================================
-- GlowWell Beauty Clinic - MySQL database schema + sample data
-- Import with:  mysql -u root -p < glowwell.sql
-- =====================================================================
DROP DATABASE IF EXISTS glowwell;
CREATE DATABASE glowwell CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE glowwell;

-- Login accounts (admins and customers). Passwords are stored hashed.
CREATE TABLE users (
  user_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(100) NOT NULL,
  email         VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Customer records (may exist without a login, e.g. walk-in clients added by admin)
CREATE TABLE customers (
  customer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NULL UNIQUE,
  full_name   VARCHAR(100) NOT NULL,
  email       VARCHAR(120) NOT NULL UNIQUE,
  phone       VARCHAR(20)  NOT NULL,
  notes       VARCHAR(255) NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cust_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE service_categories (
  category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE services (
  service_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id  INT UNSIGNED NOT NULL,
  name         VARCHAR(100) NOT NULL UNIQUE,
  description  VARCHAR(255) NULL,
  duration_min SMALLINT UNSIGNED NOT NULL,
  price        DECIMAL(8,2) NOT NULL,
  is_active    TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_serv_cat FOREIGN KEY (category_id) REFERENCES service_categories(category_id)
) ENGINE=InnoDB;

CREATE TABLE staff (
  staff_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name  VARCHAR(100) NOT NULL,
  email      VARCHAR(120) NOT NULL UNIQUE,
  phone      VARCHAR(20)  NOT NULL,
  job_title  VARCHAR(80)  NOT NULL,
  is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- Many-to-many: which staff member can perform which service
CREATE TABLE staff_services (
  staff_id   INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (staff_id, service_id),
  CONSTRAINT fk_ss_staff FOREIGN KEY (staff_id)   REFERENCES staff(staff_id)      ON DELETE CASCADE,
  CONSTRAINT fk_ss_serv  FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Weekly working hours (day_of_week: 1 = Monday ... 7 = Sunday)
CREATE TABLE staff_schedules (
  schedule_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  staff_id    INT UNSIGNED NOT NULL,
  day_of_week TINYINT UNSIGNED NOT NULL,
  start_time  TIME NOT NULL,
  end_time    TIME NOT NULL,
  UNIQUE KEY uq_staff_day (staff_id, day_of_week),
  CONSTRAINT fk_sch_staff FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE appointments (
  appointment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id    INT UNSIGNED NOT NULL,
  staff_id       INT UNSIGNED NOT NULL,
  service_id     INT UNSIGNED NOT NULL,
  appt_date      DATE NOT NULL,
  start_time     TIME NOT NULL,
  end_time       TIME NOT NULL,
  status         ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  price_charged  DECIMAL(8,2) NOT NULL,
  notes          VARCHAR(255) NULL,
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_no_double_book (staff_id, appt_date, start_time),
  CONSTRAINT fk_ap_cust  FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
  CONSTRAINT fk_ap_staff FOREIGN KEY (staff_id)    REFERENCES staff(staff_id),
  CONSTRAINT fk_ap_serv  FOREIGN KEY (service_id)  REFERENCES services(service_id)
) ENGINE=InnoDB;

-- Log of customer email notifications (innovation feature)
CREATE TABLE notifications (
  notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id     INT UNSIGNED NOT NULL,
  appointment_id  INT UNSIGNED NULL,
  subject         VARCHAR(150) NOT NULL,
  message         TEXT NOT NULL,
  sent_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_nt_cust FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------ SAMPLE DATA ---------------------------
-- Admin login:    admin@glowwell.co.za / Admin@123
-- Customer login: thandi@example.com (or priya@, sipho@, emma@) / Customer@123
INSERT INTO users (full_name,email,password_hash,role) VALUES
('Clinic Administrator','admin@glowwell.co.za','$2y$10$oTFbEqzFK3el5NeoOGkeae7hliGBu5Ck.jLuavpf18kTES4QwLBxG','admin'),
('Thandi Nkosi','thandi@example.com','$2y$10$0Atp8aor39dNpkONRXsN2.TMQSVpij0XB5TzuLUS8cJDOwwn38qfu','customer'),
('Priya Naidoo','priya@example.com','$2y$10$0Atp8aor39dNpkONRXsN2.TMQSVpij0XB5TzuLUS8cJDOwwn38qfu','customer'),
('Sipho Dlamini','sipho@example.com','$2y$10$0Atp8aor39dNpkONRXsN2.TMQSVpij0XB5TzuLUS8cJDOwwn38qfu','customer'),
('Emma van der Merwe','emma@example.com','$2y$10$0Atp8aor39dNpkONRXsN2.TMQSVpij0XB5TzuLUS8cJDOwwn38qfu','customer');

INSERT INTO customers (user_id,full_name,email,phone,notes) VALUES
(2,'Thandi Nkosi','thandi@example.com','0821234567','Prefers morning appointments'),
(3,'Priya Naidoo','priya@example.com','0832345678',NULL),
(4,'Sipho Dlamini','sipho@example.com','0843456789','Sensitive skin'),
(5,'Emma van der Merwe','emma@example.com','0714567890',NULL),
(NULL,'Lerato Mokoena','lerato@example.com','0765678901','Walk-in client'),
(NULL,'James Pillay','james@example.com','0796789012',NULL);

INSERT INTO service_categories (name) VALUES ('Facials'),('Massages'),('Hair Treatments'),('Wellness'),('Speciality');

INSERT INTO services (category_id,name,description,duration_min,price) VALUES
(1,'Classic Facial','Deep cleanse, exfoliation and hydrating mask',60,450.00),
(1,'Anti-Ageing Facial','Collagen-boosting facial with serum infusion',90,780.00),
(2,'Swedish Massage','Relaxing full-body massage',60,550.00),
(2,'Deep Tissue Massage','Targeted pressure for muscle tension',60,620.00),
(2,'Hot Stone Massage','Heated basalt stones for deep relaxation',90,850.00),
(3,'Keratin Treatment','Smoothing and frizz-control treatment',120,1200.00),
(3,'Scalp Therapy','Nourishing scalp massage and treatment',45,350.00),
(4,'Aromatherapy Session','Essential-oil wellness session',60,480.00),
(4,'Detox Body Wrap','Full-body detoxifying wrap',75,690.00),
(5,'Bridal Makeup Package','Trial plus wedding-day makeup',120,1800.00),
(5,'Microneedling','Skin-rejuvenation specialist treatment',60,950.00),
(5,'Laser Hair Removal (Small Area)','Laser session for small areas',30,500.00);

INSERT INTO staff (full_name,email,phone,job_title) VALUES
('Naledi Khumalo','naledi@glowwell.co.za','0601110001','Senior Facialist'),
('Ruan Botha','ruan@glowwell.co.za','0601110002','Massage Therapist'),
('Aisha Patel','aisha@glowwell.co.za','0601110003','Hair Specialist'),
('Zanele Mthembu','zanele@glowwell.co.za','0601110004','Wellness Therapist'),
('Chloe Jacobs','chloe@glowwell.co.za','0601110005','Aesthetic Specialist');

INSERT INTO staff_services (staff_id,service_id) VALUES
(1,1),(1,2),(1,11),(2,3),(2,4),(2,5),(3,6),(3,7),(4,8),(4,9),(4,3),(5,10),(5,11),(5,12),(5,1);

-- Mon-Fri 09:00-17:00 for all staff; Saturdays 09:00-13:00 for selected staff
INSERT INTO staff_schedules (staff_id,day_of_week,start_time,end_time)
SELECT s.staff_id, d.d, '09:00:00', '17:00:00' FROM staff s
JOIN (SELECT 1 d UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) d;
INSERT INTO staff_schedules (staff_id,day_of_week,start_time,end_time) VALUES
(1,6,'09:00:00','13:00:00'),(2,6,'09:00:00','13:00:00'),(5,6,'09:00:00','13:00:00');

INSERT INTO appointments (customer_id,staff_id,service_id,appt_date,start_time,end_time,status,price_charged,notes) VALUES
(1,1,1,'2026-09-28','09:00','10:00','completed',450.00,NULL),
(2,2,3,'2026-09-29','10:00','11:00','completed',550.00,NULL),
(3,3,7,'2026-09-30','11:00','11:45','completed',350.00,NULL),
(4,4,8,'2026-10-01','09:30','10:30','completed',480.00,NULL),
(1,5,11,'2026-10-02','14:00','15:00','completed',950.00,NULL),
(5,2,4,'2026-10-03','09:00','10:00','completed',620.00,NULL),
(6,1,2,'2026-10-05','13:00','14:30','completed',780.00,NULL),
(2,3,6,'2026-10-05','09:00','11:00','cancelled',1200.00,'Customer cancelled'),
(3,2,5,'2026-10-06','10:00','11:30','confirmed',850.00,NULL),
(4,1,1,'2026-10-06','14:00','15:00','confirmed',450.00,NULL),
(1,4,9,'2026-10-07','09:00','10:15','confirmed',690.00,NULL),
(2,5,12,'2026-10-07','11:00','11:30','pending',500.00,NULL),
(5,3,7,'2026-10-08','13:00','13:45','pending',350.00,NULL),
(6,2,3,'2026-10-08','15:00','16:00','confirmed',550.00,NULL),
(3,1,2,'2026-10-09','10:00','11:30','pending',780.00,'First anti-ageing session'),
(1,5,10,'2026-10-12','09:00','11:00','confirmed',1800.00,'Bridal trial'),
(4,2,4,'2026-10-13','11:00','12:00','pending',620.00,NULL),
(2,4,8,'2026-10-14','14:00','15:00','confirmed',480.00,NULL),
(5,1,1,'2026-10-15','09:00','10:00','pending',450.00,NULL),
(6,3,6,'2026-10-16','10:00','12:00','confirmed',1200.00,NULL);

INSERT INTO notifications (customer_id,appointment_id,subject,message) VALUES
(3,9,'Booking confirmed','Hi Sipho, your Hot Stone Massage on 2026-10-06 at 10:00 is confirmed.'),
(4,10,'Booking confirmed','Hi Emma, your Classic Facial on 2026-10-06 at 14:00 is confirmed.');
