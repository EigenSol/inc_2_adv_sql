<?php

require_once __DIR__ . '/../util/conn.php';

function seedUsers() {
    global $conn;

    $users = [
        ['John Doe', 'john@example.com'],
        ['Jane Smith', 'jane@example.com'],
        ['Alice Johnson', 'alice@example.com'],
        ['Bob Brown', 'bob@example.com'],
        ['Charlie White', 'charlie@example.com'],
        ['David Black', 'david@example.com'],
        ['Emma Wilson', 'emma@example.com'],
        ['Frank Green', 'frank@example.com'],
        ['Grace Hall', 'grace@example.com'],
        ['Henry Adams', 'henry@example.com']
    ];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    foreach ($users as $user) {
        [$name, $email] = $user;
        $password = password_hash('password123', PASSWORD_BCRYPT);
        $stmt->bind_param("sss", $name, $email, $password);
        $stmt->execute();
    }

    $stmt->close();
    echo "Users seeded successfully.\n";
}

function seedOrders() {
    global $conn;

    $users = $conn->query("SELECT id FROM users");
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)");

    foreach ($users as $user) {
        $user_id = $user['id'];
        
        for ($i = 0; $i < rand(1, 5); $i++) { // Each user gets 1 to 5 orders
            $total_price = rand(10, 500);
            $status = ['pending', 'completed', 'cancelled'][rand(0, 2)];
            $stmt->bind_param("ids", $user_id, $total_price, $status);
            $stmt->execute();
        }
    }

    $stmt->close();
    echo "Orders seeded successfully.\n";
}

function seedProducts() {
    global $conn;

    $products = [
        ['Laptop', 999.99, 10],
        ['Phone', 499.99, 20],
        ['Headphones', 79.99, 50],
        ['Keyboard', 39.99, 30],
        ['Mouse', 19.99, 40]
    ];

    $stmt = $conn->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
    
    foreach ($products as $product) {
        [$name, $price, $stock] = $product;
        $stmt->bind_param("sdi", $name, $price, $stock);
        $stmt->execute();
    }

    $stmt->close();
    echo "Products seeded successfully.\n";
}

try {
    $conn->begin_transaction();

    seedUsers();
    seedOrders();
    seedProducts();

    $conn->commit();
    echo "Seeding completed successfully.\n";
} catch (Exception $e) {
    $conn->rollback();
    echo "Seeding failed: " . $e->getMessage() . "\n";
}

?>
