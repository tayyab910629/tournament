<?php
require_once 'config/database.php';

echo "<h2>Football Tournament Setup</h2>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Could not connect to database");
    }
    
    $sql = file_get_contents('database/schema.sql');
    
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $conn->exec($statement);
        }
    }
    
    echo "<div style='color: green; padding: 20px; border: 1px solid green; margin: 20px 0;'>";
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p>Database tables created successfully.</p>";
    echo "<p><strong>Default Admin Login:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <code>admin</code></li>";
    echo "<li>Password: <code>admin123</code></li>";
    echo "</ul>";
    echo "<p><a href='admin/login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Panel</a></p>";
    echo "<p><a href='index.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Public Site</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 20px; border: 1px solid red; margin: 20px 0;'>";
    echo "<h3>❌ Setup Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in <code>config/database.php</code></p>";
    echo "</div>";
}
?>
