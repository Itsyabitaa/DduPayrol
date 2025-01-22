<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payroll</title>
    <script>
        function calculateNetPay(rowId) {
            const baseSalary = parseFloat(document.getElementById(`salary-${rowId}`).value) || 0;
            const addon = parseFloat(document.getElementById(`addon-${rowId}`).value) || 0;
            const attendanceDays = parseFloat(document.getElementById(`attendance-${rowId}`).value) || 0;
            const bonus = parseFloat(document.getElementById(`bonus-${rowId}`).value) || 0;
            const penalty = parseFloat(document.getElementById(`penalty-${rowId}`).value) || 0;

            // Proportionate base salary and addon
            const effectiveBaseSalary = (baseSalary / 30) * attendanceDays;
            const effectiveAddon = (addon / 30) * attendanceDays;

            // Calculate pension (15% of effective base salary)
            const pension = effectiveBaseSalary * 0.15;

            document.getElementById(`pension-${rowId}`).value = pension.toFixed(2);

            // Calculate net pay
            const netPay = effectiveBaseSalary + effectiveAddon - penalty + bonus - pension;
            document.getElementById(`netpay-${rowId}`).value = netPay.toFixed(2);
        }
    </script>
</head>

<body>
    <h2>Edit Payroll for <?php echo htmlspecialchars("$month $year"); ?></h2>
    <form action="/payroll/update" method="POST">
        <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
        <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">

        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Base Salary</th>
                    <th>Position Addon</th>
                    <th>Attendance Days</th>
                    <th>Pension</th>
                    <th>Penalty</th>
                    <th>Bonus</th>
                    <th>Net Pay</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payrollRecords as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['first_name']); ?></td>
                        <!-- Base Salary -->
                        <td><input type="number" name="payroll[<?php echo $record['id']; ?>][base_salary]" value="<?php echo htmlspecialchars($record['base_salary']); ?>" readonly></td>
                        <!-- Position Addon -->
                        <td><input type="number" name="payroll[<?php echo $record['id']; ?>][position_addon]" value="<?php echo htmlspecialchars($record['position_addon']); ?>" readonly></td>
                        <!-- Attendance Days -->
                        <td><input type="number" name="payroll[<?php echo $record['id']; ?>][attendance_days]" value="<?php echo htmlspecialchars($record['attendance_days']); ?>" readonly></td>
                        <!-- Pension -->
                        <td><input type="number" name="payroll[<?php echo $record['id']; ?>][pension]" value="<?php echo htmlspecialchars($record['pension']); ?>" readonly></td>
                        <!-- Penalty (Editable) -->
                        <td><input type="number" name="payroll[<?php echo $record['id']; ?>][penalty]" value="<?php echo htmlspecialchars($record['penalty']); ?>"></td>
                        <!-- Bonus (Editable) -->
                        <td><input type="number" name="payroll[<?php echo $record['id']; ?>][bonus]" value="<?php echo htmlspecialchars($record['bonus']); ?>"></td>
                        <!-- Net Pay -->
                        <td><input type="number" name="payroll[<?php echo $record['id']; ?>][net_pay]" value="<?php echo htmlspecialchars($record['net_pay']); ?>" readonly></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit">Update Payroll</button>
    </form>

</body>

</html>