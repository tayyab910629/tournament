<?php
require_once __DIR__ . '/../config/database.php';

class Team {
    private $conn;
    private $table_name = "teams";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($tournament_id, $name, $country, $flag_image, $group_name) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (tournament_id, name, country, flag_image, group_name) 
                  VALUES (:tournament_id, :name, :country, :flag_image, :group_name)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':flag_image', $flag_image);
        $stmt->bindParam(':group_name', $group_name);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getByTournament($tournament_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE tournament_id = :tournament_id ORDER BY group_name, name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByGroup($tournament_id, $group_name) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE tournament_id = :tournament_id AND group_name = :group_name 
                  ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':group_name', $group_name);
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

    public function update($id, $name, $country, $flag_image, $group_name) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, country = :country, flag_image = :flag_image, group_name = :group_name
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':flag_image', $flag_image);
        $stmt->bindParam(':group_name', $group_name);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getGroups($tournament_id) {
        $query = "SELECT DISTINCT group_name FROM " . $this->table_name . " 
                  WHERE tournament_id = :tournament_id AND group_name IS NOT NULL 
                  ORDER BY group_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
