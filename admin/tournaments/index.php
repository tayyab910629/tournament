<?php
require_once '../../config/config.php';
require_once '../../classes/Tournament.php';

requireLogin();

$tournament = new Tournament();
$tournaments = $tournament->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournaments - Football Tournament Admin</title>
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
                    <h1 class="h2">Tournaments</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="create.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> New Tournament
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (empty($tournaments)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-trophy fa-4x text-gray-300 mb-4"></i>
                        <h4 class="text-muted">No tournaments yet</h4>
                        <p class="text-muted">Create your first tournament to get started</p>
                        <a href="create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Tournament
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($tournaments as $t): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card tournament-card shadow">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title"><?php echo htmlspecialchars($t['name']); ?></h5>
                                            <span class="badge bg-<?php echo $t['status'] == 'active' ? 'success' : ($t['status'] == 'completed' ? 'secondary' : 'warning'); ?>">
                                                <?php echo ucfirst($t['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <?php if ($t['description']): ?>
                                            <p class="card-text text-muted"><?php echo htmlspecialchars(substr($t['description'], 0, 100)); ?><?php echo strlen($t['description']) > 100 ? '...' : ''; ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="row text-center mb-3">
                                            <div class="col">
                                                <small class="text-muted">Start Date</small>
                                                <div><?php echo $t['start_date'] ? date('M j, Y', strtotime($t['start_date'])) : 'TBD'; ?></div>
                                            </div>
                                            <div class="col">
                                                <small class="text-muted">End Date</small>
                                                <div><?php echo $t['end_date'] ? date('M j, Y', strtotime($t['end_date'])) : 'TBD'; ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="view.php?id=<?php echo $t['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="edit.php?id=<?php echo $t['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="delete.php?id=<?php echo $t['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this tournament?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
