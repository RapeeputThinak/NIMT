CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255),
    billing_address TEXT,
    tax_id VARCHAR(20),
    branch_code VARCHAR(50),
    course_title VARCHAR(255),
    lab_tool VARCHAR(255), /* เพิ่มฟิลด์นี้ */
    instructor_name VARCHAR(255),
    participant_count INT,
    training_date DATE,
    location VARCHAR(255),
    contact_name VARCHAR(255),
    contact_position VARCHAR(255),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    request_date DATE, /* เพิ่มฟิลด์นี้ */
    reference_no VARCHAR(100), /* เพิ่มฟิลด์นี้ */
    arrange_service VARCHAR(50),
    site_vehicle VARCHAR(10),
    site_hotel VARCHAR(10),
    nimt_room VARCHAR(10),
    nimt_food VARCHAR(10),
    consent VARCHAR(10),
    category VARCHAR(50),
    fiscal_year VARCHAR(20), /* เพิ่มฟิลด์นี้ */
    payment_status ENUM('pending', 'paid') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);