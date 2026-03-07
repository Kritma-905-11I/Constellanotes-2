-- ================================================
-- Luminary Database Schema
-- Run once: mysql -u root -p < php/schema.sql
-- ================================================

CREATE DATABASE IF NOT EXISTS luminary_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE luminary_db;

CREATE TABLE IF NOT EXISTS users (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  first_name   VARCHAR(80)  NOT NULL,
  last_name    VARCHAR(80)  NOT NULL,
  dob          DATE         NOT NULL,
  email        VARCHAR(180) NOT NULL UNIQUE,
  password     VARCHAR(255) NOT NULL,
  avatar_url   VARCHAR(500) DEFAULT NULL,
  created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS stars (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT          NOT NULL,
  star_name  VARCHAR(120) NOT NULL,
  color_hue  INT          DEFAULT 270,
  created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notes (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  star_id    INT          NOT NULL,
  user_id    INT          NOT NULL,
  title      VARCHAR(255) DEFAULT 'Untitled',
  body       MEDIUMTEXT,
  color_hue  INT          DEFAULT 270,
  created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (star_id)  REFERENCES stars(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;