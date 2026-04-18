-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 09:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dental_clinic`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_dentist` (IN `p_first_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_specialization` VARCHAR(150), IN `p_email` VARCHAR(150), IN `p_phone` VARCHAR(20))   BEGIN
    INSERT INTO dentists (first_name, last_name, specialization, email, phone)
    VALUES (p_first_name, p_last_name, p_specialization, p_email, p_phone);
    SELECT LAST_INSERT_ID() AS new_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_book_appointment` (IN `p_patient_id` INT, IN `p_dentist_id` INT, IN `p_service_id` INT, IN `p_date` DATE, IN `p_time` TIME, IN `p_notes` TEXT)   BEGIN
    DECLARE conflict INT DEFAULT 0;
    SELECT COUNT(*) INTO conflict FROM appointments
    WHERE dentist_id = p_dentist_id
      AND appointment_date = p_date
      AND appointment_time = p_time
      AND status NOT IN ('cancelled');
    IF conflict > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Time slot already booked';
    ELSE
        INSERT INTO appointments (patient_id, dentist_id, service_id, appointment_date, appointment_time, notes)
        VALUES (p_patient_id, p_dentist_id, p_service_id, p_date, p_time, p_notes);
        SELECT LAST_INSERT_ID() AS appointment_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cancel_appointment` (IN `p_id` INT, IN `p_patient_id` INT)   BEGIN
    UPDATE appointments SET status = 'cancelled'
    WHERE id = p_id AND patient_id = p_patient_id AND status = 'pending';
    SELECT ROW_COUNT() AS affected;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_change_password` (IN `p_user_id` INT, IN `p_password` VARCHAR(255))   BEGIN
    UPDATE users SET password = p_password WHERE id = p_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_payment` (IN `p_appointment_id` INT, IN `p_patient_id` INT, IN `p_amount` DECIMAL(10,2), IN `p_method` VARCHAR(20), IN `p_reference_no` VARCHAR(100), IN `p_notes` TEXT)   BEGIN
    DECLARE exists_count INT DEFAULT 0;
    SELECT COUNT(*) INTO exists_count FROM payments
    WHERE appointment_id = p_appointment_id AND status != 'refunded';
    IF exists_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Payment already exists for this appointment';
    ELSE
        INSERT INTO payments (appointment_id, patient_id, amount, method, reference_no, notes, status)
        VALUES (p_appointment_id, p_patient_id, p_amount, p_method, p_reference_no, p_notes, 'pending');
        SELECT LAST_INSERT_ID() AS payment_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_dentist` (IN `p_id` INT)   BEGIN
    UPDATE dentists SET is_active = 0 WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_patient` (IN `p_id` INT)   BEGIN
    DELETE FROM users WHERE id = p_id AND role = 'patient';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_find_user_by_email` (IN `p_email` VARCHAR(150))   BEGIN
    SELECT id, first_name, last_name, email, password, phone, role, is_verified, status
    FROM users
    WHERE LOWER(TRIM(email)) = LOWER(TRIM(p_email))
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_all_appointments` ()   BEGIN
    SELECT 
        a.id, a.appointment_date, a.appointment_time, a.status, a.notes,
        CONCAT(u.first_name, ' ', u.last_name) AS patient_name,
        u.email AS patient_email, u.phone AS patient_phone,
        CONCAT(d.first_name, ' ', d.last_name) AS dentist_name,
        s.name AS service_name, s.price
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    JOIN dentists d ON a.dentist_id = d.id
    JOIN services s ON a.service_id = s.id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_all_dentists` ()   BEGIN
    SELECT id, first_name, last_name, specialization, email, phone, is_active
    FROM dentists ORDER BY first_name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_all_patients` ()   BEGIN
    SELECT id, first_name, last_name, email, phone, created_at
    FROM users
    WHERE role = 'patient'
    ORDER BY created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_all_payments` ()   BEGIN
    SELECT p.id, p.amount, p.method, p.status, p.reference_no, p.paid_at, p.created_at,
           CONCAT(u.first_name,' ',u.last_name) AS patient_name,
           u.email AS patient_email,
           a.appointment_date, a.appointment_time,
           s.name AS service_name,
           CONCAT(d.first_name,' ',d.last_name) AS dentist_name,
           p.appointment_id
    FROM payments p
    JOIN users u ON p.patient_id = u.id
    JOIN appointments a ON p.appointment_id = a.id
    JOIN services s ON a.service_id = s.id
    JOIN dentists d ON a.dentist_id = d.id
    ORDER BY p.created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_dashboard_stats` ()   BEGIN
    SELECT
        (SELECT COUNT(*) FROM appointments WHERE status='pending') AS pending_count,
        (SELECT COUNT(*) FROM appointments WHERE status='confirmed') AS confirmed_count,
        (SELECT COUNT(*) FROM appointments WHERE status='completed') AS completed_count,
        (SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date)=CURDATE()) AS today_count,
        (SELECT COUNT(*) FROM users WHERE role='patient') AS total_patients,
        (SELECT COUNT(*) FROM appointments) AS total_appointments,
        (SELECT COUNT(*) FROM payments WHERE status='pending') AS pending_payments,
        (SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid') AS total_revenue;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_dentists` ()   BEGIN
    SELECT id, first_name, last_name, specialization, email, phone
    FROM dentists WHERE is_active = 1
    ORDER BY first_name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_dentist_by_id` (IN `p_id` INT)   BEGIN
    SELECT id, first_name, last_name, specialization, email, phone, is_active
    FROM dentists WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_patient_appointments` (IN `p_patient_id` INT)   BEGIN
    SELECT 
        a.id, a.appointment_date, a.appointment_time, a.status, a.notes,
        CONCAT(d.first_name, ' ', d.last_name) AS dentist_name,
        d.specialization,
        s.name AS service_name, s.price, s.duration_minutes
    FROM appointments a
    JOIN dentists d ON a.dentist_id = d.id
    JOIN services s ON a.service_id = s.id
    WHERE a.patient_id = p_patient_id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_patient_by_id` (IN `p_id` INT)   BEGIN
    SELECT id, first_name, last_name, email, phone, created_at
    FROM users WHERE id = p_id AND role = 'patient';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_patient_payments` (IN `p_patient_id` INT)   BEGIN
    SELECT p.id, p.amount, p.method, p.status, p.reference_no, p.notes, p.paid_at, p.created_at,
           a.appointment_date, a.appointment_time, a.status AS appt_status,
           s.name AS service_name,
           CONCAT(d.first_name,' ',d.last_name) AS dentist_name
    FROM payments p
    JOIN appointments a ON p.appointment_id = a.id
    JOIN services s ON a.service_id = s.id
    JOIN dentists d ON a.dentist_id = d.id
    WHERE p.patient_id = p_patient_id
    ORDER BY p.created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_payment_by_appointment` (IN `p_appointment_id` INT)   BEGIN
    SELECT * FROM payments WHERE appointment_id = p_appointment_id LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_pending_patients` ()   BEGIN
    SELECT id, first_name, last_name, email, phone, created_at
    FROM users
    WHERE role = 'patient' AND status = 'pending'
    ORDER BY created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_services` ()   BEGIN
    SELECT id, name, description, duration_minutes, price
    FROM services WHERE is_active = 1
    ORDER BY name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_user_by_id` (IN `p_user_id` INT)   BEGIN
    SELECT id, first_name, last_name, email, phone, role, avatar
    FROM users WHERE id = p_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_register_patient` (IN `p_first_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_email` VARCHAR(150), IN `p_password` VARCHAR(255), IN `p_phone` VARCHAR(20))   BEGIN
    DECLARE email_exists INT DEFAULT 0;
    SELECT COUNT(*) INTO email_exists FROM users WHERE email = p_email;
    IF email_exists > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
    ELSE
        INSERT INTO users (first_name, last_name, email, password, phone, role, status)
        VALUES (p_first_name, p_last_name, p_email, p_password, p_phone, 'patient', 'pending');
        SELECT LAST_INSERT_ID() AS new_user_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_appointment_status` (IN `p_id` INT, IN `p_status` VARCHAR(20))   BEGIN
    UPDATE appointments SET status = p_status WHERE id = p_id;
    SELECT ROW_COUNT() AS affected;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_avatar` (IN `p_user_id` INT, IN `p_avatar` VARCHAR(255))   BEGIN
    UPDATE users SET avatar = p_avatar WHERE id = p_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_dentist` (IN `p_id` INT, IN `p_first_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_specialization` VARCHAR(150), IN `p_email` VARCHAR(150), IN `p_phone` VARCHAR(20), IN `p_is_active` TINYINT(1))   BEGIN
    UPDATE dentists
    SET first_name=p_first_name, last_name=p_last_name,
        specialization=p_specialization, email=p_email,
        phone=p_phone, is_active=p_is_active
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_patient_status` (IN `p_user_id` INT, IN `p_status` VARCHAR(20))   BEGIN
    UPDATE users SET status = p_status WHERE id = p_user_id;
    SELECT ROW_COUNT() AS affected;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_payment_status` (IN `p_id` INT, IN `p_status` VARCHAR(20), IN `p_notes` TEXT)   BEGIN
    UPDATE payments
    SET status = p_status,
        paid_at = IF(p_status = 'paid', NOW(), paid_at),
        notes = IF(p_notes != '', p_notes, notes)
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_profile` (IN `p_user_id` INT, IN `p_first_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_phone` VARCHAR(20))   BEGIN
    UPDATE users SET 
        first_name = p_first_name,
        last_name  = p_last_name,
        phone      = p_phone
    WHERE id = p_user_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `dentist_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `dentist_id`, `service_id`, `appointment_date`, `appointment_time`, `status`, `notes`, `created_at`) VALUES
