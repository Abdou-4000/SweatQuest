<?php

require_once('./config/DataBase.php');
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "Database connected successfully<br>";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Temporary: Set a test user session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

// Load configuration
require_once __DIR__ . '/config/Database.php';

// Debug: Print current path
echo "Current path: " . __DIR__ . "<br>";

// Autoload classes with debug
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/controllers/',
        __DIR__ . '/models/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        echo "Checking file: " . $file . "<br>";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    echo "Failed to load class: " . $class . "<br>";
});

// Simple router with debug
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');
$uri = str_replace('SweatQuest/', '', $uri);
echo "Processed URI: " . $uri . "<br>";

// Routes
switch ($uri) {
    case '':
    case 'index.php':
    case 'dashboard':
        if (class_exists('WorkoutController')) {
            $controller = new WorkoutController();
            $controller->getDashboard();
        } else {
            die('WorkoutController not found');
        }
        break;
        
    case 'workout/log':
        $controller = new WorkoutController();
        echo $controller->logWorkout();
        break;
        
    default:
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found - URI: ' . $uri;
        break;
}