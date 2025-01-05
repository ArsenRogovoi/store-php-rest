<?php

namespace App\Controllers;

use PDO;
use PDOException;

class ProductController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllProducts(): void
    {
        echo json_encode(["message" => "Retrieving all products..."]);
    }

    public function createProduct(array $data): void
    {
        if (!isset($data['name']) || !isset($data['price'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name and price are required.']);
            return;
        }

        $name = $data['name'];
        $description = $data['description'] ?? '';
        $price = $data['price'];

        // validation data
        if (strlen($name) < 2) {
            http_response_code(400);
            echo json_encode(["error" => 'Name must be at least 2 characters long']);
            return;
        }

        if (!is_numeric($price) || $price <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Price must be a positive number']);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO products (name, description, price)
                VALUES (:name, :description, :price)'
            );
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':price' => $price
            ]);

            http_response_code(201); // 201 Created
            echo json_encode(['message' => 'Product created successfully.']);
        } catch (PDOException $e) {
            http_response_code(500); //500 Internal Server Error
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function updateProduct(int $id, array $data): void
    {
        // validation
        if (!isset($data["name"]) || !isset($data['price'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name and price are required.']);
            return;
        }

        $name = $data['name'];
        $description = $data['description'] ?? '';
        $price = $data['price'];

        // validation
        if (strlen($name) < 2) {
            http_response_code(400);
            echo json_encode(['error' => 'Name must be at least 2 characters long']);
            return;
        }

        if (!is_numeric($price) || $price <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Price must be a positive number']);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE products
                SET name = :name, description = :description, price = :price
                WHERE id = :id'
            );
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':id' => $id
            ]);

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
                return;
            }

            http_response_code(200);
            echo json_encode(['message' => 'Product updated seccessfully.']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function deleteProduct(int $id): void
    {
        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM products
                WHERE id = :id'
            );
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found.']);
                return;
            }

            http_response_code(200);
            echo json_encode(['message' => 'Product deleted successfully.']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
