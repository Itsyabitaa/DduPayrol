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
        if (!isset($_GET['user_id']) || !isset($_GET['month']) || !isset($_GET['year'])) {
            echo "User ID, month, and year are required.";
            return;
        }

        $user_id = $_GET['user_id'];
        $month = $_GET['month'];
        $year = $_GET['year'];

        try {
            $connection = Database::getConnection();

            // Check if payslip already exists
            $sql = "SELECT * FROM payslip WHERE user_id = :user_id AND month = :month AND year = :year";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':month' => $month,
                ':year' => $year,
            ]);

            if ($stmt->rowCount() > 0) {
                echo "Payslip for this month already exists.";
                return;
            }

            // Get user details
            $sql = "SELECT salary FROM users WHERE id = :user_id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo "User not found.";
                return;
            }

            $baseSalary = $user['salary'];

            // Get attendance details
            $sql = "SELECT days_worked FROM attendance WHERE user_id = :user_id AND month = :month AND year = :year";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':month' => $month,
                ':year' => $year,
            ]);
            $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$attendance) {
                echo "Attendance record not found.";
                return;
            }

            $daysWorked = $attendance['days_worked'];

            // Assume total working days in the month is 30
            $totalWorkingDays = 30;
            $dailySalary = $baseSalary / $totalWorkingDays;

            // Calculate salary components
            $grossSalary = $dailySalary * $daysWorked;
            $allowances = 500; // Example fixed allowance
            $deductions = 200; // Example fixed deduction
            $netSalary = $grossSalary + $allowances - $deductions;

            // Insert payslip into database
            $sql = "INSERT INTO payslip (user_id, month, year, gross_salary, net_salary, deductions, allowances) 
                VALUES (:user_id, :month, :year, :gross_salary, :net_salary, :deductions, :allowances)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':month' => $month,
                ':year' => $year,
                ':gross_salary' => $grossSalary,
                ':net_salary' => $netSalary,
                ':deductions' => $deductions,
                ':allowances' => $allowances,
            ]);

            echo "Payslip generated successfully.";
        } catch (\PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }


    // Display payslip
    public function viewPayslip()
    {


        // Load the view with the payslip data
        include('./app/views/employee/view_payslip.php');
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

    public function editpayroll()
    {

        include 'app/views/PA/EDITPAY.php';
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
