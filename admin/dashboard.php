<?php
require_once '../config/config.php';
require_once '../classes/Tournament.php';
require_once '../classes/Team.php';
require_once '../classes/Match.php';

requireLogin();

$tournament = new Tournament();
$tournaments = $tournament->getAll();

$team = new Team();
$match = new FootballMatch();

$stats = [
    'total_tournaments' => count($tournaments),
    'active_tournaments' => count(array_filter($tournaments, function($t) { return $t['status'] == 'active'; })),
    'total_teams' => 0,
    'total_matches' => 0
];

foreach ($tournaments as $t) {
    $teams = $team->getByTournament($t['id']);
    $matches = $match->getByTournament($t['id']);
    $stats['total_teams'] += count($teams);
    $stats['total_matches'] += count($matches);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Football Tournament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="tournaments/create.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> New Tournament
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Tournaments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_tournaments']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Active Tournaments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['active_tournaments']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-play fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Teams</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_teams']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Total Matches</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_matches']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-futbol fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Tournaments</h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($tournaments)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-trophy fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-muted">No tournaments created yet</p>
                                        <a href="tournaments/create.php" class="btn btn-primary">Create First Tournament</a>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th>Start Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($tournaments, 0, 5) as $t): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($t['name']); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $t['status'] == 'active' ? 'success' : ($t['status'] == 'completed' ? 'secondary' : 'warning'); ?>">
                                                                <?php echo ucfirst($t['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $t['start_date'] ? date('M j, Y', strtotime($t['start_date'])) : 'TBD'; ?></td>
                                                        <td>
                                                            <a href="tournaments/view.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="tournaments/edit.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center">
                                        <a href="tournaments/" class="btn btn-outline-primary">View All Tournaments</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="tournaments/create.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Tournament
                                    </a>
                                    <a href="teams/" class="btn btn-outline-primary">
                                        <i class="fas fa-users"></i> Manage Teams
                                    </a>
                                    <a href="matches/" class="btn btn-outline-primary">
                                        <i class="fas fa-futbol"></i> Manage Matches
                                    </a>
                                    <a href="../" class="btn btn-outline-secondary" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> View Public Site
                                    </a>
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
