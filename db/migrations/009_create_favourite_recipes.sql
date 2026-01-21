-- migration: create favourite_recipes table
CREATE TABLE IF NOT EXISTS `favourite_recipes` (
  `user_id` INT NOT NULL,
  `recipe_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `recipe_id`),
  CONSTRAINT `fk_favourite_recipes_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favourite_recipes_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_favourite_recipes_user_id` (`user_id`),
  INDEX `idx_favourite_recipes_recipe_id` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- favourite_recipes seed data
INSERT INTO `favourite_recipes` (`user_id`, `recipe_id`, `created_at`) VALUES
                                                                           (1,  1, '2026-01-18 10:00:00'),
                                                                           (1,  3, '2026-01-18 10:05:00'),
                                                                           (1,  5, '2026-01-18 10:10:00'),
                                                                           (1,  7, '2026-01-18 10:15:00'),
                                                                           (1,  9, '2026-01-18 10:20:00'),
                                                                           (1, 11, '2026-01-18 10:25:00'),
                                                                           (1, 13, '2026-01-18 10:30:00'),
                                                                           (1, 15, '2026-01-18 10:35:00'),

                                                                           (2,  2, '2026-01-18 11:00:00'),
                                                                           (2,  4, '2026-01-18 11:05:00'),
                                                                           (2,  6, '2026-01-18 11:10:00'),
                                                                           (2,  8, '2026-01-18 11:15:00'),
                                                                           (2, 10, '2026-01-18 11:20:00'),
                                                                           (2, 12, '2026-01-18 11:25:00'),
                                                                           (2, 14, '2026-01-18 11:30:00'),
                                                                           (2, 16, '2026-01-18 11:35:00'),

                                                                           (3,  1, '2026-01-19 09:00:00'),
                                                                           (3,  2, '2026-01-19 09:05:00'),
                                                                           (3,  3, '2026-01-19 09:10:00'),
                                                                           (3,  4, '2026-01-19 09:15:00'),
                                                                           (3,  5, '2026-01-19 09:20:00'),
                                                                           (3, 10, '2026-01-19 09:25:00'),
                                                                           (3, 11, '2026-01-19 09:30:00'),
                                                                           (3, 12, '2026-01-19 09:35:00');

