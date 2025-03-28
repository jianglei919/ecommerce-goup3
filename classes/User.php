<?php
class User
{
  private $conn;
  private $table_name = "users";

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function register($username, $password, $name, $address, $phone, $email)
  {
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password, name, address, phone, email) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ssssss", $username, $hashed_pw, $name, $address, $phone, $email);
    return $stmt->execute();
  }

  public function login($username, $password)
  {
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
      return $user;
    }
    return false;
  }

  public function getAllUsers()
  {
    $result = $this->conn->query("SELECT * FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
      $users[] = $row;
    }
    return $users;
  }

  public function deleteUser($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }
}