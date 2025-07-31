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

if ($_POST && isset($_POST['confirm_delete'])) {
    if ($tournament->delete($tournament_id)) {
        redirect('/admin/tournaments/?deleted=1');
    } else {
        $error = 'Failed to delete tournament';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Tournament - Football Tournament Admin</title>
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
                    <h1 class="h2">Delete Tournament</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="index.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tournaments
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> This action cannot be undone!
                                </div>
                                
                                <p>You are about to delete the following tournament:</p>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($tournament_data['name']); ?></h5>
                                        <?php if ($tournament_data['description']): ?>
                                            <p class="card-text"><?php echo htmlspecialchars($tournament_data['description']); ?></p>
                                        <?php endif; ?>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Status: <?php echo ucfirst($tournament_data['status']); ?> | 
                                                Created: <?php echo date('M j, Y', strtotime($tournament_data['created_at'])); ?>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                                
                                <p class="mt-3 text-danger">
                                    <strong>This will permanently delete:</strong>
                                </p>
                                <ul class="text-danger">
                                    <li>The tournament and all its data</li>
                                    <li>All teams associated with this tournament</li>
                                    <li>All players in those teams</li>
                                    <li>All matches and results</li>
                                    <li>All group standings</li>
                                </ul>
                                
                                <form method="POST" class="mt-4">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="index.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" name="confirm_delete" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Yes, Delete Tournament
                                        </button>
                                    </div>
                                </form>
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
