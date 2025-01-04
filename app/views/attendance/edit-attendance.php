<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendance</title>
</head>

<body>
    <h1>Edit Attendance</h1>
    <form action="/attendance/edit?attendance_id=<?= htmlspecialchars($attendance['id']) ?>" method="POST">
        <label>Days Worked:</label>
        <input type="number" name="days_worked" value="<?= htmlspecialchars($attendance['days_worked']) ?>" required>
        <br>
        <label>Month:</label>
        <input type="number" name="month" value="<?= htmlspecialchars($attendance['month']) ?>" required min="1" max="12">
        <br>
        <label>Year:</label>
        <input type="number" name="year" value="<?= htmlspecialchars($attendance['year']) ?>" required>
        <br>
        <button type="submit">Update Attendance</button>
    </form>
</body>

</html>