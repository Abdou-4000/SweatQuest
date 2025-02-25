<?php
class Achievement {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function checkAndAwardAchievements($userId) {
        // Get all unearned achievements for user
        $sql = "SELECT a.* FROM achievements a
                LEFT JOIN user_achievements ua 
                ON a.id = ua.achievement_id AND ua.user_id = :user_id
                WHERE ua.user_id IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $achievements = $stmt->fetchAll();
        
        foreach ($achievements as $achievement) {
            if ($this->checkAchievementCriteria($userId, $achievement)) {
                $this->awardAchievement($userId, $achievement['id']);
            }
        }
    }
    
    private function checkAchievementCriteria($userId, $achievement) {
        // Example achievement checks
        switch ($achievement['name']) {
            case 'First Workout':
                return $this->checkFirstWorkout($userId);
            case 'Consistency King':
                return $this->checkWorkoutStreak($userId, 5);
            case 'Weight Warrior':
                return $this->checkTotalWeight($userId, 1000);
            default:
                return false;
        }
    }
    
    private function checkFirstWorkout($userId) {
        $sql = "SELECT COUNT(*) as count FROM workout_logs WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] == 1;
    }
    
    private function checkWorkoutStreak($userId, $days) {
        // Complex query to check for consecutive workout days
        $sql = "SELECT COUNT(*) as streak
                FROM (
                    SELECT DISTINCT DATE(created_at) as workout_date
                    FROM workout_logs
                    WHERE user_id = :user_id
                    ORDER BY workout_date DESC
                    LIMIT :days
                ) as dates";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':days' => $days
        ]);
        
        $result = $stmt->fetch();
        return $result['streak'] >= $days;
    }
    
    private function awardAchievement($userId, $achievementId) {
        $sql = "INSERT INTO user_achievements (user_id, achievement_id) 
                VALUES (:user_id, :achievement_id)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':achievement_id' => $achievementId
        ]);
        
        // Get XP reward
        $sql = "SELECT xp_reward FROM achievements WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $achievementId]);
        $achievement = $stmt->fetch();
        
        // Award XP
        $user = new User();
        $user->updateXP($userId, $achievement['xp_reward']);
        
        // Create notification
        $this->createNotification($userId, $achievementId);
    }
    
    private function createNotification($userId, $achievementId) {
        $sql = "INSERT INTO notifications (user_id, type, message)
                SELECT :user_id, 'achievement', 
                CONCAT('Achievement Unlocked: ', name, ' (+', xp_reward, ' XP)')
                FROM achievements WHERE id = :achievement_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':achievement_id' => $achievementId
        ]);
    }
}

