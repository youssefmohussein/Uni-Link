<?php
require_once 'config.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Map meal names to Metadata: [Filename, Category, Price, Description]
    $items = [
        'Bruschetta' => ['Bruschetta Trio.jpg', 'Starters', 150.00, 'Crispy baguette slices topped with fresh tomatoes, basil, and balsamic glaze.'],
        'Chicken Alfredo' => ['Chicken Alfredo Pasta.jpg', 'Main Courses', 250.00, 'Creamy fettuccine pasta with grilled chicken breast and parmesan.'],
        'Grilled Salmon' => ['Grilled Salmon Delight.jpg', 'Main Courses', 380.00, 'Fresh Atlantic salmon grilled to perfection, served with asparagus.'],
        'Lasagna' => ['Lasagna.jpg', 'Main Courses', 220.00, 'Layers of pasta, rich meat sauce, and melted mozzarella cheese.'],
        'Mini Lemon Cheesecakes' => ['Mini Lemon Cheesecakes.jpg', 'Desserts', 120.00, 'Zesty and creamy mini cheesecakes with a graham cracker crust.'],
        'Samosa' => ['Samosa.jpg', 'Starters', 90.00, 'Crispy pastry filled with spiced potatoes and peas.'],
        'Tiramisu' => ['Tiramisu.jpg', 'Desserts', 140.00, 'Classic Italian dessert with coffee-soaked ladyfingers and mascarpone cream.'],
        'Tomato Sauce Pasta' => ['Tomato Sauce Pasta.jpg', 'Main Courses', 160.00, 'Simple yet delicious pasta tossed in our signature marinara sauce.'],
        'Tres Leches' => ['Tres Leches Cake.jpg', 'Desserts', 130.00, 'Sponge cake soaked in three kinds of milk, topped with whipped cream.'],
        'Truffle Arancini' => ['Truffle Arancini.jpg', 'Starters', 180.00, 'Fried risotto balls infused with truffle oil and stuffed with cheese.'],
        'Truffle Pasta' => ['Truffle Pasta.jpg', 'Main Courses', 290.00, 'Elegant pasta dish with black truffle shavings and cream sauce.'],
        'Mahshi' => ['mahshi.jpg', 'Main Courses', 190.00, 'Traditional stuffed vegetables with spiced rice and herbs.'],
        'Chocolate Lava Cake' => ['Chocolate Lava Cake.png', 'Desserts', 160.00, 'Warm chocolate cake with a molten center, served with vanilla ice cream.'],
        'Margherita Pizza' => ['Margherita Pizza.png', 'Main Courses', 200.00, 'Classic pizza with tomato sauce, fresh mozzarella, and basil.']
    ];

    echo "Syncing menu items...\n";
    
    foreach ($items as $name => $data) {
        $image = $data[0];
        $category = $data[1];
        $price = $data[2];
        $desc = $data[3];

        // Check if item exists (by name matching)
        $stmt = $pdo->prepare("SELECT meal_id FROM meals WHERE meal_name LIKE ?");
        $stmt->execute(["%$name%"]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update Image
            $update = $pdo->prepare("UPDATE meals SET image = ? WHERE meal_id = ?");
            $update->execute([$image, $existing['meal_id']]);
            echo "Updated image for '$name'.\n";
        } else {
            // Insert New Item
            $insert = $pdo->prepare("
                INSERT INTO meals (meal_name, meal_type, price, description, image, quantity, availability, created_at)
                VALUES (?, ?, ?, ?, ?, 50, 'Available', NOW())
            ");
            $insert->execute([$name, $category, $price, $desc, $image]);
            echo "Inserted new item: '$name'.\n";
        }
    }
    
    // Check for any remaining 'images/' prefixes and strip them if they don't match our new logic
    // Actually, let's just create a general cleanup that if it starts with 'images/', we assume it's legacy and maybe clear it or leave it?
    // Leaving it might break the path 'images/meals-imgs/images/...'
    // Let's set anything NOT in our mapped list to NULL if it looks broken?
    // Safer: Let the fallback handle it.
    
    echo "Update complete.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
