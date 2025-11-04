-- Db creation
CREATE DATABASE IF NOT EXISTS orizon_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE orizon_db;

-- countries table
CREATE TABLE IF NOT EXISTS countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Trips table
CREATE TABLE IF NOT EXISTS trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    available_seats INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CHECK (available_seats >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- trip_countries table
CREATE TABLE IF NOT EXISTS trip_countries (
    trip_id INT NOT NULL,
    country_id INT NOT NULL,
    PRIMARY KEY (trip_id, country_id),
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index to speed up queries
CREATE INDEX idx_trip_available_seats ON trips(available_seats);
CREATE INDEX idx_country_name ON countries(name);
CREATE INDEX idx_trip_countries_country ON trip_countries(country_id);

-- Insert data for testing
INSERT INTO countries (name) VALUES 
    ('Italia'),
    ('Francia'),
    ('Spagna'),
    ('Portogallo'),
    ('Grecia'),
    ('Croazia');

INSERT INTO trips (available_seats) VALUES 
    (15),
    (8),
    (20);

INSERT INTO trip_countries (trip_id, country_id) VALUES
    (1, 1), (1, 2),  -- Trip 1: Italy, France
    (2, 3), (2, 4),  -- Trip 2: Spain, Portugal
    (3, 5), (3, 6);  -- Trip 3: Grece, Croatia