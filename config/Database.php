<?php
class Database
{
    private $host = 'localhost:3306';
    private $db_name = 'ecommerce_group3';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection()
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // 设置字符集为 utf8mb4，支持多语言
        $this->conn->set_charset("utf8mb4");

        return $this->conn;
    }
}
?>