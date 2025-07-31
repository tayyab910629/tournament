<?php
require_once '../../config/config.php';
require_once '../../classes/Match.php';
require_once '../../classes/Tournament.php';

requireLogin();

$tournament_id = $_GET['tournament_id'] ?? 0;
$tournament = new Tournament();
$tournaments = $tournament->getAll();

$selected_tournament = null;
if ($tournament_id) {
    $selected_tournament = $tournament->getById($tournament_id);
}

$match = new FootballMatch();
$matches = [];
if ($tournament_id) {
    $matches = $match->getByTournament($tournament_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches - Football Tournament Admin</title>
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
                    <h1 class="h2">Matches</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <?php if ($tournament_id): ?>
                                <a href="generate.php?tournament_id=<?php echo $tournament_id; ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-calendar-plus"></i> Generate Fixtures
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body">
                                <label for="tournament_select" class="form-label">Select Tournament</label>
                                <select class="form-select" id="tournament_select" onchange="window.location.href='?tournament_id='+this.value">
                                    <option value="">Choose a tournament...</option>
                                    <?php foreach ($tournaments as $t): ?>
                                        <option value="<?php echo $t['id']; ?>" <?php echo $tournament_id == $t['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($t['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($tournament_id && $selected_tournament): ?>
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Matches - <?php echo htmlspecialchars($selected_tournament['name']); ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($matches)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-futbol fa-3x text-gray-300 mb-3"></i>
                                    <h5 class="text-muted">No matches found</h5>
                                    <p class="text-muted">Generate fixtures to create matches for this tournament.</p>
                                    <a href="generate.php?tournament_id=<?php echo $tournament_id; ?>" class="btn btn-success">
                                        <i class="fas fa-calendar-plus"></i> Generate Fixtures
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php 
                                $grouped_matches = [];
                                foreach ($matches as $match_data) {
                                    $stage = $match_data['stage'];
                                    $group = $match_data['group_name'] ?: 'knockout';
                                    if (!isset($grouped_matches[$stage])) {
                                        $grouped_matches[$stage] = [];
                                    }
                                    if (!isset($grouped_matches[$stage][$group])) {
                                        $grouped_matches[$stage][$group] = [];
                                    }
                                    $grouped_matches[$stage][$group][] = $match_data;
                                }
                                ?>
                                
                                <?php foreach ($grouped_matches as $stage => $stage_matches): ?>
                                    <h5 class="mt-4 mb-3"><?php echo ucfirst(str_replace('_', ' ', $stage)); ?> Stage</h5>
                                    
                                    <?php if ($stage == 'group'): ?>
                                        <?php foreach ($stage_matches as $group_name => $group_matches): ?>
                                            <h6 class="text-muted">Group <?php echo $group_name; ?></h6>
                                            <div class="row mb-4">
                                                <?php foreach ($group_matches as $match_data): ?>
                                                    <div class="col-lg-6 mb-3">
                                                        <div class="card match-card <?php echo $match_data['status']; ?>">
                                                            <div class="card-body">
                                                                <div class="row align-items-center">
                                                                    <div class="col-4 text-center">
                                                                        <?php if ($match_data['home_flag']): ?>
                                                                            <img src="../../<?php echo $match_data['home_flag']; ?>" class="team-flag mb-1" alt="Flag">
                                                                        <?php endif; ?>
                                                                        <div class="fw-bold"><?php echo htmlspecialchars($match_data['home_team_name']); ?></div>
                                                                    </div>
                                                                    <div class="col-4 text-center">
                                                                        <?php if ($match_data['status'] == 'completed'): ?>
                                                                            <div class="h4 mb-0">
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
                                                                                <?php echo ucfirst($match_data['status']); ?>
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="col-4 text-center">
                                                                        <?php if ($match_data['away_flag']): ?>
                                                                            <img src="../../<?php echo $match_data['away_flag']; ?>" class="team-flag mb-1" alt="Flag">
                                                                        <?php endif; ?>
                                                                        <div class="fw-bold"><?php echo htmlspecialchars($match_data['away_team_name']); ?></div>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-2">
                                                                    <div class="col text-center">
                                                                        <?php if ($match_data['match_date']): ?>
                                                                            <small class="text-muted">
                                                                                <?php echo date('M j, Y H:i', strtotime($match_data['match_date'])); ?>
                                                                            </small>
                                                                        <?php endif; ?>
                                                                        <div class="mt-2">
                                                                            <a href="edit.php?id=<?php echo $match_data['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                                <i class="fas fa-edit"></i> Edit
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="row mb-4">
                                            <?php foreach ($stage_matches['knockout'] as $match_data): ?>
                                                <div class="col-lg-6 mb-3">
                                                    <div class="card match-card <?php echo $match_data['status']; ?>">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-4 text-center">
                                                                    <?php if ($match_data['home_flag']): ?>
                                                                        <img src="../../<?php echo $match_data['home_flag']; ?>" class="team-flag mb-1" alt="Flag">
                                                                    <?php endif; ?>
                                                                    <div class="fw-bold"><?php echo htmlspecialchars($match_data['home_team_name']); ?></div>
                                                                </div>
                                                                <div class="col-4 text-center">
                                                                    <?php if ($match_data['status'] == 'completed'): ?>
                                                                        <div class="h4 mb-0">
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
                                                                            <?php echo ucfirst($match_data['status']); ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="col-4 text-center">
                                                                    <?php if ($match_data['away_flag']): ?>
                                                                        <img src="../../<?php echo $match_data['away_flag']; ?>" class="team-flag mb-1" alt="Flag">
                                                                    <?php endif; ?>
                                                                    <div class="fw-bold"><?php echo htmlspecialchars($match_data['away_team_name']); ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col text-center">
                                                                    <?php if ($match_data['match_date']): ?>
                                                                        <small class="text-muted">
                                                                            <?php echo date('M j, Y H:i', strtotime($match_data['match_date'])); ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                    <div class="mt-2">
                                                                        <a href="edit.php?id=<?php echo $match_data['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                            <i class="fas fa-edit"></i> Edit
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
