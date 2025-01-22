<?php

include './app/autoloader.php';
include('/xampp/htdocs/oop_pay/app/core/Database.php');
include("./app/autoloader.php");

use App\Core\Database;

class EmployeeController
{

    public function index()
    {
        // Assuming you have a Database class for DB connection
        $db = Database::getConnection();
        $sql = "SELECT id, first_name, phoneNumber, role_id, salary FROM users";
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pass the data to the view
        include './app/views/employee/addposition.php';
    }

    public function generatePayslip()
    {

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo "User ID is required.";
            return;
        }

        $user_id = $_SESSION['user_id'];

        try {
            // Establish database connection
            $connection = Database::getConnection();

            // Fetch active user details
            $userSql = "SELECT id, salary FROM users WHERE id = :user_id";
            $userStmt = $connection->prepare($userSql);
            $userStmt->execute([':user_id' => $user_id]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo "Active user not found.";
                return;
            }

            // Fetch the most recent payslip details
            $payslipSql = "SELECT 
                            base_salary, 
                            penalty, 
                            pension, 
                            bonus, 
                            position_addon, 
                    
                           bonus, 
                            net_pay, 
                            created_at AS date 
                       FROM payslips 
                       WHERE user_id = :user_id 
                       ORDER BY created_at DESC 
                       LIMIT 1";
            $payslipStmt = $connection->prepare($payslipSql);
            $payslipStmt->execute([':user_id' => $user_id]);
            $payslip = $payslipStmt->fetch(PDO::FETCH_ASSOC);

            if (!$payslip) {
                echo "sorry your payroll is not ready. ";


                return;
            }

            // Render the payslip UI
?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>View Payslip</title>
                <link rel="stylesheet" href="styles.css">
                <style>
                    /* CSS for the UI */
                    body {
                        font-family: 'Roboto', sans-serif;
                        background-color: #f4f6f8;
                        margin: 0;
                        padding: 0;
                    }

                    .dashboard {
                        display: flex;
                        height: 100vh;
                    }

                    .sidebar {
                        width: 280px;
                        background: linear-gradient(135deg, #1e5799, #7db9e8);
                        color: white;
                        padding: 20px;
                    }

                    .main-content {
                        flex: 1;
                        padding: 20px;
                        background-color: #f4f6f8;
                    }

                    .payslip-table {
                        width: 100%;
                        border-collapse: collapse;
                        background: white;
                    }

                    .payslip-table th {
                        background: #1e5799;
                        color: white;
                        padding: 10px;
                    }

                    .payslip-table td {
                        padding: 10px;
                        border: 1px solid #ddd;
                    }
                </style>
            </head>

            <body>
                <div class="dashboard">
                    <!-- Sidebar -->
                    <aside class="sidebar">
                        <h2>Dashboard</h2>
                        <ul>
                            <li><a href="/Director/dashboard">Home</a></li>
                        </ul>
                    </aside>

                    <!-- Main Content -->
                    <main class="main-content">
                        <h1>Payslip</h1>
                        <table class="payslip-table">
                            <thead>
                                <tr>
                                    <th>Salary</th>
                                    <th>Deduction</th>
                                    <th>Pension</th>
                                    <th>Bonus</th>
                                    <th>Addons</th>
                                    <th>bonus</th>
                                    <th>Net Pay</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= htmlspecialchars($payslip['base_salary']) ?></td>
                                    <td><?= htmlspecialchars($payslip['penalty']) ?></td>
                                    <td><?= htmlspecialchars($payslip['pension']) ?></td>
                                    <td><?= htmlspecialchars($payslip['bonus']) ?></td>
                                    <td><?= htmlspecialchars($payslip['position_addon']) ?></td>
                                    <td><?= htmlspecialchars($payslip['bonus']) ?></td>
                                    <td><?= htmlspecialchars($payslip['net_pay']) ?></td>
                                    <td><?= htmlspecialchars($payslip['date']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </main>
                </div>
            </body>

            </html>
<?php

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Display payslip
    public function viewPayslip()
    {


        // Load the view with the payslip data
        include('./app/views/employee/view_payslip.php');
    }
    public function preparePayroll()
    {
        // Check if the role is allowed (role 1 is for admin/authorized user)
        if ($_SESSION['role_id'] !== 1) {
            echo "Unauthorized access.";
            return;
        }

        // Handle POST request to process payroll
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];
            $currentMonth = date('n');
            $currentYear = date('Y');

            // Validate month/year (current or previous month)
            if (
                ($year == $currentYear && $month <= $currentMonth) ||
                ($year == $currentYear - 1 && $month == 12 && $currentMonth == 1)
            ) {
                // Check if payroll has already been created for the given month/year
                $connection = Database::getConnection();
                $payrollTrackQuery = "SELECT status FROM payroll_tracks WHERE month = :month AND year = :year";
                $stmt = $connection->prepare($payrollTrackQuery);
                $stmt->execute([':month' => $month, ':year' => $year]);
                $payrollTrack = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($payrollTrack) {
                    if ($payrollTrack['status'] === 'created') {
                        echo "Payroll for this month and year has already been prepared.";
                        return;
                    } elseif ($payrollTrack['status'] === 'finalized') {
                        echo "Payroll for this month and year has already been finalized and cannot be modified.";
                        return;
                    }
                } else {
                    // If no record exists, create a new payroll track with status 'created'
                    $insertTrackQuery = "INSERT INTO payroll_tracks (month, year, status) VALUES (:month, :year, 'created')";
                    $stmt = $connection->prepare($insertTrackQuery);
                    $stmt->execute([':month' => $month, ':year' => $year]);
                }

                // Fetch data from the database for payroll processing
                $usersQuery = "SELECT id, first_name, salary, position_id FROM users";
                $users = $connection->query($usersQuery)->fetchAll(PDO::FETCH_ASSOC);

                // Initialize data for payroll processing
                foreach ($users as &$user) {
                    $attendanceQuery = "SELECT days_worked FROM attendance WHERE user_id = :user_id AND month = :month AND year = :year";
                    $stmt = $connection->prepare($attendanceQuery);
                    $stmt->execute([':user_id' => $user['id'], ':month' => $month, ':year' => $year]);

                    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);
                    $user['attendance_days'] = $attendance ? $attendance['days_worked'] : 0;

                    // Fetch the position-based addon salary
                    switch ($user['position_id']) {
                        case 1:
                            $user['position_addon'] = 3500;
                            break;
                        case 2:
                            $user['position_addon'] = 4000;
                            break;
                        case 3:
                            $user['position_addon'] = 2500;
                            break;
                        case 4:
                            $user['position_addon'] = 0;
                            break;
                        default:
                            $user['position_addon'] = 0;
                            break;
                    }

                    // Default values for pension, penalty, and bonus
                    $user['pension'] = 0;
                    $user['penalty'] = 0;
                    $user['bonus'] = 0;
                }

                // Pass data to the view
                include './app/views/payroll/payroll_prepare.php';
            } else {
                echo "Invalid month/year. Only the current or previous month is allowed.";
            }
        } else {
            // If the request method is GET, display the form for selecting month and year
            include './app/views/payroll/payroll_prepare_form.php';
        }
    }
    public function updatePayroll()
    {
        // Check if the role is allowed (role 1 is for admin/authorized user)
        if ($_SESSION['role_id'] !== 1) {
            echo "Unauthorized access.";
            return;
        }

        // Handle POST request to update payroll
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];

