-- --------------------------------------------------------------------------------
-- Table des utilisateurs
-- --------------------------------------------------------------------------------
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(255) NOT NULL UNIQUE,
                       email VARCHAR(255) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       full_name VARCHAR(255) NULL,
                       profile_picture VARCHAR(255) NULL,
                       created_at DATETIME NOT NULL,
                       updated_at DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------------------
-- Table des livres
-- --------------------------------------------------------------------------------
CREATE TABLE books (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       user_id INT NOT NULL,
                       title VARCHAR(255) NOT NULL,
                       author VARCHAR(255) NOT NULL,
                       image VARCHAR(255) DEFAULT NULL,
                       description TEXT DEFAULT NULL,
                       status VARCHAR(20) NOT NULL DEFAULT 'disponible',
                       created_at DATETIME NOT NULL,
                       updated_at DATETIME DEFAULT NULL,
                       CONSTRAINT fk_books_user
                           FOREIGN KEY (user_id)
                               REFERENCES users(id)
                               ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------------------
-- Table des conversations
-- --------------------------------------------------------------------------------
CREATE TABLE conversations (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               user_one_id INT NOT NULL,
                               user_two_id INT NOT NULL,
                               created_at DATETIME NOT NULL,
                               updated_at DATETIME DEFAULT NULL,
                               CONSTRAINT fk_conversations_user_one
                                   FOREIGN KEY (user_one_id)
                                       REFERENCES users(id)
                                       ON DELETE CASCADE,
                               CONSTRAINT fk_conversations_user_two
                                   FOREIGN KEY (user_two_id)
                                       REFERENCES users(id)
                                       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------------------
-- Table des messages
-- --------------------------------------------------------------------------------
CREATE TABLE messages (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          conversation_id INT NOT NULL,
                          sender_id INT NOT NULL,
                          content TEXT NOT NULL,
                          sent_at DATETIME NOT NULL,
                          is_read_by_user_one TINYINT(1) NOT NULL DEFAULT 0,
                          is_read_by_user_two TINYINT(1) NOT NULL DEFAULT 0,
                          created_at DATETIME NOT NULL,
                          updated_at DATETIME DEFAULT NULL,
                          CONSTRAINT fk_messages_conversation
                              FOREIGN KEY (conversation_id)
                                  REFERENCES conversations(id)
                                  ON DELETE CASCADE,
                          CONSTRAINT fk_messages_sender
                              FOREIGN KEY (sender_id)
                                  REFERENCES users(id)
                                  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;