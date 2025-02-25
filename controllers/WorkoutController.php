<?php
class WorkoutController {
    private $workout;
    private $achievement;
    
    public function __construct() {
        $this->workout = new Workout();
        $this->achievement = new Achievement();
    }
    
    public function logWorkout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return json_encode(['error' => 'Invalid request method']);
        }
        
        // Validate input
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return json_encode(['error' => 'User not authenticated']);
        }
        
        $exerciseId = filter_input(INPUT_POST, 'exercise_id', FILTER_VALIDATE_INT);
        $sets = filter_input(INPUT_POST, 'sets', FILTER_VALIDATE_INT);
        $reps = filter_input(INPUT_POST, 'reps', FILTER_VALIDATE_INT);
        $weight = filter_input(INPUT_POST, 'weight', FILTER_VALIDATE_FLOAT);
        $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
        
        if (!$exerciseId || !$sets || !$reps) {
            return json_encode(['error' => 'Invalid input parameters']);
        }
        
        // Log the workout
        if ($this->workout->logWorkout($userId, $exerciseId, $sets, $reps, $weight, $notes)) {
            // Check for achievements
            $this->achievement->checkAndAwardAchievements($userId);
            
            return json_encode([
                'success' => true,
                'message' => 'Workout logged successfully'
            ]);
        }
        
        return json_encode(['error' => 'Failed to log workout']);
    }
    
    public function getDashboard() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }
        
        $user = new User();
        $stats = $user->getStats($userId);
        $recentWorkouts = $this->workout->getRecentWorkouts($userId);
        
        // Load the dashboard view with data
        require_once 'views/dashboard.php';
    }
}