            // Iterate over the payroll records that need to be updated
            foreach ($_POST['payroll'] as $userId => $payrollData) {
                // Retrieve the updated data from the form
                $baseSalary = $payrollData['base_salary'];
                $positionAddon = $payrollData['position_addon'];
                $attendanceDays = $payrollData['attendance_days'];
                $pension = $payrollData['pension'];
                $penalty = $payrollData['penalty'];
                $bonus = $payrollData['bonus'];
                $netPay = $payrollData['net_pay'];

                // Update the payroll in the database
                $connection = Database::getConnection();

                $updateQuery = "
                UPDATE payroll_preparations 
                SET 
                    base_salary = :base_salary,
                    position_addon = :position_addon,
                    attendance_days = :attendance_days,
                    pension = :pension,
                    penalty = :penalty,
                    bonus = :bonus,
                    net_pay = :net_pay
                WHERE 
                    user_id = :user_id AND month = :month AND year = :year
            ";

                $stmt = $connection->prepare($updateQuery);
                $stmt->execute([
                    ':base_salary' => $baseSalary,
                    ':position_addon' => $positionAddon,
                    ':attendance_days' => $attendanceDays,
                    ':pension' => $pension,
                    ':penalty' => $penalty,
                    ':bonus' => $bonus,
                    ':net_pay' => $netPay,
                    ':user_id' => $userId,
                    ':month' => $month,
                    ':year' => $year
                ]);
            }

