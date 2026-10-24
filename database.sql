CREATE DATABASE IF NOT EXISTS dental_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dental_db;

DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS slots;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    capacity INT NOT NULL DEFAULT 1
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    slot_id INT NOT NULL,
    note VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'confirmed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_slot (user_id, slot_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES slots(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@dental.com', '$2y$10$lkb3ZBZEr5wbLQfH6.xyEO4iMTh0FEFQMXMkqowUD6qb7Neow83Vi', 'admin'),
('Demo Patient', 'patient@dental.com', '$2y$10$TFGGNSSqS4B2xIO92qHojuc3OnEbaFg9ta.0R6sdqzs1xSPMf1LX.', 'user');

INSERT INTO slots (slot_date, start_time, end_time, capacity) VALUES
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '10:00:00', 2),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', '11:00:00', 1),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', '12:00:00', 3),
(DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', '10:00:00', 2),
(DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '15:00:00', 2),
(DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', '11:00:00', 1);
