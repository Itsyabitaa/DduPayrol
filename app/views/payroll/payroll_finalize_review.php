<h1>Review Payroll for Finalization</h1>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Salary</th>
            <th>Position Addon</th>
            <th>Pension</th>
            <th>Penalty</th>
            <th>Bonus</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($payrollDetails as $detail): ?>
            <tr>
                <td><?= htmlspecialchars($detail['first_name']) ?></td>
                <td><?= htmlspecialchars($detail['base_salary']) ?></td>
                <td><?= htmlspecialchars($detail['position_addon']) ?></td>
                <td><?= htmlspecialchars($detail['pension']) ?></td>
                <td><?= htmlspecialchars($detail['penalty']) ?></td>
                <td><?= htmlspecialchars($detail['bonus']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<form method="POST" action="/payroll/confirm-finalize">
    <input type="hidden" name="month" value="<?= htmlspecialchars($_POST['month']) ?>">
    <input type="hidden" name="year" value="<?= htmlspecialchars($_POST['year']) ?>">
    <button type="submit">Finalize Payroll</button>
</form>