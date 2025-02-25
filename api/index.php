<?php
phpinfo();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// If no user is logged in, set a test user for development
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

// Load database configuration
require_once __DIR__ . '/config/Database.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/controllers/',
        __DIR__ . '/models/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    echo "Failed to load class: " . $class . "<br>";
});

// Simple router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');
$uri = str_replace('SweatQuest/api/', '', $uri);

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

//test