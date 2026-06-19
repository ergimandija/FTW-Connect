ALTER TABLE chat ADD COLUMN pinned_message_id INT NULL;

ALTER TABLE chat_user ADD COLUMN nickname VARCHAR(255) NULL;

CREATE TABLE message_reaction (
    message_id INT NOT NULL,
    user_id INT NOT NULL,
    emoji VARCHAR(10) NOT NULL,
    PRIMARY KEY (message_id, user_id, emoji),

    CONSTRAINT fk_react_msg
    FOREIGN KEY (message_id)
    REFERENCES message(id)
    ON DELETE CASCADE,

    CONSTRAINT fk_react_usr
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
