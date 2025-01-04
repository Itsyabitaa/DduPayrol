<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
</head>

<body>
    <h1>Attendance Records</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Days Worked</th>
                <th>Month</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendances as $attendance): ?>
                <tr>
                    <td><?= htmlspecialchars($attendance['employee_name']) ?></td>
                    <td><?= htmlspecialchars($attendance['days_worked']) ?></td>
                    <td><?= htmlspecialchars($attendance['month']) ?></td>
                    <td><?= htmlspecialchars($attendance['year']) ?></td>
                    <td>
                        <a href="/attendance/edit?attendance_id=<?= htmlspecialchars($attendance['id']) ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>