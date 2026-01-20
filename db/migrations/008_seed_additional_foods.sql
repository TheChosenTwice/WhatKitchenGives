-- migration: seed additional common ingredients and popular/fast-food recipes
-- Inserts additional ingredients, popular recipes (burgers, nuggets, pizza, tacos, etc.)
-- and populates the `recipe_ingredients` pivot using SELECT joins (title/name lookup).

-- Add commonly used ingredients not present in previous seeds
INSERT IGNORE INTO `ingredients` (`name`) VALUES
  ('Mozzarella Cheese'),
  ('Parmesan Cheese'),
  ('Avocado'),
  ('Lime'),
  ('Cilantro'),
  ('Pickles'),
  ('Burger Buns'),
  ('Breadcrumbs'),
  ('Shrimp'),
  ('Tuna'),
  ('Sweet Potato'),
  ('Maple Syrup');

-- Seed popular / fast-food style recipes
INSERT IGNORE INTO `recipes` (`title`, `instructions`, `category`, `cooking_time`, `serving_size`, `image`) VALUES
  (
    'Classic Cheeseburger',
    'Form patties from ground beef, season, grill or pan-fry, assemble on buns with cheese, lettuce, tomato, onions and pickles. Serve with ketchup/mustard.',
    'fastfood',
    20,
    1,
    NULL
  ),
  (
    'Chicken Nuggets',
    'Cut chicken into pieces, bread with flour/egg/breadcrumbs, fry until golden and cooked through. Serve with ketchup or dip.',
    'fastfood',
    25,
    2,
    NULL
  ),
  (
    'Margherita Pizza',
    'Prepare dough, spread tomato passata, top with sliced mozzarella and basil, bake until crust is golden and cheese melted.',
    'pizza',
    18,
    2,
    NULL
  ),
  (
    'Fish Tacos',
    'Season and pan-fry fish, warm tortillas, assemble with shredded lettuce, chopped tomatoes, cilantro and a squeeze of lime. Serve with yogurt-based sauce.',
    'tacos',
    20,
    2,
    NULL
  ),
  (
    'Shrimp Stir Fry',
    'Stir-fry shrimp with garlic, bell peppers and onions, add soy sauce and serve over rice.',
    'stirfry',
    15,
    2,
    NULL
  ),
  (
    'BLT Sandwich',
    'Layer crispy bacon, lettuce and tomato on bread with mayonnaise. Serve toasted or cold.',
    'sandwich',
    10,
    1,
    NULL
  ),
  (
    'Pancakes',
    'Mix flour, milk, eggs, sugar and baking powder to a batter. Fry spoonfuls on a hot pan until golden both sides. Serve with butter and maple syrup.',
    'breakfast',
    15,
    2,
    NULL
  ),
  (
    'Grilled Cheese Sandwich',
    'Butter bread, place cheese between slices and grill in a pan until bread is golden and cheese melted.',
    'sandwich',
    8,
    1,
    NULL
  ),
  (
    'Tuna Melt',
    'Mix canned tuna with mayonnaise and chopped onion, spread on bread, top with cheese and toast until melted.',
    'sandwich',
    10,
    1,
    NULL
  ),
  (
    'Sweet Potato Fries',
    'Cut sweet potatoes into sticks, toss with oil, salt and pepper, and roast or fry until crispy.',
    'side',
    30,
    2,
    NULL
  );

-- Populate recipe_ingredients pivot using recipe titles and ingredient names (id lookup)
INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Classic Cheeseburger'
  AND i.name IN ('Ground Beef', 'Burger Buns', 'Cheddar Cheese', 'Salad / Lettuce', 'Tomatoes', 'Onions', 'Pickles', 'Ketchup', 'Mustard', 'Salt', 'Black Pepper');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Chicken Nuggets'
  AND i.name IN ('Chicken Breast', 'Flour', 'Eggs', 'Breadcrumbs', 'Salt', 'Black Pepper', 'Vegetable Oil');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Margherita Pizza'
  AND i.name IN ('Flour', 'Tomato Sauce / Passata', 'Mozzarella Cheese', 'Basil', 'Olive Oil', 'Salt');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Fish Tacos'
  AND i.name IN ('Fish (in General)', 'Tortillas', 'Salad / Lettuce', 'Tomatoes', 'Onions', 'Yogurt', 'Cilantro', 'Lime', 'Salt');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Shrimp Stir Fry'
  AND i.name IN ('Shrimp', 'Garlic', 'Bell Peppers', 'Onions', 'Soy Sauce', 'Vegetable Oil', 'Rice');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'BLT Sandwich'
  AND i.name IN ('Bacon', 'Salad / Lettuce', 'Tomatoes', 'Bread (in General)', 'Mayonnaise');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Pancakes'
  AND i.name IN ('Flour', 'Milk', 'Eggs', 'Sugar', 'Baking Powder', 'Butter', 'Maple Syrup');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Grilled Cheese Sandwich'
  AND i.name IN ('Bread (in General)', 'Butter', 'Cheddar Cheese');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Tuna Melt'
  AND i.name IN ('Tuna', 'Mayonnaise', 'Bread (in General)', 'Cheddar Cheese', 'Onions');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id FROM `recipes` r JOIN `ingredients` i
WHERE r.title = 'Sweet Potato Fries'
  AND i.name IN ('Sweet Potato', 'Olive Oil', 'Salt', 'Black Pepper');

-- Backfill category_id for newly inserted ingredients
UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Dairy & Eggs'
  SET i.category_id = c.id
  WHERE i.name IN ('Mozzarella Cheese', 'Parmesan Cheese');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Fruits'
  SET i.category_id = c.id
  WHERE i.name IN ('Avocado', 'Lime');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Herbs & Spices'
  SET i.category_id = c.id
  WHERE i.name IN ('Cilantro');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Condiments & Sauces'
  SET i.category_id = c.id
  WHERE i.name IN ('Pickles');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Grains & Bread'
  SET i.category_id = c.id
  WHERE i.name IN ('Burger Buns');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Baking & Staples'
  SET i.category_id = c.id
  WHERE i.name IN ('Breadcrumbs', 'Maple Syrup');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Seafood'
  SET i.category_id = c.id
  WHERE i.name IN ('Shrimp', 'Tuna');

UPDATE `ingredients` i
  JOIN `categories` c ON c.name = 'Vegetables'
  SET i.category_id = c.id
  WHERE i.name IN ('Sweet Potato');

