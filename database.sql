-- ─────────────────────────────────────────────────────────────────────────
-- UniHub Database Schema
-- MySQL 8+ | PHP 8 Backend
-- Run: mysql -u root -p unihub < database.sql
-- ─────────────────────────────────────────────────────────────────────────

CREATE DATABASE IF NOT EXISTS unihub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE unihub;

-- ─── USERS ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(80)  NOT NULL,
    last_name     VARCHAR(80)  NOT NULL,
    email         VARCHAR(180) NOT NULL UNIQUE,
    student_id    VARCHAR(30)  DEFAULT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('student','admin','staff') NOT NULL DEFAULT 'student',
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role  (role)
) ENGINE=InnoDB;

-- ─── COURSES ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS courses (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(20)  NOT NULL UNIQUE,
    name        VARCHAR(200) NOT NULL,
    semester    VARCHAR(30)  DEFAULT NULL,
    description TEXT         DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code)
) ENGINE=InnoDB;

-- ─── RESOURCES ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS resources (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uploader_id   INT UNSIGNED NOT NULL,
    title         VARCHAR(255) NOT NULL,
    description   TEXT         DEFAULT NULL,
    course        VARCHAR(200) DEFAULT NULL,
    category      ENUM('slide','lab','note','other') NOT NULL DEFAULT 'other',
    tags          JSON         DEFAULT NULL,
    file_path     VARCHAR(500) DEFAULT NULL,
    external_link VARCHAR(500) DEFAULT NULL,
    file_size     INT UNSIGNED DEFAULT NULL,
    views         INT UNSIGNED NOT NULL DEFAULT 0,
    status        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_uploader (uploader_id)
) ENGINE=InnoDB;

-- ─── BOOKMARKS ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS bookmarks (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    resource_id INT UNSIGNED NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_resource (user_id, resource_id),
    FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── NOTICES ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notices (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT UNSIGNED NOT NULL,
    title       VARCHAR(255) NOT NULL,
    category    ENUM('academic','exam','event','urgent') NOT NULL DEFAULT 'academic',
    audience    ENUM('all','student','staff') NOT NULL DEFAULT 'all',
    message     TEXT         NOT NULL,
    expiry_date DATE         DEFAULT NULL,
    is_pinned   TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_audience (audience),
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- ─── FEEDBACK ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS feedback (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED NOT NULL,
    subject        VARCHAR(255) NOT NULL,
    topic_category ENUM('tech','academic','advisor') NOT NULL DEFAULT 'tech',
    message        TEXT         NOT NULL,
    status         ENUM('open','resolved') NOT NULL DEFAULT 'open',
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────────────────────────────────
-- SEED DATA
-- Admin password: Admin@1234
-- Students register their own accounts via /student/register.php
-- ─────────────────────────────────────────────────────────────────────────

INSERT INTO users (first_name, last_name, email, student_id, password_hash, role) VALUES
('System', 'Administrator', 'admin@rjt.ac.lk', NULL, '$2y$12$XMmy3LA9s2PR1EgD4Bl8n.Rx2gLjNBQmfS/hnElb2jfWU1alp.Lge', 'admin');

INSERT INTO courses (code, name, semester, description) VALUES
('ICT1209', 'Web Technologies',             'Semester 1, 2024', 'HTML, CSS, JS, PHP and MySQL fundamentals for web development.'),
('ICT1207', 'Human Computer Interaction',   'Semester 1, 2024', 'Design principles and UX methodologies for interactive systems.'),
('ICT3201', 'Data Structures',              'Semester 2, 2024', 'Arrays, linked lists, trees, graphs and complexity analysis.'),
('CML1204', 'Principles of Management',     'Semester 1, 2024', 'Core management theories, leadership and organizational behaviour.'),
('CML1203', 'Health and Wellbeing',         'Semester 1, 2024', 'Community health, family planning and wellness strategies.');

