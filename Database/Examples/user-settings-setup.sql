CREATE TABLE IF NOT EXISTS user_settings (
  entry_id INT PRIMARY KEY AUTO_INCREMENT,
  meta_key VARCHAR(255),
  meta_value VARCHAR(255),
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);