<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($username, $email, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password)";
        $stmt = $this->db->prepare($sql);
        
        try {
            return $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hash
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function authenticate($username, $password) {
        $sql = "SELECT id, username, password_hash FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        
        if ($user = $stmt->fetch()) {
            if (password_verify($password, $user['password_hash'])) {
                unset($user['password_hash']);
                return $user;
            }
        }
        return false;
    }
    
    public function updateXP($userId, $xp) {
        $sql = "UPDATE users SET xp = xp + :xp WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':xp' => $xp, ':id' => $userId]);
    }
    
    public function getStats($userId) {
        $sql = "SELECT u.username, u.xp, u.level, 
                COUNT(DISTINCT w.id) as total_workouts,
                SUM(w.xp_earned) as total_xp_earned,
                COUNT(DISTINCT ua.achievement_id) as achievements_count
                FROM users u
                LEFT JOIN workout_logs w ON u.id = w.user_id
                LEFT JOIN user_achievements ua ON u.id = ua.user_id
                WHERE u.id = :id
                GROUP BY u.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch();
    }
}

