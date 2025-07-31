<?php
require_once 'config/config.php';
require_once 'classes/Tournament.php';
require_once 'classes/Team.php';
require_once 'classes/Match.php';
require_once 'classes/GroupStandings.php';

$tournament = new Tournament();
$tournaments = $tournament->getAll();

$selected_tournament_id = $_GET['tournament'] ?? (count($tournaments) > 0 ? $tournaments[0]['id'] : 0);
$selected_tournament = null;
$teams = [];
$groups = [];
$matches = [];
$group_standings = [];

if ($selected_tournament_id) {
    $selected_tournament = $tournament->getById($selected_tournament_id);
    if ($selected_tournament) {
        $team = new Team();
        $teams = $team->getByTournament($selected_tournament_id);
        $groups = $team->getGroups($selected_tournament_id);
        
        $match = new FootballMatch();
        $matches = $match->getByTournament($selected_tournament_id);
        
        $standings = new GroupStandings();
        foreach ($groups as $group_name) {
            $group_standings[$group_name] = $standings->getByGroup($selected_tournament_id, $group_name);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $selected_tournament ? htmlspecialchars($selected_tournament['name']) : 'Football Tournament'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/public.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-futbol"></i> Football Tournament
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#overview">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#groups">Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#matches">Matches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#bracket">Knockout</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="tournamentDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo $selected_tournament ? htmlspecialchars($selected_tournament['name']) : 'Select Tournament'; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($tournaments as $t): ?>
                                <li>
                                    <a class="dropdown-item <?php echo $t['id'] == $selected_tournament_id ? 'active' : ''; ?>" 
                                       href="?tournament=<?php echo $t['id']; ?>">
                                        <?php echo htmlspecialchars($t['name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (!$selected_tournament): ?>
        <div class="container mt-5">
            <div class="text-center">
                <i class="fas fa-trophy fa-4x text-muted mb-4"></i>
                <h2 class="text-muted">No tournaments available</h2>
                <p class="text-muted">Please check back later or contact the administrator.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="hero-section bg-primary text-white py-5">
            <div class="container text-center">
                <h1 class="display-4 mb-3"><?php echo htmlspecialchars($selected_tournament['name']); ?></h1>
                <?php if ($selected_tournament['description']): ?>
                    <p class="lead"><?php echo htmlspecialchars($selected_tournament['description']); ?></p>
                <?php endif; ?>
                <div class="row justify-content-center mt-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count($teams); ?></div>
                            <div class="stat-label">Teams</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count($groups); ?></div>
                            <div class="stat-label">Groups</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count($matches); ?></div>
                            <div class="stat-label">Matches</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count(array_filter($matches, function($m) { return $m['status'] == 'completed'; })); ?></div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container my-5">
            <section id="groups" class="mb-5">
                <h2 class="section-title">Group Standings</h2>
                <?php if (empty($groups)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No groups available yet</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($groups as $group_name): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card group-card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Group <?php echo $group_name; ?></h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <?php if (isset($group_standings[$group_name]) && !empty($group_standings[$group_name])): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Team</th>
                                                            <th>P</th>
                                                            <th>W</th>
                                                            <th>D</th>
                                                            <th>L</th>
                                                            <th>GF</th>
                                                            <th>GA</th>
                                                            <th>GD</th>
                                                            <th>Pts</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($group_standings[$group_name] as $index => $standing): ?>
                                                            <tr class="<?php echo $index < 2 ? 'table-success' : ''; ?>">
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="position-badge me-2"><?php echo $index + 1; ?></span>
                                                                        <?php if ($standing['flag_image']): ?>
                                                                            <img src="<?php echo $standing['flag_image']; ?>" class="team-flag me-2" alt="Flag">
                                                                        <?php endif; ?>
                                                                        <span class="fw-bold"><?php echo htmlspecialchars($standing['team_name']); ?></span>
                                                                    </div>
                                                                </td>
                                                                <td><?php echo $standing['played']; ?></td>
                                                                <td><?php echo $standing['wins']; ?></td>
                                                                <td><?php echo $standing['draws']; ?></td>
                                                                <td><?php echo $standing['losses']; ?></td>
                                                                <td><?php echo $standing['goals_for']; ?></td>
                                                                <td><?php echo $standing['goals_against']; ?></td>
                                                                <td><?php echo $standing['goal_difference']; ?></td>
                                                                <td><strong><?php echo $standing['points']; ?></strong></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="p-3 text-center text-muted">
                                                No matches played yet
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section id="matches" class="mb-5">
                <h2 class="section-title">Recent Matches</h2>
                <?php if (empty($matches)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-futbol fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No matches scheduled yet</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $recent_matches = array_slice(array_filter($matches, function($m) { 
                        return $m['status'] == 'completed' || $m['status'] == 'live'; 
                    }), -6);
                    ?>
                    <div class="row">
                        <?php foreach ($recent_matches as $match_data): ?>
                            <div class="col-lg-6 mb-3">
                                <div class="card match-card <?php echo $match_data['status']; ?>">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-4 text-center">
                                                <?php if ($match_data['home_flag']): ?>
                                                    <img src="<?php echo $match_data['home_flag']; ?>" class="team-flag mb-2" alt="Flag">
                                                <?php endif; ?>
                                                <div class="fw-bold"><?php echo htmlspecialchars($match_data['home_team_name']); ?></div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <?php if ($match_data['status'] == 'completed'): ?>
                                                    <div class="score">
                                                        <?php echo $match_data['home_score']; ?> - <?php echo $match_data['away_score']; ?>
                                                    </div>
                                                    <?php if ($match_data['home_penalties'] !== null): ?>
                                                        <small class="text-muted">
                                                            (<?php echo $match_data['home_penalties']; ?> - <?php echo $match_data['away_penalties']; ?> pens)
                                                        </small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <div class="text-muted">vs</div>
                                                    <span class="badge bg-<?php echo $match_data['status'] == 'live' ? 'success' : 'warning'; ?>">
                                                        <?php echo $match_data['status'] == 'live' ? 'LIVE' : 'Scheduled'; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-4 text-center">
                                                <?php if ($match_data['away_flag']): ?>
                                                    <img src="<?php echo $match_data['away_flag']; ?>" class="team-flag mb-2" alt="Flag">
                                                <?php endif; ?>
                                                <div class="fw-bold"><?php echo htmlspecialchars($match_data['away_team_name']); ?></div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-2">
                                            <?php if ($match_data['group_name']): ?>
                                                <small class="text-muted">Group <?php echo $match_data['group_name']; ?></small>
                                            <?php else: ?>
                                                <small class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $match_data['stage'])); ?></small>
                                            <?php endif; ?>
                                            <?php if ($match_data['match_date']): ?>
                                                <br><small class="text-muted"><?php echo date('M j, Y H:i', strtotime($match_data['match_date'])); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section id="bracket" class="mb-5">
                <h2 class="section-title">Knockout Bracket</h2>
                <div class="text-center py-4">
                    <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Knockout bracket will be available after group stage completion</p>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 Football Tournament. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
