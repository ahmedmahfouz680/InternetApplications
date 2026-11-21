CREATE DATABASE IF NOT EXISTS company_db;
USE company_db;

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

INSERT INTO employees (name, email) VALUES
('Ahmed Ali', 'ahmed@example.com'),
('Sara Mohamed', 'sara@example.com');