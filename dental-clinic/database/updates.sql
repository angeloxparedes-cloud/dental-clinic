-- ============================================
-- UPDATES: Payments + Dentist Mgmt + Settings
-- Run this in phpMyAdmin after schema.sql
-- ============================================

USE dental_clinic;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    patient_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('cash','gcash','maya','card') DEFAULT 'cash',
    status ENUM('pending','paid','refunded') DEFAULT 'pending',
    reference_no VARCHAR(100),
    notes TEXT,
    paid_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
);

DELIMITER //

-- ============================================
-- DENTIST MANAGEMENT PROCEDURES
-- ============================================

DROP PROCEDURE IF EXISTS sp_add_dentist //
CREATE PROCEDURE sp_add_dentist(
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_specialization VARCHAR(150),
    IN p_email VARCHAR(150),
    IN p_phone VARCHAR(20)
)
BEGIN
    INSERT INTO dentists (first_name, last_name, specialization, email, phone)
    VALUES (p_first_name, p_last_name, p_specialization, p_email, p_phone);
    SELECT LAST_INSERT_ID() AS new_id;
END //

DROP PROCEDURE IF EXISTS sp_update_dentist //
CREATE PROCEDURE sp_update_dentist(
    IN p_id INT,
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_specialization VARCHAR(150),
    IN p_email VARCHAR(150),
    IN p_phone VARCHAR(20),
    IN p_is_active TINYINT(1)
)
BEGIN
    UPDATE dentists
    SET first_name=p_first_name, last_name=p_last_name,
        specialization=p_specialization, email=p_email,
        phone=p_phone, is_active=p_is_active
    WHERE id = p_id;
END //

DROP PROCEDURE IF EXISTS sp_delete_dentist //
CREATE PROCEDURE sp_delete_dentist(IN p_id INT)
BEGIN
    UPDATE dentists SET is_active = 0 WHERE id = p_id;
END //

DROP PROCEDURE IF EXISTS sp_get_all_dentists //
CREATE PROCEDURE sp_get_all_dentists()
BEGIN
    SELECT id, first_name, last_name, specialization, email, phone, is_active
    FROM dentists ORDER BY first_name;
END //

DROP PROCEDURE IF EXISTS sp_get_dentist_by_id //
CREATE PROCEDURE sp_get_dentist_by_id(IN p_id INT)
BEGIN
    SELECT id, first_name, last_name, specialization, email, phone, is_active
    FROM dentists WHERE id = p_id;
END //

-- ============================================
-- PAYMENT PROCEDURES
-- ============================================

DROP PROCEDURE IF EXISTS sp_create_payment //
CREATE PROCEDURE sp_create_payment(
    IN p_appointment_id INT,
    IN p_patient_id INT,
    IN p_amount DECIMAL(10,2),
    IN p_method VARCHAR(20),
    IN p_reference_no VARCHAR(100),
    IN p_notes TEXT
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS sp_get_patient_payments //
CREATE PROCEDURE sp_get_patient_payments(IN p_patient_id INT)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS sp_get_all_payments //
CREATE PROCEDURE sp_get_all_payments()
BEGIN
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
END //

DROP PROCEDURE IF EXISTS sp_update_payment_status //
CREATE PROCEDURE sp_update_payment_status(
    IN p_id INT,
    IN p_status VARCHAR(20),
    IN p_notes TEXT
)
BEGIN
    UPDATE payments
    SET status = p_status,
        paid_at = IF(p_status = 'paid', NOW(), paid_at),
        notes = IF(p_notes != '', p_notes, notes)
    WHERE id = p_id;
END //

DROP PROCEDURE IF EXISTS sp_get_payment_by_appointment //
CREATE PROCEDURE sp_get_payment_by_appointment(IN p_appointment_id INT)
BEGIN
    SELECT * FROM payments WHERE appointment_id = p_appointment_id LIMIT 1;
END //

-- ============================================
-- PROFILE / SETTINGS PROCEDURES
-- ============================================

DROP PROCEDURE IF EXISTS sp_update_profile //
CREATE PROCEDURE sp_update_profile(
    IN p_id INT,
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_phone VARCHAR(20)
)
BEGIN
    UPDATE users SET first_name=p_first_name, last_name=p_last_name, phone=p_phone
    WHERE id = p_id;
    SELECT first_name, last_name, phone FROM users WHERE id = p_id;
END //

DROP PROCEDURE IF EXISTS sp_change_password //
CREATE PROCEDURE sp_change_password(
    IN p_id INT,
    IN p_new_password VARCHAR(255)
)
BEGIN
    UPDATE users SET password = p_new_password WHERE id = p_id;
    SELECT ROW_COUNT() AS affected;
END //

DROP PROCEDURE IF EXISTS sp_get_user_by_id //
CREATE PROCEDURE sp_get_user_by_id(IN p_id INT)
BEGIN
    SELECT id, first_name, last_name, email, phone, role FROM users WHERE id = p_id;
END //

-- Updated dashboard stats including payments
DROP PROCEDURE IF EXISTS sp_get_dashboard_stats //
CREATE PROCEDURE sp_get_dashboard_stats()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM appointments WHERE status='pending') AS pending_count,
        (SELECT COUNT(*) FROM appointments WHERE status='confirmed') AS confirmed_count,
        (SELECT COUNT(*) FROM appointments WHERE status='completed') AS completed_count,
        (SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date)=CURDATE()) AS today_count,
        (SELECT COUNT(*) FROM users WHERE role='patient') AS total_patients,
        (SELECT COUNT(*) FROM appointments) AS total_appointments,
        (SELECT COUNT(*) FROM payments WHERE status='pending') AS pending_payments,
        (SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid') AS total_revenue;
END //

DELIMITER ;
