<?php
class User {
    protected $conn; // Change to protected
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($name, $contact, $password, $role) {
        $query = "INSERT INTO " . $this->table_name . " (name, contact, password, role) VALUES (:name, :contact, :password, :role)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindParam(':role', $role);
        
        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT id, password, role FROM " . $this->table_name . " WHERE name = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                return $user; // Return user data on success
            }
        }
        return null; // Invalid credentials
    }
}
?>
