-- migration: seed recipes

INSERT IGNORE INTO `recipes` (`title`, `instructions`, `category`, `cooking_time`, `serving_size`, `image`) VALUES
  (
    'Tomato Pasta',
    'Boil pasta. Warm tomato sauce in a pan, season, then toss with pasta and serve.',
    'pasta',
    20,
    2,
    NULL
  ),
  (
    'Cheesy Omelette',
    'Beat eggs with a splash of milk, cook in butter, add cheddar cheese, fold and serve.',
    'breakfast',
    10,
    1,
    NULL
  ),
  (
    'Chicken & Rice Bowl',
    'Cook rice. Pan-cook chicken breast, season with salt and pepper, and serve over rice.',
    'main',
    30,
    2,
    NULL
  ),
  (
    'Garlic Butter Potatoes',
    'Boil potatoes until tender. Toss with butter, garlic, salt and black pepper.',
    'side',
    25,
    2,
    NULL
  ),
  (
    'Banana Yogurt Bowl',
    'Slice bananas and mix into yogurt. Optional: dust with cinnamon.',
    'dessert',
    5,
    1,
    NULL
  ),
  (
    'Tomato Omelette',
    'Beat eggs with salt and pepper. Cook in butter and add chopped tomatoes. Fold and serve.',
    'breakfast',
    12,
    1,
    NULL
  );
