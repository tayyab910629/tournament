<?php
require_once '../../config/config.php';
require_once '../../classes/Team.php';
require_once '../../classes/Tournament.php';

requireLogin();

$tournament_id = $_GET['tournament_id'] ?? 0;
$tournament = new Tournament();
$tournament_data = $tournament->getById($tournament_id);

if (!$tournament_data) {
    redirect('/admin/tournaments/');
}

$error = '';
$success = '';

if ($_POST) {
    $name = sanitize($_POST['name']);
    $country = sanitize($_POST['country']);
    $group_name = sanitize($_POST['group_name']);
    
    $flag_image = '';
    if (isset($_FILES['flag_image']) && $_FILES['flag_image']['error'] == 0) {
        $upload_dir = '../../uploads/flags/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['flag_image']['name'], PATHINFO_EXTENSION);
        $flag_image = 'uploads/flags/' . uniqid() . '.' . $file_extension;
        
        if (!move_uploaded_file($_FILES['flag_image']['tmp_name'], '../../' . $flag_image)) {
            $flag_image = '';
        }
    }
    
    if (empty($name) || empty($group_name)) {
        $error = 'Team name and group are required';
    } else {
        $team = new Team();
        $team_id = $team->create($tournament_id, $name, $country, $flag_image, $group_name);
        
        if ($team_id) {
            $success = 'Team created successfully!';
            header("refresh:2;url=../tournaments/view.php?id=$tournament_id");
        } else {
            $error = 'Failed to create team';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Team - <?php echo htmlspecialchars($tournament_data['name']); ?></title>
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
                    <h1 class="h2">Add Team to <?php echo htmlspecialchars($tournament_data['name']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="../tournaments/view.php?id=<?php echo $tournament_id; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tournament
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Team Details</h6>
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
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Team Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                                               placeholder="e.g., England" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" name="country" 
                                               value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>" 
                                               placeholder="e.g., United Kingdom">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="group_name" class="form-label">Group *</label>
                                        <select class="form-select" id="group_name" name="group_name" required>
                                            <option value="">Select Group</option>
                                            <?php for ($i = ord('A'); $i <= ord('H'); $i++): ?>
                                                <option value="<?php echo chr($i); ?>" <?php echo (isset($_POST['group_name']) && $_POST['group_name'] == chr($i)) ? 'selected' : ''; ?>>
                                                    Group <?php echo chr($i); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="flag_image" class="form-label">Team Flag</label>
                                        <input type="file" class="form-control" id="flag_image" name="flag_image" accept="image/*">
                                        <div class="form-text">Upload a flag image (optional)</div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="../tournaments/view.php?id=<?php echo $tournament_id; ?>" class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Add Team
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Group Guidelines</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Recommended group structure:</p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> 4 teams per group</li>
                                    <li><i class="fas fa-check text-success"></i> Balanced skill levels</li>
                                    <li><i class="fas fa-check text-success"></i> Geographic diversity</li>
                                </ul>
                                <hr>
                                <p class="text-muted"><strong>Next:</strong> After adding all teams, generate fixtures to create the group stage matches.</p>
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