(2, 2, 2, 7, '2026-04-20', '13:00:00', 'completed', 'gg', '2026-04-17 07:03:47'),
(3, 2, 1, 3, '2026-04-18', '11:00:00', 'completed', '', '2026-04-17 10:37:33'),
(5, 2, 5, 3, '2026-04-19', '14:00:00', 'completed', 'adasda', '2026-04-18 06:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `dentists`
--

CREATE TABLE `dentists` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `specialization` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dentists`
--

INSERT INTO `dentists` (`id`, `first_name`, `last_name`, `specialization`, `email`, `phone`, `is_active`, `created_at`) VALUES
(1, 'Marie Edsyl', 'Hormachuelos', 'General Dentistry', 'marieedsyla@dentalclinic.com', '09171234567', 1, '2026-04-16 18:37:01'),
(2, 'Julius', 'Credo', 'Orthodontics', 'julius@dentalclinic.com', '09181234567', 0, '2026-04-16 18:37:01'),
(4, 'Noby Mae', 'Halina', 'Pediatric Dentistry', 'nobymae@gmaiul.com', '09456923956', 1, '2026-04-17 10:45:15'),
(5, 'Melvy', 'Jongco', 'Endodontics', 'jongco@gmail.com', '09456975934', 1, '2026-04-17 10:45:56');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','gcash','maya','card') DEFAULT 'cash',
  `status` enum('pending','paid','refunded') DEFAULT 'pending',
  `reference_no` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `appointment_id`, `patient_id`, `amount`, `method`, `status`, `reference_no`, `notes`, `paid_at`, `created_at`) VALUES
(1, 2, 2, 6000.00, 'gcash', 'paid', '09123985964', 'Thank you', '2026-04-17 07:06:25', '2026-04-17 07:05:49'),
(2, 3, 2, 2000.00, 'card', 'paid', 'hatdog', 'asdas', '2026-04-17 10:38:57', '2026-04-17 10:38:36');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 30,
  `price` decimal(10,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `duration_minutes`, `price`, `is_active`) VALUES
(1, 'General Checkup', 'Routine dental examination and cleaning', 30, 500.00, 1),
(2, 'Tooth Extraction', 'Simple or surgical tooth removal', 45, 1500.00, 1),
(3, 'Dental Filling', 'Composite or amalgam filling', 60, 2000.00, 1),
(4, 'Teeth Whitening', 'Professional teeth whitening treatment', 90, 5000.00, 1),
(5, 'Braces Consultation', 'Initial consultation for orthodontic treatment', 45, 800.00, 1),
(6, 'Root Canal', 'Endodontic root canal treatment', 90, 8000.00, 1),
(7, 'Dental Crown', 'Porcelain or metal crown placement', 60, 6000.00, 1),
(8, 'Oral Prophylaxis', 'Professional teeth cleaning and polishing', 45, 1200.00, 1),
(9, 'General Checkup', 'Routine dental examination and cleaning', 30, 500.00, 1),
(10, 'Tooth Extraction', 'Simple or surgical tooth removal', 45, 1500.00, 1),
(11, 'Dental Filling', 'Composite or amalgam filling', 60, 2000.00, 1),
(12, 'Teeth Whitening', 'Professional teeth whitening treatment', 90, 5000.00, 1),
(13, 'Braces Consultation', 'Initial consultation for orthodontic treatment', 45, 800.00, 1),
(14, 'Root Canal', 'Endodontic root canal treatment', 90, 8000.00, 1),
(15, 'Dental Crown', 'Porcelain or metal crown placement', 60, 6000.00, 1),
(16, 'Oral Prophylaxis', 'Professional teeth cleaning and polishing', 45, 1200.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','patient') DEFAULT 'patient',
  `is_verified` tinyint(1) DEFAULT 1,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `role`, `is_verified`, `status`, `created_at`, `avatar`) VALUES
(1, 'Admin', 'User', 'admin@auzadentalclinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 'admin', 1, 'approved', '2026-04-16 18:37:01', 'avatar_1_1776498414.png'),
(2, 'Angel', 'Frederick', 'angelox@gmail.com', '$2y$10$UWthv4/kekF9PT8cf7ate.hAkLNhVifOITJKWPSRHzN8MncZbMSxO', '09123985964', 'patient', 1, 'approved', '2026-04-16 18:43:42', 'avatar_2_1776498385.jpg'),
(7, 'Luke Kirby', 'Jao', 'lukekirby@gmail.com', '$2y$10$ocwNKvAZt9pC039r.YwA7eeg77HMRtdRcOb9TT/RpQ8xAfZr7EZ/2', '09456923956', 'patient', 1, 'approved', '2026-04-18 15:39:07', NULL),
(8, 'Jeeruz Marl', 'Tabar', 'jeeruzmarl@gmail.com', '$2y$10$yEH7Ix7BOO61X1Zi7gRFY.cHtJqdwJf.7oryJCMY/gRbKCbeB.WAW', '09123456856', 'patient', 1, 'approved', '2026-04-18 15:48:00', 'avatar_8_1776498513.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `dentist_id` (`dentist_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `dentists`
--
ALTER TABLE `dentists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dentists`
--
ALTER TABLE `dentists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
