<?php

class Clients {
    private $conn;
    private $table = "clients";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT id, name, company, email, phone, created_by, created_at FROM " . $this->table);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, company, email, phone, created_by, created_at FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (name, company, email, phone, created_by) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['company'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['created_by'] ?? null
        ]);
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];

        if (isset($data['name'])) { $fields[] = "name = ?"; $params[] = $data['name']; }
        if (array_key_exists('company', $data)) { $fields[] = "company = ?"; $params[] = $data['company']; }
        if (array_key_exists('email', $data)) { $fields[] = "email = ?"; $params[] = $data['email']; }
        if (array_key_exists('phone', $data)) { $fields[] = "phone = ?"; $params[] = $data['phone']; }

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
}
