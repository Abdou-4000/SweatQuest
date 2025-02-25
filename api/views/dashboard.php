<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SweatQuest - Your Workout Journey</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .progress {
            height: 20px;
        }
        .muscle-group-badge {
            font-size: 0.8rem;
        }
        .level-badge {
            background-color: #6c5ce7;
            color: white;
            padding: 0.4rem 0.6rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-dumbbell"></i> SweatQuest</a>
            <div class="d-flex align-items-center">
                <span class="level-badge me-3">Level <?= $stats['level'] ?></span>
                <div>
                    <small class="text-light d-block">XP: <?= $stats['xp'] ?></small>
                    <div class="progress" style="width: 100px">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($stats['xp'] % 1000) / 10 ?>%" 
                             aria-valuenow="<?= $stats['xp'] % 1000 ?>" aria-valuemin="0" aria-valuemax="1000"></div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Quick Log Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-plus-circle"></i> Quick Log Workout</h5>
                <form id="quickLogForm" class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" name="exercise_id" required>
                            <option value="">Select Exercise</option>
                            <?php foreach ($exercises as $exercise): ?>
                            <option value="<?= $exercise['id'] ?>"><?= htmlspecialchars($exercise['name']) ?> 
                                (<?= ucfirst($exercise['muscle_group']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="sets" placeholder="Sets" required min="1">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="reps" placeholder="Reps" required min="1">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.5" class="form-control" name="weight" placeholder="Weight (kg)">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Log Workout
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Stats Card -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Your Stats</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-fire"></i> Total Workouts</span>
                                <span class="badge bg-primary rounded-pill"><?= $stats['total_workouts'] ?? 0 ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-star"></i> Total XP Earned</span>
                                <span class="badge bg-success rounded-pill"><?= $stats['total_xp_earned'] ?? 0 ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-trophy"></i> Achievements</span>
                                <span class="badge bg-warning text-dark rounded-pill"><?= $stats['achievements_count'] ?? 0 ?></span>
                            </li>
                        </ul>
                        <div class="mt-3">
                            <h6>Muscle Groups Worked</h6>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <?php 
                                $allMuscleGroups = ['chest', 'back', 'legs', 'shoulders', 'arms', 'core', 'cardio'];
                                foreach ($allMuscleGroups as $group): 
                                    $active = in_array($group, $muscleGroups ?? []);
                                ?>
                                    <span class="badge <?= $active ? 'bg-info' : 'bg-secondary' ?> <?= $active ? 'text-dark' : '' ?>">
                                        <?= ucfirst($group) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Workouts -->
            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-history"></i> Recent Workouts</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentWorkouts)): ?>
                            <div class="alert alert-info">
                                No workouts logged yet. Start your fitness journey by logging your first workout!
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Exercise</th>
                                            <th>Sets</th>
                                            <th>Reps</th>
                                            <th>Weight</th>
                                            <th>XP</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentWorkouts as $workout): ?>
                                        <tr>
                                            <td>
                                                <span class="d-block"><?= htmlspecialchars($workout['exercise_name']) ?></span>
                                                <span class="badge bg-secondary muscle-group-badge">
                                                    <?= ucfirst($workout['muscle_group']) ?>
                                                </span>
                                            </td>
                                            <td><?= $workout['sets'] ?></td>
                                            <td><?= $workout['reps'] ?></td>
                                            <td><?= $workout['weight'] ? $workout['weight'] . ' kg' : '-' ?></td>
                                            <td><span class="badge bg-success">+<?= $workout['xp_earned'] ?> XP</span></td>
                                            <td><?= date('M d, H:i', strtotime($workout['created_at'])) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Success!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Your workout has been logged successfully!</p>
                    <p id="xpEarned" class="fw-bold"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Awesome!</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('quickLogForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('/SweatQuest/api/workout/log', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            document.getElementById('xpEarned').textContent = `You earned ${data.xp_earned} XP!`;
            successModal.show();
            
            // Reset form
            e.target.reset();
            
            // Refresh the page after modal is closed
            document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
                location.reload();
            });
        } else {
            throw new Error(data.error || 'Failed to log workout');
        }
    } catch (error) {
        console.error('Error:', error);
        // Show error in a more user-friendly way
        const errorMessage = error.message || 'Failed to log workout. Please try again.';
        alert(errorMessage);
    }
});
    </script>
</body>
</html>