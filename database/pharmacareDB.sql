-- ============================================================
-- PharmaCare Database Schema
-- Import this in phpMyAdmin or via: mysql -u root -p < pharmacare.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS pharmacare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pharmacare;

-- ------------------------------------------------------------
-- ROLES
-- ------------------------------------------------------------
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO roles (name) VALUES
    ('super_admin'),
    ('admin'),
    ('pharmacist'),
    ('cashier');

-- ------------------------------------------------------------
-- USERS
-- created_by tracks who created the account, to enforce the
-- hierarchy: super_admin -> admin -> (cashier, pharmacist)
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_by INT NULL,
    status ENUM('active','disabled') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- DRUGS (inventory)
-- ------------------------------------------------------------
CREATE TABLE drugs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    category VARCHAR(100) NULL,
    unit VARCHAR(30) NOT NULL DEFAULT 'unit',   -- e.g. tablet, bottle, box
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    quantity_available INT NOT NULL DEFAULT 0,
    reorder_level INT NOT NULL DEFAULT 10,
    added_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- STOCK LOGS (audit trail every time inventory changes)
-- ------------------------------------------------------------
CREATE TABLE stock_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drug_id INT NOT NULL,
    change_qty INT NOT NULL,            -- positive = stock in, negative = sold/removed
    action ENUM('stock_in','sale','adjustment') NOT NULL,
    performed_by INT NOT NULL,
    note VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drug_id) REFERENCES drugs(id),
    FOREIGN KEY (performed_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- DRUG REQUESTS (header) -- created by pharmacist after
-- confirming the doctor's note against available stock
-- ------------------------------------------------------------
CREATE TABLE drug_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_code VARCHAR(20) NOT NULL UNIQUE,   -- e.g. REQ-000123
    customer_name VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(30) NULL,
    pharmacist_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('awaiting_payment','paid','completed','cancelled') NOT NULL DEFAULT 'awaiting_payment',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pharmacist_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- DRUG REQUEST ITEMS (line items - a note can list several drugs)
-- ------------------------------------------------------------
CREATE TABLE drug_request_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    drug_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (request_id) REFERENCES drug_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (drug_id) REFERENCES drugs(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- PAYMENTS / RECEIPTS -- issued by cashier once customer pays
-- ------------------------------------------------------------
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL UNIQUE,
    receipt_no VARCHAR(30) NOT NULL UNIQUE,     -- e.g. RCT-000045
    cashier_id INT NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','card','mobile_money') NOT NULL DEFAULT 'cash',
    paid_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES drug_requests(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- CHECKOUTS -- pharmacist releases drugs once receipt is verified
-- ------------------------------------------------------------
CREATE TABLE checkouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL UNIQUE,
    pharmacist_id INT NOT NULL,
    checked_out_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES drug_requests(id),
    FOREIGN KEY (pharmacist_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- NOTE: No default super admin is seeded here on purpose --
-- a hard-coded password hash in a SQL file is a bad habit to get into.
-- Run database/seed_super_admin.php once (from the browser or CLI)
-- after importing this schema; it hashes a password properly and inserts the account.
-- ------------------------------------------------------------