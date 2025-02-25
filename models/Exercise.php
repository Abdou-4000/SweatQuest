<?php
class Exercise {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllExercises() {
        $sql = "SELECT id, name, muscle_group, description FROM exercises ORDER BY muscle_group, name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getExerciseById($id) {
        $sql = "SELECT id, name, muscle_group, description FROM exercises WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getExercisesByMuscleGroup($muscleGroup) {
        $sql = "SELECT id, name, muscle_group, description FROM exercises WHERE muscle_group = :muscle_group ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':muscle_group' => $muscleGroup]);
        return $stmt->fetchAll();
    }
    
    public function addExercise($name, $muscleGroup, $description = null) {
        $sql = "INSERT INTO exercises (name, muscle_group, description) VALUES (:name, :muscle_group, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $name, 
            ':muscle_group' => $muscleGroup,
            ':description' => $description
        ]);
    }
}