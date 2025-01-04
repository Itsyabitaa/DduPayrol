<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Employees</title>
</head>

<body>
    <h1>Employees in Your School</h1>
    <ul>
        <?php foreach ($employees as $employee): ?>
            <li>
                <?= htmlspecialchars($employee['first_name']) ?>
                <form action="/attendance/store" method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $employee['id'] ?>">
                    <input type="number" name="days_worked" placeholder="Days Worked" required>
                    <input type="number" name="month" placeholder="Month (1-12)" required>
                    <input type="number" name="year" placeholder="Year" required>
                    <button type="submit">Add Attendance</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>