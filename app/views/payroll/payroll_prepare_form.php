<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prepare Payroll</title>
</head>

<body>
    <h2>Prepare Payroll</h2>
    <!-- Form to Prepare Payroll -->
    <form action="" method="POST">
        <input type="hidden" name="action" value="prepare"> <!-- Differentiating action -->

        <label for="month">Month:</label>
        <select name="month" id="month" required>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>

        <label for="year">Year:</label>
        <input type="number" name="year" id="year" value="<?php echo date('Y'); ?>" min="2025" required>

        <button type="submit">Prepare Payroll</button>
    </form>

    <h2>Edit Payroll</h2>
    <!-- Form to Edit Payroll -->
    <form action="/payroll/edit" method="POST" style="display: inline-block;">
        <input type="hidden" name="action" value="edit"> <!-- Differentiating action -->

        <label for="edit-month">Month:</label>
        <select name="month" id="edit-month" required>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>

        <label for="edit-year">Year:</label>
        <input type="number" name="year" id="edit-year" value="<?php echo date('Y'); ?>" min="2025" required>

        <button type="submit">Edit Payroll</button>
    </form>
</body>

</html>