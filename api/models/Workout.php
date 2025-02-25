<?php
class Workout {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function logWorkout($userId, $exerciseId, $sets, $reps, $weight = null, $notes = null) {
        $xpEarned = $this->calculateXP($sets, $reps, $weight);
        
        $sql = "INSERT INTO workout_logs (user_id, exercise_id, sets, reps, weight, notes, xp_earned) 
                VALUES (:user_id, :exercise_id, :sets, :reps, :weight, :notes, :xp_earned)";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                ':user_id' => $userId,
                ':exercise_id' => $exerciseId,
                ':sets' => $sets,
                ':reps' => $reps,
                ':weight' => $weight,
                ':notes' => $notes,
                ':xp_earned' => $xpEarned
            ]);
            
            // Get the ID of the inserted workout
            $workoutId = $this->db->lastInsertId();
            
            // Update user's XP
            $user = new User();
            $user->updateXP($userId, $xpEarned);
            
            return [
                'id' => $workoutId,
                'xp_earned' => $xpEarned
            ];
        } catch(PDOException $e) {
            return false;
        }
    }
    
    private function calculateXP($sets, $reps, $weight) {
        // Basic XP calculation formula
        $baseXP = $sets * $reps * 1; // 1 XP per rep
        
        if ($weight) {
            // Bonus XP for weight-based exercises
            $weightMultiplier = $weight / 10; // 1 extra XP per 10kg
            $baseXP += $weightMultiplier;
        }
        
        return round($baseXP);
    }
    
    public function getRecentWorkouts($userId, $limit = 10) {
        $sql = "SELECT w.*, e.name as exercise_name, e.muscle_group
                FROM workout_logs w
                JOIN exercises e ON w.exercise_id = e.id
                WHERE w.user_id = :user_id
                ORDER BY w.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getWorkoutsByMuscleGroup($userId, $muscleGroup, $limit = 10) {
        $sql = "SELECT w.*, e.name as exercise_name, e.muscle_group
                FROM workout_logs w
                JOIN exercises e ON w.exercise_id = e.id
                WHERE w.user_id = :user_id AND e.muscle_group = :muscle_group
                ORDER BY w.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':muscle_group', $muscleGroup, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getTotalWeight($userId) {
        $sql = "SELECT SUM(sets * reps * weight) as total_weight
                FROM workout_logs
                WHERE user_id = :user_id AND weight IS NOT NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        
        return $result['total_weight'] ?? 0;
    }
}