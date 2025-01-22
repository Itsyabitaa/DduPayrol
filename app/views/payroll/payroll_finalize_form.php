<h1>Finalize Payroll</h1>
<form method="POST" action="/payroll/finalize">
    <label for="month">Month:</label>
    <input type="number" name="month" min="1" max="12" required>
    <label for="year">Year:</label>
    <input type="number" name="year" min="2000" max="<?= date('Y') ?>" required>
    <button type="submit">Check Payroll</button>
</form>