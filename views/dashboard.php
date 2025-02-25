<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Logger - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">💪 Workout Logger</a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3">Level <?= $stats['level'] ?></span>
                <span class="text-light">XP: <?= $stats['xp'] ?></span>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Quick Log Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Quick Log</h5>
                <form id="quickLogForm" class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" name="exercise_id" required>
                            <option value="">Select Exercise</option>
                            <!-- PHP: Loop through exercises -->
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="sets" placeholder="Sets" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="reps" placeholder="Reps" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.5" class="form-control" name="weight" placeholder="Weight (kg)">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Log Workout</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Stats Card -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Your Stats</h5>
                        <ul class="list-unstyled">
                            <li>Total Workouts: <?= $stats['total_workouts'] ?></li>
                            <li>Total XP Earned: <?= $stats['total_xp_earned'] ?></li>
                            <li>Achievements: <?= $stats['achievements_count'] ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Workouts -->
            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Recent Workouts</h5>
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
                                        <td><?= htmlspecialchars($workout['exercise_name']) ?></td>
                                        <td><?= $workout['sets'] ?></td>
                                        <td><?= $workout['reps'] ?></td>
                                        <td><?= $workout['weight'] ? $workout['weight'] . ' kg' : '-' ?></td>
                                        <td><?= $workout['xp_earned'] ?></td>
                                        <td><?= date('M d, H:i', strtotime($workout['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
                const response = await fetch('/workout/log', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Refresh the page to show new workout
                    location.reload();
                } else {
                    alert(data.error || 'Failed to log workout');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to log workout');
            }
        });
    </script>
</body>
</html>

