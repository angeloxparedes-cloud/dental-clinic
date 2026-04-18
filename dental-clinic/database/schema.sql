-- ============================================
-- DENTAL CLINIC APPOINTMENT SYSTEM
-- Database Schema + Stored Procedures
-- ============================================

CREATE DATABASE IF NOT EXISTS dental_clinic;
USE dental_clinic;

-- ============================================
-- TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'patient') DEFAULT 'patient',
    is_verified TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS dentists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    specialization VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    duration_minutes INT DEFAULT 30,
    price DECIMAL(10,2) DEFAULT 0.00,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    dentist_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dentist_id) REFERENCES dentists(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER //

-- Register a new patient
DROP PROCEDURE IF EXISTS sp_register_patient //
CREATE PROCEDURE sp_register_patient(
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_email VARCHAR(150),
    IN p_password VARCHAR(255),
    IN p_phone VARCHAR(20)
)
BEGIN
    DECLARE email_exists INT DEFAULT 0;
    SELECT COUNT(*) INTO email_exists FROM users WHERE email = p_email;
    IF email_exists > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
    ELSE
        INSERT INTO users (first_name, last_name, email, password, phone, role)
        VALUES (p_first_name, p_last_name, p_email, p_password, p_phone, 'patient');
        SELECT LAST_INSERT_ID() AS new_user_id;
    END IF;
END //

-- Find user by email for login
DROP PROCEDURE IF EXISTS sp_find_user_by_email //
CREATE PROCEDURE sp_find_user_by_email(IN p_email VARCHAR(150))
BEGIN
    SELECT id, first_name, last_name, email, password, phone, role, is_verified
    FROM users
    WHERE LOWER(TRIM(email)) = LOWER(TRIM(p_email))
    LIMIT 1;
END //

-- Get all patients
DROP PROCEDURE IF EXISTS sp_get_all_patients //
CREATE PROCEDURE sp_get_all_patients()
BEGIN
    SELECT id, first_name, last_name, email, phone, created_at
    FROM users
    WHERE role = 'patient'
    ORDER BY created_at DESC;
END //

-- Get patient by ID
DROP PROCEDURE IF EXISTS sp_get_patient_by_id //
CREATE PROCEDURE sp_get_patient_by_id(IN p_id INT)
BEGIN
    SELECT id, first_name, last_name, email, phone, created_at
    FROM users WHERE id = p_id AND role = 'patient';
END //

-- Book appointment
DROP PROCEDURE IF EXISTS sp_book_appointment //
CREATE PROCEDURE sp_book_appointment(
    IN p_patient_id INT,
    IN p_dentist_id INT,
    IN p_service_id INT,
    IN p_date DATE,
    IN p_time TIME,
    IN p_notes TEXT
)
BEGIN
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
END //

-- Get appointments for a patient
DROP PROCEDURE IF EXISTS sp_get_patient_appointments //
CREATE PROCEDURE sp_get_patient_appointments(IN p_patient_id INT)
BEGIN
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
END //

-- Get all appointments (admin)
DROP PROCEDURE IF EXISTS sp_get_all_appointments //
CREATE PROCEDURE sp_get_all_appointments()
BEGIN
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
END //

-- Update appointment status
DROP PROCEDURE IF EXISTS sp_update_appointment_status //
CREATE PROCEDURE sp_update_appointment_status(IN p_id INT, IN p_status VARCHAR(20))
BEGIN
    UPDATE appointments SET status = p_status WHERE id = p_id;
    SELECT ROW_COUNT() AS affected;
END //

-- Cancel appointment (patient)
DROP PROCEDURE IF EXISTS sp_cancel_appointment //
CREATE PROCEDURE sp_cancel_appointment(IN p_id INT, IN p_patient_id INT)
BEGIN
    UPDATE appointments SET status = 'cancelled'
    WHERE id = p_id AND patient_id = p_patient_id AND status = 'pending';
    SELECT ROW_COUNT() AS affected;
END //

-- Get all dentists
DROP PROCEDURE IF EXISTS sp_get_dentists //
CREATE PROCEDURE sp_get_dentists()
BEGIN
    SELECT id, first_name, last_name, specialization, email, phone
    FROM dentists WHERE is_active = 1
    ORDER BY first_name;
END //

-- Get all services
DROP PROCEDURE IF EXISTS sp_get_services //
CREATE PROCEDURE sp_get_services()
BEGIN
    SELECT id, name, description, duration_minutes, price
    FROM services WHERE is_active = 1
    ORDER BY name;
END //

-- Admin dashboard stats
DROP PROCEDURE IF EXISTS sp_get_dashboard_stats //
CREATE PROCEDURE sp_get_dashboard_stats()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM appointments WHERE status = 'pending') AS pending_count,
        (SELECT COUNT(*) FROM appointments WHERE status = 'confirmed') AS confirmed_count,
        (SELECT COUNT(*) FROM appointments WHERE status = 'completed') AS completed_count,
        (SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = CURDATE()) AS today_count,
        (SELECT COUNT(*) FROM users WHERE role = 'patient') AS total_patients,
        (SELECT COUNT(*) FROM appointments) AS total_appointments;
END //

DELIMITER ;

-- ============================================
-- SEED DATA
-- ============================================

-- Admin user (password: Admin@1234)
INSERT INTO users (first_name, last_name, email, password, role, is_verified)
VALUES ('Admin', 'User', 'admin@dentalclinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1)
ON DUPLICATE KEY UPDATE id=id;

-- Dentists
INSERT INTO dentists (first_name, last_name, specialization, email, phone) VALUES
('Maria', 'Santos', 'General Dentistry', 'maria@dentalclinic.com', '09171234567'),
('Jose', 'Reyes', 'Orthodontics', 'jose@dentalclinic.com', '09181234567'),
('Ana', 'Cruz', 'Cosmetic Dentistry', 'ana@dentalclinic.com', '09191234567')
ON DUPLICATE KEY UPDATE id=id;

-- Services
INSERT INTO services (name, description, duration_minutes, price) VALUES
('General Checkup', 'Routine dental examination and cleaning', 30, 500.00),
('Tooth Extraction', 'Simple or surgical tooth removal', 45, 1500.00),
('Dental Filling', 'Composite or amalgam filling', 60, 2000.00),
('Teeth Whitening', 'Professional teeth whitening treatment', 90, 5000.00),
('Braces Consultation', 'Initial consultation for orthodontic treatment', 45, 800.00),
('Root Canal', 'Endodontic root canal treatment', 90, 8000.00),
('Dental Crown', 'Porcelain or metal crown placement', 60, 6000.00),
('Oral Prophylaxis', 'Professional teeth cleaning and polishing', 45, 1200.00)
ON DUPLICATE KEY UPDATE id=id;
