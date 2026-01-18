-- migration: create recipes, ingredients, and recipe_ingredients tables

CREATE TABLE IF NOT EXISTS `ingredients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(128) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `recipes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `instructions` TEXT NOT NULL,
  `category` VARCHAR(64) DEFAULT NULL,
  `cooking_time` INT DEFAULT NULL,
  `serving_size` INT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `recipe_ingredients` (
  `recipe_id` INT NOT NULL,
  `ingredient_id` INT NOT NULL,
  PRIMARY KEY (`recipe_id`, `ingredient_id`),
  CONSTRAINT `fk_recipe_ingredients_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recipe_ingredients_ingredient`
    FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_recipe_ingredients_ingredient_id` (`ingredient_id`),
  INDEX `idx_recipe_ingredients_recipe_id` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
