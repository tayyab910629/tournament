<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Team.php';

class GroupStandings {
    private $conn;
    private $table_name = "group_standings";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function updateFromMatch($match) {
        $tournament_id = $match['tournament_id'];
        $group_name = $match['group_name'];
        $home_team_id = $match['home_team_id'];
        $away_team_id = $match['away_team_id'];
        $home_score = $match['home_score'];
        $away_score = $match['away_score'];

        $this->ensureTeamStanding($tournament_id, $home_team_id, $group_name);
        $this->ensureTeamStanding($tournament_id, $away_team_id, $group_name);

        $home_wins = $home_score > $away_score ? 1 : 0;
        $away_wins = $away_score > $home_score ? 1 : 0;
        $draws = $home_score == $away_score ? 1 : 0;

        $query = "UPDATE " . $this->table_name . " 
                  SET played = played + 1,
                      wins = wins + :wins,
                      draws = draws + :draws,
                      losses = losses + :losses,
                      goals_for = goals_for + :goals_for,
                      goals_against = goals_against + :goals_against,
                      goal_difference = goals_for - goals_against,
                      points = (wins * 3) + draws
                  WHERE tournament_id = :tournament_id AND team_id = :team_id AND group_name = :group_name";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':team_id', $home_team_id);
        $stmt->bindParam(':group_name', $group_name);
        $stmt->bindParam(':wins', $home_wins);
        $stmt->bindParam(':draws', $draws);
        $stmt->bindParam(':losses', $away_wins);
        $stmt->bindParam(':goals_for', $home_score);
        $stmt->bindParam(':goals_against', $away_score);
        $stmt->execute();

        $stmt->bindParam(':team_id', $away_team_id);
        $stmt->bindParam(':wins', $away_wins);
        $stmt->bindParam(':losses', $home_wins);
        $stmt->bindParam(':goals_for', $away_score);
        $stmt->bindParam(':goals_against', $home_score);
        $stmt->execute();
    }

    private function ensureTeamStanding($tournament_id, $team_id, $group_name) {
        $query = "INSERT IGNORE INTO " . $this->table_name . " 
                  (tournament_id, team_id, group_name) 
                  VALUES (:tournament_id, :team_id, :group_name)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':group_name', $group_name);
        $stmt->execute();
    }

    public function getByGroup($tournament_id, $group_name) {
        $query = "SELECT gs.*, t.name as team_name, t.flag_image
                  FROM " . $this->table_name . " gs
                  JOIN teams t ON gs.team_id = t.id
                  WHERE gs.tournament_id = :tournament_id AND gs.group_name = :group_name
                  ORDER BY gs.points DESC, gs.goal_difference DESC, gs.goals_for DESC, t.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':group_name', $group_name);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopTeamsFromGroups($tournament_id, $teams_per_group = 2) {
        $team = new Team();
        $groups = $team->getGroups($tournament_id);
        $qualified_teams = [];

        foreach ($groups as $group_name) {
            $standings = $this->getByGroup($tournament_id, $group_name);
            $qualified_teams = array_merge($qualified_teams, array_slice($standings, 0, $teams_per_group));
        }

        return $qualified_teams;
    }
}

?>
