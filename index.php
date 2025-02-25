<?php
session_start();

// Load configuration
require_once 'config/Database.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        'controllers/',
        'models/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Simple router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

// Routes
switch ($uri) {
    case '':
    case 'dashboard':
        $controller = new WorkoutController();
        $controller->getDashboard();
        break;
        
    case 'workout/log':
        $controller = new WorkoutController();
        echo $controller->logWorkout();
        break;
        
    // Add more routes as needed
        
    default:
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
        break;
}

