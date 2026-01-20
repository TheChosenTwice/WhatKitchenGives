-- migration: create categories table and seed data
-- Creates a simple `categories` table and inserts category rows sufficient
-- to cover all ingredients present in 003_seed_ingredients.sql

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `categories` (`name`) VALUES
  ('Fruits'),
  ('Vegetables'),
  ('Dairy & Eggs'),
  ('Meat & Poultry'),
  ('Seafood'),
  ('Baking & Staples'),
  ('Oils & Vinegars'),
  ('Condiments & Sauces'),
  ('Grains & Bread'),
  ('Canned Goods'),
  ('Herbs & Spices');
