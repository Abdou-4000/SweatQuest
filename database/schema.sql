-- Create database
CREATE DATABASE IF NOT EXISTS workout_logger;
USE workout_logger;

-- Users table
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(100) NOT NULL,
    xp INT DEFAULT 0,
    level INT DEFAULT 1,
    last_workout_date DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Exercise definitions
CREATE TABLE exercises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    muscle_group ENUM('chest', 'back', 'legs', 'shoulders', 'arms', 'core', 'cardio') NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Workout logs (immutable)
CREATE TABLE workout_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    exercise_id INT NOT NULL,
    sets INT NOT NULL,
    reps INT NOT NULL,
    weight DECIMAL(5,2),
    notes TEXT,
    xp_earned INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (exercise_id) REFERENCES exercises(id)
);

-- Achievements
CREATE TABLE achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    xp_reward INT NOT NULL,
    icon VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- User achievements
CREATE TABLE user_achievements (
    user_id INT NOT NULL,
    achievement_id INT NOT NULL,
    earned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, achievement_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (achievement_id) REFERENCES achievements(id)
);

-- Challenges
CREATE TABLE challenges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    goal_type ENUM('workout_count', 'total_weight', 'streak') NOT NULL,
    goal_value INT NOT NULL,
    duration_days INT NOT NULL,
    xp_reward INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- User challenges
CREATE TABLE user_challenges (
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    progress INT DEFAULT 0,
    start_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    PRIMARY KEY (user_id, challenge_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (challenge_id) REFERENCES challenges(id)
);

-- Notifications
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('achievement', 'challenge', 'motivation', 'xp_decay') NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert some sample exercises
INSERT INTO exercises (name, muscle_group, description) VALUES
('Bench Press', 'chest', 'Barbell bench press for chest development'),
('Squat', 'legs', 'Barbell back squat for leg strength'),
('Deadlift', 'back', 'Conventional deadlift for overall strength'),
('Pull-ups', 'back', 'Bodyweight pulling exercise for back and arms'),
('Push-ups', 'chest', 'Bodyweight pushing exercise for chest and triceps');

-- Insert sample achievements
INSERT INTO achievements (name, description, xp_reward, icon) VALUES
('First Workout', 'Complete your first workout', 100, '🎯'),
('Consistency King', 'Work out 5 days in a row', 500, '👑'),
('Weight Warrior', 'Lift 1000kg total in one week', 1000, '💪'),
('Early Bird', 'Complete 5 workouts before 8 AM', 300, '🌅'),
('Beast Mode', 'Complete 10 different exercises in one workout', 400, '🔥');

-- Insert sample challenges
INSERT INTO challenges (name, description, goal_type, goal_value, duration_days, xp_reward) VALUES
('Week Warrior', 'Complete 5 workouts in 7 days', 'workout_count', 5, 7, 500),
('Heavyweight', 'Lift 2000kg total', 'total_weight', 2000, 14, 1000),
('Iron Streak', 'Maintain a 10-day workout streak', 'streak', 10, 10, 1000);

