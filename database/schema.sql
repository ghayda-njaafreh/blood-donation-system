-- Blood Donation System (PHP + MySQLi Prepared Statements)
-- Import this file into phpMyAdmin or MySQL CLI.
-- Recommended DB name: blood_donation

CREATE DATABASE IF NOT EXISTS blood_donation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blood_donation;

-- Users (admins)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Donors
CREATE TABLE IF NOT EXISTS donors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  city VARCHAR(80) NOT NULL,
  blood_type VARCHAR(3) NOT NULL,
  age INT NULL,
  last_donation_date DATE NULL,
  available TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_blood_city (blood_type, city),
  INDEX idx_available (available)
) ENGINE=InnoDB;

-- Blood Requests
CREATE TABLE IF NOT EXISTS requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  requester_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  city VARCHAR(80) NOT NULL,
  needed_blood_type VARCHAR(3) NOT NULL,
  units INT NOT NULL DEFAULT 1,
  needed_date DATE NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_needed_city (needed_blood_type, city)
) ENGINE=InnoDB;
