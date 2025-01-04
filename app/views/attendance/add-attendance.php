<!-- views/attendance/add-attendance.php -->
<form method="POST" action="/attendance/store">
    <label for="user_id">Employee ID:</label>
    <input type="number" id="user_id" name="user_id" required>

    <label for="days_worked">Days Worked:</label>
    <input type="number" id="days_worked" name="days_worked" required>

    <label for="month">Month:</label>
    <select name="month" id="month" required>
        <option value="<?php echo date('m'); ?>"><?php echo date('F'); ?></option>
    </select>

    <label for="year">Year:</label>
    <select name="year" id="year" required>
        <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
    </select>

    <button type="submit">Add Attendance</button>
</form>