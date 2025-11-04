<?php
// models/Country.php

class Country {
    private $conn;
    private $table = 'countries';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a country
    public function create($name) {
        $query = "INSERT INTO " . $this->table . " (name) VALUES (:name)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            
            if ($stmt->execute()) {
                return [
                    'id' => $this->conn->lastInsertId(),
                    'name' => $name
                ];
            }
            return false;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return 'duplicate';
            }
            return false;
        }
    }

    // Read a single country
    public function readOne($id) {
        $query = "SELECT id, name, created_at, updated_at 
                  FROM " . $this->table . " 
                  WHERE id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    // Read all countries
    public function readAll() {
        $query = "SELECT id, name, created_at, updated_at 
                  FROM " . $this->table . " 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Update a country
    public function update($id, $name) {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name 
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return $this->readOne($id);
            }
            return false;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return 'duplicate';
            }
            return false;
        }
    }

    // Delete a country
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    // Verify if a country exists
    public function exists($id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['count'] > 0;
    }

    // Verifiy if all countries exist
    public function allExist($ids) {
        if (empty($ids)) {
            return false;
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT COUNT(DISTINCT id) as count 
                  FROM " . $this->table . " 
                  WHERE id IN ($placeholders)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($ids);
        $row = $stmt->fetch();
        
        return $row['count'] == count($ids);
    }
}
