<?php
echo "Moodle Debug Test<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test database connection
try {
    $pdo = new PDO('mysql:host=localhost;port=3307;dbname=moodle', 'root', '');
    echo "Database connection: OK<br>";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "Tables in database: " . count($tables) . "<br>";
    
} catch(Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test config file
if (file_exists('config.php')) {
    echo "Config file: EXISTS<br>";
    include_once 'config.php';
    if (isset($CFG)) {
        echo "Config loaded: OK<br>";
        echo "Database type: " . $CFG->dbtype . "<br>";
        echo "Database host: " . $CFG->dbhost . "<br>";
        echo "Database port: " . $CFG->dboptions['dbport'] . "<br>";
    } else {
        echo "Config loaded: FAILED<br>";
    }
} else {
    echo "Config file: NOT FOUND<br>";
}
?>
