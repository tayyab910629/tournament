<?php
require_once __DIR__ . '/../config/database.php';

class Player {
    private $conn;
    private $table_name = "players";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($team_id, $name, $position, $jersey_number, $age, $photo) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (team_id, name, position, jersey_number, age, photo) 
                  VALUES (:team_id, :name, :position, :jersey_number, :age, :photo)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':jersey_number', $jersey_number);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':photo', $photo);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getByTeam($team_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE team_id = :team_id ORDER BY jersey_number, name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $position, $jersey_number, $age, $photo) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, position = :position, jersey_number = :jersey_number, 
                      age = :age, photo = :photo
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':jersey_number', $jersey_number);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':photo', $photo);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
