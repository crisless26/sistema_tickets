<?php

class RequestModel {
    private $conn;
    private $table = "requests";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT id, client_id, subject, details, status, created_at, updated_at FROM " . $this->table);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, client_id, subject, details, status, created_at, updated_at FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $status = $data['status'] ?? 'pendiente';

        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (client_id, subject, details, status) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['client_id'],
            $data['subject'],
            $data['details'] ?? null,
            $status
        ]);
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];

        if (isset($data['client_id'])) { $fields[] = "client_id = ?"; $params[] = $data['client_id']; }
        if (isset($data['subject'])) { $fields[] = "subject = ?"; $params[] = $data['subject']; }
        if (array_key_exists('details', $data)) { $fields[] = "details = ?"; $params[] = $data['details']; }
        if (isset($data['status'])) { $fields[] = "status = ?"; $params[] = $data['status']; }

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
