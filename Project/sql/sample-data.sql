-- Create database
CREATE DATABASE IF NOT EXISTS smart_browser_state;
USE smart_browser_state;

-- Create table
CREATE TABLE IF NOT EXISTS browser_states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(50) NOT NULL,
    browser_name VARCHAR(50) NOT NULL,
    tab_count INT NOT NULL,
    save_type ENUM('Auto-saved','Manual') NOT NULL,
    created_at DATETIME NOT NULL
);

-- Insert sample data
INSERT INTO browser_states (device_name, browser_name, tab_count, save_type, created_at) VALUES
('Laptop','Chrome',15,'Auto-saved','2026-02-10 09:15:00'),
('Desktop','Firefox',8,'Manual','2026-02-09 14:25:00'),
('Work PC','Edge',12,'Auto-saved','2026-02-08 11:40:00'),
('Mobile','Safari',5,'Manual','2026-02-11 18:10:00'),
('Laptop','Chrome',20,'Auto-saved','2026-02-12 08:05:00'),
('Desktop','Edge',7,'Manual','2026-02-12 16:30:00'),
('Work PC','Chrome',10,'Auto-saved','2026-02-13 09:55:00'),
('Mobile','Firefox',6,'Manual','2026-02-13 21:15:00'),
('Laptop','Safari',18,'Manual','2026-02-14 12:10:00'),
('Desktop','Chrome',22,'Auto-saved','2026-02-14 15:20:00'),
('Work PC','Firefox',14,'Manual','2026-02-15 10:05:00'),
('Mobile','Chrome',4,'Auto-saved','2026-02-15 22:00:00'),
('Laptop','Edge',9,'Auto-saved','2026-02-16 08:30:00'),
('Desktop','Safari',13,'Manual','2026-02-16 14:45:00'),
('Work PC','Chrome',17,'Auto-saved','2026-02-16 18:55:00'),
('Mobile','Edge',3,'Manual','2026-02-17 07:25:00'),
('Laptop','Firefox',11,'Auto-saved','2026-02-17 09:10:00'),
('Desktop','Chrome',19,'Manual','2026-02-17 20:30:00');
