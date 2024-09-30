DROP DATABASE IF EXISTS tomtroc;
CREATE DATABASE tomtroc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tomtroc;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                         `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                         `username` VARCHAR(50) NOT NULL UNIQUE,
                         `email` VARCHAR(100) NOT NULL UNIQUE,
                         `password` VARCHAR(255) NOT NULL,
                         `full_name` VARCHAR(100),
                         `profile_picture` VARCHAR(255),
                         `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
                         `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                         `user_id` INT UNSIGNED NOT NULL,
                         `title` VARCHAR(255) NOT NULL,
                         `author` VARCHAR(255) NOT NULL,
                         `image` VARCHAR(255),
                         `description` TEXT,
                         `status` ENUM('disponible', 'non_disponible') DEFAULT 'disponible',
                         `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
                            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            `sender_id` INT UNSIGNED NOT NULL,
                            `receiver_id` INT UNSIGNED NOT NULL,
                            `book_id` INT UNSIGNED NOT NULL,
                            `content` TEXT NOT NULL,
                            `is_read` BOOLEAN DEFAULT FALSE,
                            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                            FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                            FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_books_user_id ON `books`(`user_id`);
CREATE INDEX idx_messages_sender_id ON `messages`(`sender_id`);
CREATE INDEX idx_messages_receiver_id ON `messages`(`receiver_id`);
CREATE INDEX idx_messages_book_id ON `messages`(`book_id`);
