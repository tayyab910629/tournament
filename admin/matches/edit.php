<?php
require_once '../../config/config.php';
require_once '../../classes/Match.php';

requireLogin();

$match_id = $_GET['id'] ?? 0;
$match = new FootballMatch();
$match_data = $match->getById($match_id);

if (!$match_data) {
    redirect('/admin/matches/');
}

$error = '';
$success = '';

if ($_POST) {
    $home_score = $_POST['home_score'] !== '' ? (int)$_POST['home_score'] : null;
    $away_score = $_POST['away_score'] !== '' ? (int)$_POST['away_score'] : null;
    $home_penalties = $_POST['home_penalties'] !== '' ? (int)$_POST['home_penalties'] : null;
    $away_penalties = $_POST['away_penalties'] !== '' ? (int)$_POST['away_penalties'] : null;
    $match_date = $_POST['match_date'] ?: null;
    
    if ($home_score !== null && $away_score !== null) {
        if ($match->updateResult($match_id, $home_score, $away_score, $home_penalties, $away_penalties)) {
            $success = 'Match result updated successfully!';
            $match_data = $match->getById($match_id);
        } else {
            $error = 'Failed to update match result';
        }
    } else {
        $error = 'Both team scores are required';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Match - Football Tournament Admin</title>
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
                    <h1 class="h2">Edit Match</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="index.php?tournament_id=<?php echo $match_data['tournament_id']; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Matches
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Match Details</h6>
                            </div>
                            <div class="card-body">
                                <?php if ($error): ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($success): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="row mb-4">
                                    <div class="col-5 text-center">
                                        <?php if ($match_data['home_flag']): ?>
                                            <img src="../../<?php echo $match_data['home_flag']; ?>" class="team-flag mb-2" alt="Flag" style="width: 48px; height: 32px;">
                                        <?php endif; ?>
                                        <h4><?php echo htmlspecialchars($match_data['home_team_name']); ?></h4>
                                    </div>
                                    <div class="col-2 text-center">
                                        <div class="text-muted">vs</div>
                                    </div>
                                    <div class="col-5 text-center">
                                        <?php if ($match_data['away_flag']): ?>
                                            <img src="../../<?php echo $match_data['away_flag']; ?>" class="team-flag mb-2" alt="Flag" style="width: 48px; height: 32px;">
                                        <?php endif; ?>
                                        <h4><?php echo htmlspecialchars($match_data['away_team_name']); ?></h4>
                                    </div>
                                </div>

                                <form method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="match_date" class="form-label">Match Date & Time</label>
                                            <input type="datetime-local" class="form-control" id="match_date" name="match_date" 
                                                   value="<?php echo $match_data['match_date'] ? date('Y-m-d\TH:i', strtotime($match_data['match_date'])) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Stage</label>
                                            <input type="text" class="form-control" value="<?php echo ucfirst(str_replace('_', ' ', $match_data['stage'])); ?>" readonly>
                                        </div>
                                    </div>

                                    <h6 class="mb-3">Match Result</h6>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="home_score" class="form-label"><?php echo htmlspecialchars($match_data['home_team_name']); ?> Score</label>
                                            <input type="number" class="form-control" id="home_score" name="home_score" 
                                                   value="<?php echo $match_data['home_score'] !== null ? $match_data['home_score'] : ''; ?>" min="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="away_score" class="form-label"><?php echo htmlspecialchars($match_data['away_team_name']); ?> Score</label>
                                            <input type="number" class="form-control" id="away_score" name="away_score" 
                                                   value="<?php echo $match_data['away_score'] !== null ? $match_data['away_score'] : ''; ?>" min="0">
                                        </div>
                                    </div>

                                    <h6 class="mb-3">Penalties (if applicable)</h6>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="home_penalties" class="form-label"><?php echo htmlspecialchars($match_data['home_team_name']); ?> Penalties</label>
                                            <input type="number" class="form-control" id="home_penalties" name="home_penalties" 
                                                   value="<?php echo $match_data['home_penalties'] !== null ? $match_data['home_penalties'] : ''; ?>" min="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="away_penalties" class="form-label"><?php echo htmlspecialchars($match_data['away_team_name']); ?> Penalties</label>
                                            <input type="number" class="form-control" id="away_penalties" name="away_penalties" 
                                                   value="<?php echo $match_data['away_penalties'] !== null ? $match_data['away_penalties'] : ''; ?>" min="0">
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="index.php?tournament_id=<?php echo $match_data['tournament_id']; ?>" class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Match
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Match Info</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Stage:</strong> <?php echo ucfirst(str_replace('_', ' ', $match_data['stage'])); ?></p>
                                <?php if ($match_data['group_name']): ?>
                                    <p><strong>Group:</strong> <?php echo $match_data['group_name']; ?></p>
                                <?php endif; ?>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php echo $match_data['status'] == 'completed' ? 'success' : ($match_data['status'] == 'live' ? 'warning' : 'secondary'); ?>">
                                        <?php echo ucfirst($match_data['status']); ?>
                                    </span>
                                </p>
                                
                                <?php if ($match_data['status'] == 'completed'): ?>
                                    <hr>
                                    <h6>Current Result</h6>
                                    <div class="text-center">
                                        <div class="h4">
                                            <?php echo $match_data['home_score']; ?> - <?php echo $match_data['away_score']; ?>
                                        </div>
                                        <?php if ($match_data['home_penalties'] !== null): ?>
                                            <small class="text-muted">
                                                (<?php echo $match_data['home_penalties']; ?> - <?php echo $match_data['away_penalties']; ?> pens)
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
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
