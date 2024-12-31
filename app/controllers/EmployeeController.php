<?php

include './app/autoloader.php';
include('/xampp/htdocs/oop_pay/app/core/Database.php');
include("./app/autoloader.php");

use App\Core\Database;

class EmployeeController
{
    private $employeeModel;

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
            $salary = $_POST['Salary'];
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
            $sql = "INSERT INTO users (first_name, last_name, username, password, salary, gender, position_id, account_status, dob, place_of_birth, role_id, phonenumber)
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
}
