<?php
require_once '../../config/config.php';
require_once '../../classes/Tournament.php';

requireLogin();

$error = '';
$success = '';

if ($_POST) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    
    error_log("DEBUG: start_date received: " . var_export($start_date, true));
    error_log("DEBUG: end_date received: " . var_export($end_date, true));
    
    if (empty($name)) {
        $error = 'Tournament name is required';
    } elseif ($start_date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
        $error = 'Invalid start date format';
    } elseif ($end_date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        $error = 'Invalid end date format';
    } elseif ($start_date && $end_date && $start_date > $end_date) {
        $error = 'End date must be after start date';
    } else {
        $tournament = new Tournament();
        $tournament_id = $tournament->create($name, $description, $start_date, $end_date);
        
        if ($tournament_id) {
            $success = 'Tournament created successfully!';
            header("refresh:2;url=view.php?id=$tournament_id");
        } else {
            $error = 'Failed to create tournament';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Tournament - Football Tournament Admin</title>
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
                    <h1 class="h2">Create Tournament</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="index.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tournaments
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Tournament Details</h6>
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
                                        <br><small>Redirecting to tournament page...</small>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tournament Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                                               placeholder="e.g., EURO 2024" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" 
                                                  placeholder="Brief description of the tournament"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                                       value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                                       value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Create Tournament
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Next Steps</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">After creating the tournament, you can:</p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-users text-primary"></i> Add teams to groups</li>
                                    <li><i class="fas fa-user text-primary"></i> Add players to teams</li>
                                    <li><i class="fas fa-calendar text-primary"></i> Generate fixtures</li>
                                    <li><i class="fas fa-futbol text-primary"></i> Enter match results</li>
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
