-- MySQL Database Schema for Satta Matka Pro

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    api_token VARCHAR(255) DEFAULT NULL,
    role VARCHAR(20) DEFAULT 'admin'
);

CREATE TABLE IF NOT EXISTS markets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    openTime VARCHAR(20) NOT NULL,
    closeTime VARCHAR(20) NOT NULL,
    isActive TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    market_id INT NOT NULL,
    result_date DATE NOT NULL,
    openNumber VARCHAR(10),
    jodiNumber VARCHAR(10),
    closeNumber VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (market_id) REFERENCES markets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_market_date (market_id, result_date)
);

-- Insert Default Admin (Password is 'admin123')
INSERT INTO users (username, password_hash) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert Initial Markets
INSERT INTO markets (name, openTime, closeTime) VALUES 
('MILAN DAY', '3:00 PM', '5:00 PM'),
('KALYAN', '3:45 PM', '5:45 PM'),
('MAIN BAZAR', '9:35 PM', '12:05 AM');
