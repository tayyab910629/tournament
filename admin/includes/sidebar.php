<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="/admin/dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/tournaments/') !== false ? 'active' : ''; ?>" href="/admin/tournaments/">
                    <i class="fas fa-trophy"></i> Tournaments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/teams/') !== false ? 'active' : ''; ?>" href="/admin/teams/">
                    <i class="fas fa-users"></i> Teams
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/matches/') !== false ? 'active' : ''; ?>" href="/admin/matches/">
                    <i class="fas fa-futbol"></i> Matches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/players/') !== false ? 'active' : ''; ?>" href="/admin/players/">
                    <i class="fas fa-user"></i> Players
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Reports</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="/admin/reports/standings.php">
                    <i class="fas fa-chart-bar"></i> Group Standings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/reports/bracket.php">
                    <i class="fas fa-sitemap"></i> Knockout Bracket
                </a>
            </li>
        </ul>
    </div>
</nav>
