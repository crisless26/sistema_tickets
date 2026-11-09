<?php

class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT id, name, email, role, is_active, created_at FROM " . $this->table);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, email, role, is_active, created_at FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($data) {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $role = $data['role'] ?? 'user';

        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (name, email, password, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['email'], $hash, $role]);
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];

        if (isset($data['name'])) { $fields[] = "name = ?"; $params[] = $data['name']; }
        if (isset($data['email'])) { $fields[] = "email = ?"; $params[] = $data['email']; }
        if (isset($data['role'])) { $fields[] = "role = ?"; $params[] = $data['role']; }
        if (isset($data['is_active'])) { $fields[] = "is_active = ?"; $params[] = $data['is_active']; }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function createRefreshToken($userId, $tokenHash, $expiresAt) {
        $stmt = $this->conn->prepare("INSERT INTO refresh_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $tokenHash, $expiresAt]);
    }
}
