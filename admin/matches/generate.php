<?php
require_once '../../config/config.php';
require_once '../../classes/Tournament.php';
require_once '../../classes/Team.php';
require_once '../../classes/Match.php';

requireLogin();

$tournament_id = $_GET['tournament_id'] ?? 0;
$tournament = new Tournament();
$tournament_data = $tournament->getById($tournament_id);

if (!$tournament_data) {
    redirect('/admin/tournaments/');
}

$team = new Team();
$teams = $team->getByTournament($tournament_id);
$groups = $team->getGroups($tournament_id);

$match = new FootballMatch();
$existing_matches = $match->getByTournament($tournament_id, 'group');

$error = '';
$success = '';

if ($_POST && isset($_POST['generate'])) {
    if (empty($groups)) {
        $error = 'No teams found. Please add teams to groups first.';
    } else {
        $match->generateGroupFixtures($tournament_id);
        $success = 'Group stage fixtures generated successfully!';
        header("refresh:2;url=../tournaments/view.php?id=$tournament_id");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Fixtures - <?php echo htmlspecialchars($tournament_data['name']); ?></title>
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
                    <h1 class="h2">Generate Fixtures - <?php echo htmlspecialchars($tournament_data['name']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="../tournaments/view.php?id=<?php echo $tournament_id; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tournament
                            </a>
                        </div>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <br><small>Redirecting to tournament page...</small>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Group Stage Overview</h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($groups)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                                        <h5 class="text-muted">No teams found</h5>
                                        <p class="text-muted">Please add teams to groups before generating fixtures.</p>
                                        <a href="../teams/create.php?tournament_id=<?php echo $tournament_id; ?>" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add Teams
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($groups as $group_name): ?>
                                            <?php $group_teams = $team->getByGroup($tournament_id, $group_name); ?>
                                            <div class="col-md-6 mb-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="m-0">Group <?php echo $group_name; ?></h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php if (empty($group_teams)): ?>
                                                            <p class="text-muted">No teams in this group</p>
                                                        <?php else: ?>
                                                            <ul class="list-unstyled">
                                                                <?php foreach ($group_teams as $team_data): ?>
                                                                    <li class="mb-2">
                                                                        <?php if ($team_data['flag_image']): ?>
                                                                            <img src="../../<?php echo $team_data['flag_image']; ?>" class="team-flag me-2" alt="Flag">
                                                                        <?php endif; ?>
                                                                        <?php echo htmlspecialchars($team_data['name']); ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                            <small class="text-muted">
                                                                <?php 
                                                                $team_count = count($group_teams);
                                                                $matches_per_group = $team_count * ($team_count - 1) / 2;
                                                                echo "$matches_per_group matches will be generated";
                                                                ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if (!empty($existing_matches)): ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Warning:</strong> <?php echo count($existing_matches); ?> group stage matches already exist. 
                                            Generating fixtures again will create duplicate matches.
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="text-center">
                                        <form method="POST" style="display: inline;">
                                            <button type="submit" name="generate" class="btn btn-success btn-lg" 
                                                    <?php echo !empty($existing_matches) ? 'onclick="return confirm(\'This will create duplicate matches. Continue?\')"' : ''; ?>>
                                                <i class="fas fa-calendar-plus"></i> Generate Group Stage Fixtures
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Fixture Generation</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">This will create round-robin fixtures for each group:</p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> Every team plays every other team in their group</li>
                                    <li><i class="fas fa-check text-success"></i> Matches are created with "scheduled" status</li>
                                    <li><i class="fas fa-check text-success"></i> You can edit match dates and times later</li>
                                </ul>
                                <hr>
                                <p class="text-muted"><strong>After generation:</strong></p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-arrow-right text-primary"></i> Set match dates/times</li>
                                    <li><i class="fas fa-arrow-right text-primary"></i> Enter match results</li>
                                    <li><i class="fas fa-arrow-right text-primary"></i> View group standings</li>
                                </ul>
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
