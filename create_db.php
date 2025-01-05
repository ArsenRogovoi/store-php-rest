<?php

try {
    $pdo = new PDO('sqlite: products.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection to the database was successful.<br>";
} catch (PDOException $e) {
    echo "Database connection error:" . $e->getMessage();
    exit;
}

$sql = "CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL
)";

try {
    $pdo->exec($sql);
    echo "Table 'products' has been successfuly created or already exists.<br>";
} catch (PDOException $e) {
    echo "Error occured in try to create table: " . $e->getMessage();
}

echo "Script DB initialization was successfuly executed";

// exec() method used in non select queries(CREATE TABLE, ISERT INTO, 
// UPDATE, DELETE).
// exec() return number of affected rows or 0 if no affected rows or false 
// if error occured.