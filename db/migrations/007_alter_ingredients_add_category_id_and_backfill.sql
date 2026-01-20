-- migration: alter ingredients add category_id and backfill
-- Adds a nullable `category_id` column to `ingredients`, an index and foreign key,
-- then backfills existing rows using the `categories` table.

ALTER TABLE `ingredients`
  ADD COLUMN `category_id` INT UNSIGNED NULL AFTER `name`,
  ADD INDEX `idx_ingredients_category_id` (`category_id`);

ALTER TABLE `ingredients`
  ADD CONSTRAINT `fk_ingredients_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- Backfill category_id for existing ingredients by matching category name

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Fruits'
  SET i.category_id = c.id
  WHERE i.name IN ('Apples', 'Bananas', 'Lemons', 'Pears');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Vegetables'
  SET i.category_id = c.id
  WHERE i.name IN (
    'Tomatoes', 'Potatoes', 'Onions', 'Garlic', 'Carrots', 'Bell Peppers',
    'Broccoli', 'Cauliflower', 'Mushrooms', 'Zucchini', 'Cucumber', 'Salad / Lettuce'
  );

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Dairy & Eggs'
  SET i.category_id = c.id
  WHERE i.name IN ('Eggs', 'Milk', 'Butter', 'Cheddar Cheese', 'Cream Cheese', 'Yogurt');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Meat & Poultry'
  SET i.category_id = c.id
  WHERE i.name IN ('Chicken Breast', 'Chicken (in General)', 'Ground Beef', 'Pork', 'Bacon');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Seafood'
  SET i.category_id = c.id
  WHERE i.name IN ('Fish (in General)');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Baking & Staples'
  SET i.category_id = c.id
  WHERE i.name IN (
    'Flour', 'Sugar', 'Brown Sugar', 'Salt', 'Black Pepper', 'Baking Powder', 'Baking Soda'
  );

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Oils & Vinegars'
  SET i.category_id = c.id
  WHERE i.name IN ('Olive Oil', 'Vegetable Oil', 'Vinegar');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Condiments & Sauces'
  SET i.category_id = c.id
  WHERE i.name IN (
    'Ketchup', 'Mayonnaise', 'Mustard', 'Soy Sauce', 'Hot Sauce', 'Tomato Sauce / Passata'
  );

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Grains & Bread'
  SET i.category_id = c.id
  WHERE i.name IN ('Bread (in General)', 'Rice', 'Pasta', 'Tortillas');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Canned Goods'
  SET i.category_id = c.id
  WHERE i.name IN ('Canned Tomatoes', 'Beans (canned)', 'Chicken Broth / Stock');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Herbs & Spices'
  SET i.category_id = c.id
  WHERE i.name IN ('Oregano', 'Basil', 'Paprika', 'Cinnamon', 'Garlic Powder', 'Onion Powder');

-- Any remaining ingredients will keep category_id = NULL
