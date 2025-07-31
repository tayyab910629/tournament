<?php
require_once '../../config/config.php';
require_once '../../classes/Tournament.php';
require_once '../../classes/Team.php';
require_once '../../classes/Match.php';
require_once '../../classes/GroupStandings.php';

requireLogin();

$tournament_id = $_GET['id'] ?? 0;

$tournament = new Tournament();
$tournament_data = $tournament->getById($tournament_id);

if (!$tournament_data) {
    redirect('/admin/tournaments/');
}

$team = new Team();
$teams = $team->getByTournament($tournament_id);
$groups = $team->getGroups($tournament_id);

$match = new FootballMatch();
$matches = $match->getByTournament($tournament_id);

$standings = new GroupStandings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tournament_data['name']); ?> - Tournament Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo htmlspecialchars($tournament_data['name']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="edit.php?id=<?php echo $tournament_id; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="../teams/create.php?tournament_id=<?php echo $tournament_id; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Add Team
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Description</h6>
                                        <p><?php echo $tournament_data['description'] ? htmlspecialchars($tournament_data['description']) : 'No description'; ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="text-muted">Start Date</h6>
                                        <p><?php echo $tournament_data['start_date'] ? date('M j, Y', strtotime($tournament_data['start_date'])) : 'TBD'; ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="text-muted">End Date</h6>
                                        <p><?php echo $tournament_data['end_date'] ? date('M j, Y', strtotime($tournament_data['end_date'])) : 'TBD'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Status</h6>
                                <span class="badge bg-<?php echo $tournament_data['status'] == 'active' ? 'success' : ($tournament_data['status'] == 'completed' ? 'secondary' : 'warning'); ?> fs-6">
                                    <?php echo ucfirst($tournament_data['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Teams</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($teams); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Groups</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($groups); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Matches</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($matches); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-futbol fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Completed</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo count(array_filter($matches, function($m) { return $m['status'] == 'completed'; })); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($groups)): ?>
                    <div class="row">
                        <?php foreach ($groups as $group_name): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Group <?php echo $group_name; ?> Standings</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $group_standings = $standings->getByGroup($tournament_id, $group_name); ?>
                                        <?php if (!empty($group_standings)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
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
                                                        <?php foreach ($group_standings as $standing): ?>
                                                            <tr>
                                                                <td>
                                                                    <?php if ($standing['flag_image']): ?>
                                                                        <img src="../../<?php echo $standing['flag_image']; ?>" class="team-flag me-2" alt="Flag">
                                                                    <?php endif; ?>
                                                                    <?php echo htmlspecialchars($standing['team_name']); ?>
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
                                            <p class="text-muted">No matches played yet</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <a href="../teams/create.php?tournament_id=<?php echo $tournament_id; ?>" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-users"></i> Add Teams
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="../matches/generate.php?tournament_id=<?php echo $tournament_id; ?>" class="btn btn-outline-success w-100">
                                            <i class="fas fa-calendar"></i> Generate Fixtures
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="../matches/?tournament_id=<?php echo $tournament_id; ?>" class="btn btn-outline-info w-100">
                                            <i class="fas fa-futbol"></i> Manage Matches
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="../../?tournament=<?php echo $tournament_id; ?>" class="btn btn-outline-secondary w-100" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> View Public
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
