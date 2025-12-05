-- Tabla para Beta Testers
CREATE TABLE IF NOT EXISTS beta_testers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    country VARCHAR(100),
    company VARCHAR(255),
    interested_app VARCHAR(255),
    problems_to_solve TEXT,
    technical_level ENUM('user', 'advanced', 'developer') DEFAULT 'user',
    status ENUM('pending', 'active', 'inactive') DEFAULT 'pending',
    access_token VARCHAR(64) UNIQUE,
    discord_username VARCHAR(100),
    telegram_username VARCHAR(100),
    contribution_level ENUM('bronze', 'silver', 'gold', 'platinum') DEFAULT 'bronze',
    bugs_reported INT DEFAULT 0,
    suggestions_accepted INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_contribution_level (contribution_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
