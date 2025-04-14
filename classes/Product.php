<?php
class Product
{
  private $conn;
  private $table_name = "products";

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function getAllProducts($limit = 6, $offset = 0)
  {
    $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
      $products[] = $row;
    }
    return $products;
  }

  public function getProduct($id)
  {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
  }

  public function createProduct($name, $short_description, $long_description, $price, $photo)
  {
    $query = "INSERT INTO " . $this->table_name . " (name, short_description, long_description, price, photo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("sssds", $name, $short_description, $long_description, $price, $photo);
    return $stmt->execute();
    
  }

  public function updateProduct($id, $name, $short_description, $long_description, $price, $photo = null)
  {
    if ($photo) {
      $query = "UPDATE " . $this->table_name . " SET name = ?, short_description = ?, long_description = ?, price = ?, photo = ? WHERE id = ?";
      $stmt = $this->conn->prepare($query);
      $stmt->bind_param("sssssi", $name, $short_description, $long_description, $price, $photo, $id);
      return $stmt->execute();
    } else {
      $query = "UPDATE " . $this->table_name . " SET name = ?, short_description = ?, long_description = ?, price = ? WHERE id = ?";
      $stmt = $this->conn->prepare($query);
      $stmt->bind_param("ssdi", $name, $short_description,$long_description, $price, $id);
      return $stmt->execute();
    }
  }

  public function deleteProduct($id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }

  public function searchProducts($keyword, $limit = 6, $offset = 0)
  {
    $keyword = "%" . $keyword . "%";
    $query = "SELECT * FROM " . $this->table_name . " WHERE name LIKE ? OR short_description LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ssii", $keyword, $keyword, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
      $products[] = $row;
    }
    return $products;
  }

  public function countAllProducts()
  {
    $query = "SELECT COUNT(*) AS total FROM " . $this->table_name;
    $result = $this->conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
  }

  public function countSearchResults($keyword)
  {
    $keyword = "%" . $keyword . "%";
    $query = "SELECT COUNT(*) AS total FROM " . $this->table_name . " WHERE name LIKE ? OR short_description LIKE ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
  }
}