            // Redirect to a confirmation page or back to the payroll list
            header('Location: /payroll/list'); // Adjust to the appropriate route
            exit;
        }
    }

    public function payrolledit()
    {
        // Check if the role is allowed (role 1 is for admin/authorized user)
        if ($_SESSION['role_id'] !== 1) {
            echo "Unauthorized access.";
            return;
        }

        // Handle POST request to fetch and edit payroll
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get selected month and year from the form
            $month = $_POST['month'];
            $year = $_POST['year'];

            // Check if the month and year are valid (you can add more validation here)
            if (!in_array($month, range(1, 12))) {
                echo "Invalid month.";
                return;
            }

            if ($year < 2025 || $year > date('Y')) {
                echo "Invalid year.";
                return;
            }

            // Connect to the database and fetch payroll data for the selected month and year
            $connection = Database::getConnection();

            $payrollQuery = "
            SELECT 
                pp.user_id AS id, 
                u.first_name, 
                pp.base_salary, 
                pp.position_addon, 
                pp.attendance_days, 
                pp.pension, 
                pp.penalty, 
                pp.bonus, 
                pp.net_pay 
            FROM payroll_preparations pp
            JOIN users u ON pp.user_id = u.id
            WHERE pp.month = :month AND pp.year = :year
        ";

            $stmt = $connection->prepare($payrollQuery);
            $stmt->execute([':month' => $month, ':year' => $year]);

            $payrollRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($payrollRecords)) {
                echo "No payroll records found for the selected month and year.";
                return;
            }

            // Pass payroll records to the view
            include './app/views/payroll/payroll_edit.php';
        }
    }


    private function savePayrollUpdates()
    {
        // Validate role
        if ($_SESSION['role_id'] !== 1) {
            http_response_code(403); // Forbidden
            echo "Access denied.";
            exit();
        }

        // Connect to the database
        $conn = new mysqli('localhost', 'root', '', 'ooppayrol');
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        // Update payroll data
        foreach ($_POST['penalty'] as $userId => $penalty) {
            $bonus = $_POST['bonus'][$userId];

            $stmt = $conn->prepare("
                UPDATE payroll_preparations 
                SET penalty = ?, bonus = ? 
                WHERE user_id = ? AND month = ? AND year = ?
            ");
            $stmt->bind_param("ddiii", $penalty, $bonus, $userId, $_POST['month'], $_POST['year']);
            $stmt->execute();
            $stmt->close();
        }

        $conn->close();

        // Redirect back to the payroll page
        header("Location: /payroll/edit?month=" . $_POST['month'] . "&year=" . $_POST['year']);
        exit();
    }

    // Show the payroll editing form
    private function showPayrollForm()
    {
        $month = $_POST['month'] ?? date('m');
        $year = $_POST['year'] ?? date('Y');

        // Fetch payroll data for the given month/year
        $connection = Database::getConnection();
        $query = "
            SELECT * FROM payroll_preparations 
            WHERE month = :month AND year = :year
        ";
        $stmt = $connection->prepare($query);
        $stmt->execute([':month' => $month, ':year' => $year]);
        $payrollData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Include the view to show the payroll editing form
        include 'views/payroll_edit_form.php';
    }




    public function saveTemporary()
    {
        // Check if the role is allowed
        if ($_SESSION['role_id'] !== 1) {
            echo "Unauthorized access.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];

            // Database connection
            $connection = Database::getConnection();

            // Loop through each user to update pension, penalty, bonus, and net pay
            foreach ($_POST['penalty'] as $userId => $penalty) {
                $bonus = $_POST['bonus'][$userId];

                // Fetch user details from the database
                $userQuery = "SELECT salary, position_id FROM users WHERE id = :user_id";
                $stmt = $connection->prepare($userQuery);
                $stmt->execute([':user_id' => $userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Fetch attendance days for the user in the specified month and year
                $attendanceQuery = "SELECT days_worked FROM attendance WHERE user_id = :user_id AND month = :month AND year = :year";
                $stmt = $connection->prepare($attendanceQuery);
                $stmt->execute([':user_id' => $userId, ':month' => $month, ':year' => $year]);
                $attendance = $stmt->fetch(PDO::FETCH_ASSOC);
                $attendanceDays = $attendance ? $attendance['days_worked'] : 0;

                // Determine position addon based on position_id
                switch ($user['position_id']) {
                    case 1:
                        $addon = 3500;
                        break;
                    case 2:
                        $addon = 4000;
                        break;
                    case 3:
                        $addon = 2500;
                        break;
                    case 4:
                        $addon = 0;
                        break;
                    default:
                        $addon = 0;
                        break;
                }

                // Calculate pension as 15% of the base salary
                $pension = $user['salary'] * 0.15;

                // Net pay calculation
                $netPay = ($user['salary'] + $addon) * $attendanceDays - $penalty + $bonus - $pension;

                // Insert payroll data into payroll_preparation table
                $insertQuery = "INSERT INTO payroll_preparations 
                (user_id, month, year, base_salary, position_addon, attendance_days, pension, penalty, bonus, net_pay) 
                VALUES 
                (:user_id, :month, :year, :base_salary, :position_addon, :attendance_days, :pension, :penalty, :bonus, :net_pay)";

                $stmt = $connection->prepare($insertQuery);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':month' => $month,
                    ':year' => $year,
                    ':base_salary' => $user['salary'],
                    ':position_addon' => $addon,
                    ':attendance_days' => $attendanceDays,
                    ':pension' => $pension,
                    ':penalty' => $penalty,
                    ':bonus' => $bonus,
                    ':net_pay' => $netPay
                ]);
            }

            echo "Payroll data saved successfully!";
        }
    }
    public function finalizePayroll()
    {
        if ($_SESSION['role_id'] !== 1) { // Role 1: HR Manager
            echo "Unauthorized access.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];

            $connection = Database::getConnection();

            // Check payroll status
            $payrollTrackQuery = "SELECT status FROM payroll_tracks WHERE month = :month AND year = :year";
            $stmt = $connection->prepare($payrollTrackQuery);
            $stmt->execute([':month' => $month, ':year' => $year]);
            $payrollTrack = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$payrollTrack) {
                echo "No payroll available for finalization for this month and year.";
                return;
            }

            if ($payrollTrack['status'] === 'finalized') {
                echo "Payroll for this month and year has already been finalized.";
                return;
            }

            if ($payrollTrack['status'] === 'created') {
                // Fetch data from preparation table
                $prepareQuery = "
                SELECT p.user_id, u.first_name, p.base_salary, p.position_addon, 
                       p.pension, p.penalty, p.bonus, p.net_pay 
                FROM payroll_preparations p
                INNER JOIN users u ON p.user_id = u.id
                WHERE p.month = :month AND p.year = :year
            ";
                $stmt = $connection->prepare($prepareQuery);
                $stmt->execute([':month' => $month, ':year' => $year]);
                $payrollDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$payrollDetails) {
                    echo "No payroll preparation data found for this month and year.";
                    return;
                }

                // Display the payroll details for review
                include './app/views/payroll/payroll_finalize_review.php';
                return;
            }
        } else {
            // Display form to select month and year
            include './app/views/payroll/payroll_finalize_form.php';
        }
    }

    public function confirmFinalizePayroll()
    {
        if ($_SESSION['role_id'] !== 1) { // Role 2: HR Manager
            echo "Unauthorized access.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];

            // Connect to the database
            $connection = Database::getConnection();

            try {
                // Begin transaction
                $connection->beginTransaction();

                // Fetch data from payroll_preparations table
                $fetchQuery = "
                SELECT user_id, base_salary, position_addon, attendance_days, 
                       pension, penalty, bonus, net_pay
                FROM payroll_preparations
                WHERE month = :month AND year = :year
            ";
                $stmt = $connection->prepare($fetchQuery);
                $stmt->execute([':month' => $month, ':year' => $year]);
                $payrollData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($payrollData)) {
                    echo "No payroll data found for finalization for $month/$year.";
                    $connection->rollBack();
                    return;
                }

                // Insert data into payslips table
                $insertQuery = "
                INSERT INTO payslips (user_id, month, year, base_salary, position_addon, 
                                      attendance_days, pension, penalty, bonus, net_pay)
                VALUES (:user_id, :month, :year, :base_salary, :position_addon, 
                        :attendance_days, :pension, :penalty, :bonus, :net_pay)
            ";
                $insertStmt = $connection->prepare($insertQuery);

                foreach ($payrollData as $row) {
                    $insertStmt->execute([
                        ':user_id' => $row['user_id'],
                        ':month' => $month,
                        ':year' => $year,
                        ':base_salary' => $row['base_salary'],
                        ':position_addon' => $row['position_addon'],
                        ':attendance_days' => $row['attendance_days'],
                        ':pension' => $row['pension'],
                        ':penalty' => $row['penalty'],
                        ':bonus' => $row['bonus'],
                        ':net_pay' => $row['net_pay'],
                    ]);
                }

                // Delete data from payroll_preparations table
                $deleteQuery = "
                DELETE FROM payroll_preparations
                WHERE month = :month AND year = :year
            ";
                $deleteStmt = $connection->prepare($deleteQuery);
                $deleteStmt->execute([':month' => $month, ':year' => $year]);

                // Update payroll_tracks table to set status to 'finalized'
                $finalizeQuery = "
                UPDATE payroll_tracks 
                SET status = 'finalized' 
                WHERE month = :month AND year = :year
            ";
                $finalizeStmt = $connection->prepare($finalizeQuery);
                $finalizeStmt->execute([':month' => $month, ':year' => $year]);

                // Commit transaction
                $connection->commit();

                echo "Payroll for $month/$year has been successfully finalized.";
            } catch (Exception $e) {
                // Rollback on error
                $connection->rollBack();
                echo "Error finalizing payroll: " . $e->getMessage();
            }
        } else {
            echo "Invalid request method.";
        }
    }

    public function Addemployee()
    {


        // Load the view with the payslip data
        include('./app/views/employee/Addemp.php');
    }
    public function viewprofile()
    {


        // Load the view with the payslip data
        include('./app/views/employee/view_profile.php');
    }

    public function AssignPosition()
    {


        // Load the view with the payslip data
        include('./app/views/employee/addposition.php');
    }
    public function EditPosition()
    {


        // Load the view with the payslip data
        include('./app/views/employee/editposition.php');
    }


    // Method to add a new employee
    public function postAddemployee()
    {
        // Assuming POST data has been sent
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and get input values
            $firstName = $_POST['FirstName'];
            $lastName = $_POST['LastName'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $salary = $_POST['salary'];
            $phoneNumber = $_POST['phoneNumber'];
            $gender = $_POST['gender'];
            $dob = $_POST['dob'];
            $position = $_POST['position'];
            $placeOfBirth = $_POST['place_of_birth'];
            $roleId = $_POST['role_id'];

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert into the database
            $db = Database::getConnection();
            $sql = "INSERT INTO users (first_name, last_name, username, password, Salary, gender, position_id, account_status, dob, place_of_birth, role_id, phonenumber)
        VALUES (:first_name, :last_name, :username, :password, :salary, :gender, :position_id, 'active', :dob, :place_of_birth, :role_id, :phonenumber)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':salary', $salary);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':position_id', $position);
            $stmt->bindParam(':dob', $dob);
            $stmt->bindParam(':place_of_birth', $placeOfBirth);
            $stmt->bindParam(':role_id', $roleId);
            $stmt->bindParam(':phonenumber', $phoneNumber);

            if ($stmt->execute()) {
                echo "User added successfully! ";
                echo "<a href='/Add_employee'>Add Another Employee</a> | ";
                echo "<a href='/hrp/dashboard'>Go to Dashboard</a>";
            } else {
                echo "Error adding user. ";
                echo "<a href='/hrp/dashboard'>Go to Dashboard</a>";
            }
        }
    }


    // Assign a role and position to an employee


    // Edit an employee's role and position
    public function editRoleAndPosition($userId = null, $roleId = null, $positionId = null)
    {
        if ($userId === null || $roleId === null || $positionId === null) {
            echo "Error: Missing required parameters to update role and position.";
            return;
        }

        try {
            $db = Database::getConnection();
            $sql = "UPDATE users SET role_id = :role_id, position_id = :position_id WHERE id = :user_id";
            $stmt = $db->prepare($sql);

            // Bind parameters to the query
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
            $stmt->bindParam(':position_id', $positionId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "Role and position updated successfully!";
            } else {
                echo "Failed to update role and position.";
            }
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
    public function listEmployeesBySchool()
    {
        // Ensure the user is logged in and their school ID is available in the session
        if (!isset($_SESSION['sch_id'])) {
            echo "Access denied. School ID is not set.";
            return;
        }

        $school_id = $_SESSION['sch_id']; // Use the school ID from the session

        try {
            $connection = Database::getConnection();

            // Fetch employees of the specific school
            $sql = "SELECT id, first_name FROM users WHERE sch_id = :school_id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([':school_id' => $school_id]);

            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($employees)) {
                echo "No employees found for your school.";
                return;
            }

            // Pass data to the view
            require_once __DIR__ . '/../views/attendance/list-employees.php';
        } catch (\PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }




    public function store()
    {
        // Ensure data is present
        if (!isset($_POST['user_id'], $_POST['days_worked'], $_POST['month'], $_POST['year'])) {
            echo "Invalid input.";
            return;
        }

        $user_id = $_POST['user_id'];
        $days_worked = $_POST['days_worked'];
        $month = $_POST['month'];
        $year = $_POST['year'];

        $current_month = date('m');
        $current_year = date('Y');

        // Validate month and year
        if ($year < $current_year || ($year == $current_year && $month < $current_month)) {
            echo "Cannot add attendance for past months.";
            return;
        }

        if ($year > $current_year || ($year == $current_year && $month > $current_month)) {
            echo "Cannot add attendance for future months.";
            return;
        }

        try {
            $connection = Database::getConnection();

            // Check if attendance already exists
            $sql = "SELECT id FROM attendance WHERE user_id = :user_id AND month = :month AND year = :year";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':month' => $month,
                ':year' => $year,
            ]);

            if ($stmt->rowCount() > 0) {
                echo "Attendance already exists.";
                return;
            }

            // Insert attendance
            $sql = "INSERT INTO attendance (user_id, days_worked, month, year) VALUES (:user_id, :days_worked, :month, :year)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':days_worked' => $days_worked,
                ':month' => $month,
                ':year' => $year,
            ]);

            echo "Attendance added successfully.";
        } catch (\PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function editAttendance()
    {
        if (!isset($_GET['attendance_id'])) {
            echo "Attendance ID is required.";
            return;
        }

        $attendance_id = $_GET['attendance_id'];

        try {
            $connection = Database::getConnection();

            // Check if the request is a POST (update action)
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Get current month and year
                $currentMonth = date('m');
                $currentYear = date('Y');

                // Validate the provided month and year
                if ($_POST['month'] != $currentMonth || $_POST['year'] != $currentYear) {
                    echo "You can only edit attendance for the current month.";
                    return;
                }

                // Update attendance record
                $sql = "UPDATE attendance 
                    SET days_worked = :days_worked 
                    WHERE id = :id AND month = :month AND year = :year";
                $stmt = $connection->prepare($sql);
                $stmt->execute([
                    ':days_worked' => $_POST['days_worked'],
                    ':month' => $currentMonth,
                    ':year' => $currentYear,
                    ':id' => $attendance_id,
                ]);

                echo "Attendance updated successfully.";
                return;
            }

            // Fetch the current attendance data for editing
            $sql = "SELECT * FROM attendance WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([':id' => $attendance_id]);
            $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$attendance) {
                echo "Attendance record not found.";
                return;
            }

            // Pass attendance data to the view
            require_once __DIR__ . '/../views/attendance/edit-attendance.php';
        } catch (\PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function viewAttendance()
    {
        if (!isset($_SESSION['sch_id'])) {
            echo "Access denied. School ID is not set.";
            return;
        }

        $school_id = $_SESSION['sch_id'];

        try {
            $connection = Database::getConnection();

            // Fetch attendance records for the logged-in school dean's school
            $sql = "SELECT a.id, first_name AS employee_name, a.days_worked, a.month, a.year
                FROM attendance a
                INNER JOIN users u ON a.user_id = u.id
                WHERE u.sch_id = :school_id
                ORDER BY a.year DESC, a.month DESC";
            $stmt = $connection->prepare($sql);
            $stmt->execute([':school_id' => $school_id]);

            $attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($attendances)) {
                echo "No attendance records found for your school.";
                return;
            }

            // Pass attendance data to the view
            require_once __DIR__ . '/../views/attendance/view-attendance.php';
        } catch (\PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }


    public function postEditUser()
    {
        // Assuming session is already started
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                echo "Session expired or user not logged in.";
                exit;
            }

            // Fetch `user_id` from session
            $userId = $_SESSION['user_id'];

            // Sanitize and retrieve input values
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $currentPassword = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_STRING);
            $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);

            // Ensure inputs are provided
            if (empty($username) || empty($currentPassword) || empty($newPassword)) {
                echo "All fields are required.";
                exit;
            }

            // Database connection
            $db = Database::getConnection();

            // Fetch the current user details
            $sql = "SELECT username, password FROM users WHERE id = :userId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo "User not found.";
                exit;
            }

            // Verify the current password
            if (!password_verify($currentPassword, $user['password'])) {
                echo "Current password is incorrect.";
                exit;
            }

            // Check if the new username is the same as the previous one
            if ($username === $user['username']) {
                echo "The new username must be different from the current username.";
                exit;
            }

            // Check if the new password is the same as the current one
            if (password_verify($newPassword, $user['password'])) {
                echo "The new password must be different from the current password.";
                exit;
            }

            // Check if the username is unique
            $sql = "SELECT id FROM users WHERE username = :username AND id != :userId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "The username is already taken.";
                exit;
            }

            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update the user details
            $sql = "UPDATE users SET username = :username, password = :password WHERE id = :userId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':userId', $userId);

            if ($stmt->execute()) {
                echo "User details updated successfully!";
                echo "<a href='/viewprofile'>Go Back to Profile</a>";
            } else {
                echo "Error updating user details.";
            }
        }
    }


    public function loadAttendance()
    {


        include 'app/views/attendance/attendance.php';
    }

    public function loadAttendanceAdd()
    {
        include 'app/views/attendance/attendanceAdd.php';
    }

    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Display payroll data for a specific employee
    public function viewPayroll($userId, $month, $year)
    {
        // Query to get payroll information for the specific employee and month/year
        $query = "SELECT * FROM payslips WHERE user_id = :user_id AND MONTH(created_at) = :month AND YEAR(created_at) = :year";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userId, 'month' => $month, 'year' => $year]);

            $payroll = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($payroll) {
                return $payroll;
            } else {
                return "No payroll data found for the given period.";
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }


    // Add a new attendance record for an employee (Only school head can do this)
    public function addAttendance($userId, $month, $year, $daysWorked)
    {
        // Get the school head's role (role check is done here)
        $role = $this->getUserRole($userId);

        if ($role !== 'School Head') {
            return "Only School Head can fill attendance records.";
        }

        // Check if payroll for this user, month, and year already exists
        $existingPayroll = $this->viewPayroll($userId, $month, $year);

        if (is_array($existingPayroll)) {
            return "Payroll already exists for this employee for the given month and year.";
        }

        // Calculate salary deduction based on days worked
        $employeeSalary = $this->getEmployeeSalary($userId);
        $totalWorkingDays = 30; // For example, a month has 30 days
        $deduction = ($totalWorkingDays - $daysWorked) * ($employeeSalary / $totalWorkingDays);

        // Insert attendance and calculate net pay
        $netPay = $employeeSalary - $deduction;

        $query = "INSERT INTO payslips (user_id, salary, deduction, net_pay, created_at)
                  VALUES (:user_id, :salary, :deduction, :net_pay, NOW())";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'user_id' => $userId,
                'salary' => $employeeSalary,
                'deduction' => $deduction,
                'net_pay' => $netPay
            ]);

            return "Attendance record and payroll updated successfully.";
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    // Helper method to get employee role
    private function getUserRole($userId)
    {
        $query = "SELECT role FROM users WHERE id = :user_id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userId]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ? $user['role'] : null;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    // Helper method to get the employee salary
    private function getEmployeeSalary($userId)
    {
        $query = "SELECT salary FROM employees WHERE user_id = :user_id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userId]);

            $employee = $stmt->fetch(PDO::FETCH_ASSOC);

            return $employee ? $employee['salary'] : 0;
        } catch (PDOException $e) {
            return 0; // In case of error, return 0 salary
        }
    }
}
