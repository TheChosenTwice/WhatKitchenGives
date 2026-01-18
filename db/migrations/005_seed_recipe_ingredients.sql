-- migration: seed recipe_ingredients pivot
-- Uses title/name lookups so it doesn't depend on hard-coded IDs.

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id
FROM `recipes` r
JOIN `ingredients` i
WHERE r.title = 'Tomato Pasta'
  AND i.name IN ('Pasta', 'Tomato Sauce / Passata', 'Olive Oil', 'Salt', 'Black Pepper', 'Basil', 'Oregano');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id
FROM `recipes` r
JOIN `ingredients` i
WHERE r.title = 'Cheesy Omelette'
  AND i.name IN ('Eggs', 'Milk', 'Butter', 'Cheddar Cheese', 'Salt', 'Black Pepper');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id
FROM `recipes` r
JOIN `ingredients` i
WHERE r.title = 'Chicken & Rice Bowl'
  AND i.name IN ('Chicken Breast', 'Rice', 'Salt', 'Black Pepper', 'Olive Oil');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id
FROM `recipes` r
JOIN `ingredients` i
WHERE r.title = 'Garlic Butter Potatoes'
  AND i.name IN ('Potatoes', 'Butter', 'Garlic', 'Salt', 'Black Pepper');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id
FROM `recipes` r
JOIN `ingredients` i
WHERE r.title = 'Banana Yogurt Bowl'
  AND i.name IN ('Bananas', 'Yogurt', 'Cinnamon');

INSERT IGNORE INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`)
SELECT r.id, i.id
FROM `recipes` r
JOIN `ingredients` i
WHERE r.title = 'Tomato Omelette'
  AND i.name IN ('Eggs', 'Tomatoes', 'Butter', 'Salt', 'Black Pepper');
