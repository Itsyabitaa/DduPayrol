<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Data</title>
    <script>
        // Function to dynamically calculate net pay
        function calculateNetPay(rowId) {
            // Get values from inputs
            const baseSalary = parseFloat(document.getElementById(`salary-${rowId}`).value) || 0;
            const addon = parseFloat(document.getElementById(`addon-${rowId}`).value) || 0;
            const attendanceDays = parseFloat(document.getElementById(`attendance-${rowId}`).value) || 0;
            const bonus = parseFloat(document.getElementById(`bonus-${rowId}`).value) || 0;
            const penalty = parseFloat(document.getElementById(`penalty-${rowId}`).value) || 0;

            // Calculate pension as 15% of salary
            const pension = baseSalary * 0.15;
            document.getElementById(`pension-${rowId}`).value = pension.toFixed(2);

            // Calculate net pay
            const netPay = (baseSalary + addon) * attendanceDays - penalty + bonus - pension;
            document.getElementById(`netpay-${rowId}`).textContent = netPay.toFixed(2);
        }

        // Function to calculate all rows initially
        function calculateAllRows() {
            const rows = document.querySelectorAll('.payroll-row');
            rows.forEach(row => {
                const rowId = row.dataset.rowId;
                calculateNetPay(rowId);
            });
        }
    </script>
</head>

<body onload="calculateAllRows()">
    <h2>Payroll for <?php echo $month . " " . $year; ?></h2>
    <form action="/payroll/save-temporary" method="POST">
        <input type="hidden" name="month" value="<?php echo $month; ?>">
        <input type="hidden" name="year" value="<?php echo $year; ?>">

        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Base Salary</th>
                    <th>Position Addon</th>
                    <th>Attendance Days</th>
                    <th>Pension (15%)</th>
                    <th>Penalty</th>
                    <th>Bonus</th>
                    <th>Net Pay</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as &$user): ?>
                    <tr class="payroll-row" data-row-id="<?php echo $user['id']; ?>">
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td>
                            <input type="number" id="salary-<?php echo $user['id']; ?>"
                                value="<?php echo htmlspecialchars($user['salary']); ?>" readonly>
                        </td>
                        <td>
                            <input type="number" id="addon-<?php echo $user['id']; ?>"
                                value="<?php echo htmlspecialchars($user['position_addon']); ?>" readonly>
                        </td>
                        <td>
                            <input type="number" id="attendance-<?php echo $user['id']; ?>"
                                value="<?php echo htmlspecialchars($user['attendance_days']); ?>" readonly>
                        </td>
                        <td>
                            <input type="number" id="pension-<?php echo $user['id']; ?>"
                                value="<?php echo number_format($user['salary'] * 0.15, 2); ?>" readonly>
                        </td>
                        <td>
                            <input type="number" id="penalty-<?php echo $user['id']; ?>"
                                name="penalty[<?php echo $user['id']; ?>]"
                                value="<?php echo htmlspecialchars($user['penalty']); ?>"
                                oninput="calculateNetPay(<?php echo $user['id']; ?>)">
                        </td>
                        <td>
                            <input type="number" id="bonus-<?php echo $user['id']; ?>"
                                name="bonus[<?php echo $user['id']; ?>]"
                                value="<?php echo htmlspecialchars($user['bonus']); ?>"
                                oninput="calculateNetPay(<?php echo $user['id']; ?>)">
                        </td>
                        <td id="netpay-<?php echo $user['id']; ?>">
                            <?php
                            $baseSalary = $user['salary'];
                            $addon = $user['position_addon'];
                            $attendanceDays = $user['attendance_days'];
                            $pension = $baseSalary * 0.15;
                            $penalty = $user['penalty'];
                            $bonus = $user['bonus'];

                            $netPay = ($baseSalary + $addon) * $attendanceDays - $penalty + $bonus - $pension;
                            echo number_format($netPay, 2);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit">Save Payroll</button>

    </form>

</body>

</html>