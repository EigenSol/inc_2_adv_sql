<?php

require_once __DIR__ . '/../util/conn.php';

function createUsersTable() {
  global $conn;
  $sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )";

  if (!$conn->query($sql)) {
    throw new Exception("Error creating users table: " . $conn->error);
  }
}

function createOrdersTable() {
  global $conn;
  $sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  )";

  if (!$conn->query($sql)) {
    throw new Exception("Error creating orders table: " . $conn->error);
  }
}

function createProductsTable() {
  global $conn;
  $sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )";

  if (!$conn->query($sql)) {
    throw new Exception("Error creating products table: " . $conn->error);
  }
}

function rollbackTables() {
  global $conn;
  $conn->query("DROP TABLE IF EXISTS orders");
  $conn->query("DROP TABLE IF EXISTS users");
  $conn->query("DROP TABLE IF EXISTS products");
}

try {
  $conn->begin_transaction();

  createUsersTable();
  createOrdersTable();
  createProductsTable();

  $conn->commit();
  echo "Done.";
} catch (Exception $e) {
  $conn->rollback();
  rollbackTables();
  die("Error: " . $e->getMessage());
}

?>
