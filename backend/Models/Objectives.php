<?php

class Objectives {
    private $conn;
    private $table = "objectives";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT id, user_id, client_id, title, description, progress_percentage, status, created_at, updated_at FROM " . $this->table);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, user_id, client_id, title, description, progress_percentage, status, created_at, updated_at FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $progress = $data['progress_percentage'] ?? 0;
        $status = $data['status'] ?? 'en_curso';

        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (user_id, client_id, title, description, progress_percentage, status) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['user_id'],
            $data['client_id'],
            $data['title'],
            $data['description'] ?? null,
            $progress,
            $status
        ]);
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];

        if (isset($data['user_id'])) { $fields[] = "user_id = ?"; $params[] = $data['user_id']; }
        if (isset($data['client_id'])) { $fields[] = "client_id = ?"; $params[] = $data['client_id']; }
        if (isset($data['title'])) { $fields[] = "title = ?"; $params[] = $data['title']; }
        if (array_key_exists('description', $data)) { $fields[] = "description = ?"; $params[] = $data['description']; }
        if (isset($data['progress_percentage'])) { $fields[] = "progress_percentage = ?"; $params[] = $data['progress_percentage']; }
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
