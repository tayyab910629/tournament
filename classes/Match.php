<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Team.php';
require_once __DIR__ . '/GroupStandings.php';

class FootballMatch {
    private $conn;
    private $table_name = "matches";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($tournament_id, $home_team_id, $away_team_id, $match_date, $stage, $group_name = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (tournament_id, home_team_id, away_team_id, match_date, stage, group_name) 
                  VALUES (:tournament_id, :home_team_id, :away_team_id, :match_date, :stage, :group_name)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':home_team_id', $home_team_id);
        $stmt->bindParam(':away_team_id', $away_team_id);
        $stmt->bindParam(':match_date', $match_date);
        $stmt->bindParam(':stage', $stage);
        $stmt->bindParam(':group_name', $group_name);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function generateGroupFixtures($tournament_id) {
        $team = new Team();
        $groups = $team->getGroups($tournament_id);
        
        foreach ($groups as $group_name) {
            $teams = $team->getByGroup($tournament_id, $group_name);
            $team_count = count($teams);
            
            for ($i = 0; $i < $team_count; $i++) {
                for ($j = $i + 1; $j < $team_count; $j++) {
                    $this->create(
                        $tournament_id,
                        $teams[$i]['id'],
                        $teams[$j]['id'],
                        null,
                        'group',
                        $group_name
                    );
                }
            }
        }
        return true;
    }

    public function getByTournament($tournament_id, $stage = null) {
        $query = "SELECT m.*, 
                         ht.name as home_team_name, ht.flag_image as home_flag,
                         at.name as away_team_name, at.flag_image as away_flag
                  FROM " . $this->table_name . " m
                  JOIN teams ht ON m.home_team_id = ht.id
                  JOIN teams at ON m.away_team_id = at.id
                  WHERE m.tournament_id = :tournament_id";
        
        if ($stage) {
            $query .= " AND m.stage = :stage";
        }
        
        $query .= " ORDER BY m.match_date, m.group_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        if ($stage) {
            $stmt->bindParam(':stage', $stage);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateResult($id, $home_score, $away_score, $home_penalties = null, $away_penalties = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET home_score = :home_score, away_score = :away_score, 
                      home_penalties = :home_penalties, away_penalties = :away_penalties,
                      status = 'completed'
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':home_score', $home_score);
        $stmt->bindParam(':away_score', $away_score);
        $stmt->bindParam(':home_penalties', $home_penalties);
        $stmt->bindParam(':away_penalties', $away_penalties);
        
        if ($stmt->execute()) {
            $this->updateGroupStandings($id);
            return true;
        }
        return false;
    }

    private function updateGroupStandings($match_id) {
        $query = "SELECT m.*, m.tournament_id, m.group_name, m.home_team_id, m.away_team_id,
                         m.home_score, m.away_score
                  FROM " . $this->table_name . " m
                  WHERE m.id = :match_id AND m.stage = 'group'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':match_id', $match_id);
        $stmt->execute();
        $match = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$match || $match['home_score'] === null) return;
        
        $standings = new GroupStandings();
        $standings->updateFromMatch($match);
    }

    public function getById($id) {
        $query = "SELECT m.*, 
                         ht.name as home_team_name, ht.flag_image as home_flag,
                         at.name as away_team_name, at.flag_image as away_flag
                  FROM " . $this->table_name . " m
                  JOIN teams ht ON m.home_team_id = ht.id
                  JOIN teams at ON m.away_team_id = at.id
                  WHERE m.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>
