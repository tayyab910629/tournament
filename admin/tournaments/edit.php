<?php
require_once '../../config/config.php';
require_once '../../classes/Tournament.php';

requireLogin();

$tournament_id = $_GET['id'] ?? 0;
$tournament = new Tournament();
$tournament_data = $tournament->getById($tournament_id);

if (!$tournament_data) {
    redirect('/admin/tournaments/');
}

$error = '';
$success = '';

if ($_POST) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $start_date = $_POST['start_date'] ?: null;
    $end_date = $_POST['end_date'] ?: null;
    $status = $_POST['status'];
    
    if (empty($name)) {
        $error = 'Tournament name is required';
    } else {
        if ($tournament->update($tournament_id, $name, $description, $start_date, $end_date, $status)) {
            $success = 'Tournament updated successfully!';
            $tournament_data = $tournament->getById($tournament_id);
        } else {
            $error = 'Failed to update tournament';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tournament - Football Tournament Admin</title>
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
                    <h1 class="h2">Edit Tournament</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="view.php?id=<?php echo $tournament_id; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tournament
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
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tournament Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($tournament_data['name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($tournament_data['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                                       value="<?php echo $tournament_data['start_date']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                                       value="<?php echo $tournament_data['end_date']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="upcoming" <?php echo $tournament_data['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                            <option value="active" <?php echo $tournament_data['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="completed" <?php echo $tournament_data['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="view.php?id=<?php echo $tournament_id; ?>" class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Tournament
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Tournament Status</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Current status:</p>
                                <span class="badge bg-<?php echo $tournament_data['status'] == 'active' ? 'success' : ($tournament_data['status'] == 'completed' ? 'secondary' : 'warning'); ?> fs-6">
                                    <?php echo ucfirst($tournament_data['status']); ?>
                                </span>
                                
                                <hr>
                                <p class="text-muted"><strong>Status Guide:</strong></p>
                                <ul class="list-unstyled">
                                    <li><span class="badge bg-warning">Upcoming</span> - Tournament not started</li>
                                    <li><span class="badge bg-success">Active</span> - Tournament in progress</li>
                                    <li><span class="badge bg-secondary">Completed</span> - Tournament finished</li>
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
