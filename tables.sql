CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN status ENUM('active', 'blocked') DEFAULT 'active';
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT 'default.png';

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO admin (username, password) VALUES ('admin', MD5('admin@1'));


CREATE TABLE mining (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points_earned INT DEFAULT 0,
    last_mined TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


ALTER TABLE users ADD COLUMN facebook_followed TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN twitter_followed TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN instagram_followed TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN linkedin_followed TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN total_points INT DEFAULT 0;
