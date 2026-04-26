CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    pwdHash VARCHAR(255) NOT NULL,
    role VARCHAR(50),
    status VARCHAR(50),
    profilePicturePath VARCHAR(255),
    reset_token VARCHAR(255),
    reset_expires DATETIME,
    failed_attempts INT DEFAULT 0,
    locked_until DATETIME NULL;
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    
    CONSTRAINT fkpous
    FOREIGN KEY (user_id)
    REFERENCES Users(id)
    ON DELETE CASCADE
);

CREATE TABLE Comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    post_id INT,

    CONSTRAINT fkcous
    FOREIGN KEY (user_id)
    REFERENCES Users(id)
    ON DELETE CASCADE,

    CONSTRAINT fkcopo
    FOREIGN KEY (post_id)
    REFERENCES Post(id)
    ON DELETE CASCADE
);

CREATE TABLE Message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT,
    sender_id INT,
    content VARBINARY(4096),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fkmech
    FOREIGN KEY (chat_id)
    REFERENCES Chat(id)
    ON DELETE CASCADE,

    CONSTRAINT fkmeus
    FOREIGN KEY (sender_id)
    REFERENCES Users(id)
    ON DELETE CASCADE
);

-- N:M relationship between User and Chat
CREATE TABLE Chat_User (
    chat_id INT,
    user_id INT,
    role VARCHAR(50),
    type VARCHAR(50),

    PRIMARY KEY (chat_id, user_id),

    CONSTRAINT fkcuch
    FOREIGN KEY (chat_id)
    REFERENCES Chat(id)
    ON DELETE CASCADE,

    CONSTRAINT fkcuus
    FOREIGN KEY (user_id)
    REFERENCES Users(id)
    ON DELETE CASCADE
);