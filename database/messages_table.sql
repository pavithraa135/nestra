-- Nestra: messages table for match chat
CREATE TABLE IF NOT EXISTS messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT NOT NULL,
    receiver_id INT NOT NULL,
    message     TEXT NOT NULL,
    sent_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_convo (sender_id, receiver_id),
    INDEX idx_sent  (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
