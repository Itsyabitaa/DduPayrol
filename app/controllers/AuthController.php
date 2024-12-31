<?php

// namespace App\Controllers;

namespace App\Controllers;

require_once __DIR__ . '/../core/View.php';

use App\Core\View;
use App\Model\Employee;


class AuthController
{
    public function showLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // Verify credentials with the database
            $employeeModel = new Employee();
            $employee = $employeeModel->getEmployeeByUsername($username);


            // Assuming $employee is fetched from the database
            if ($employee && password_verify($password, $employee['password'])) {
                // Prevent session fixation attacks
                session_regenerate_id(true);

                // Store employee details in session
                $_SESSION['employee_id'] = $employee['id'];

                // Redirect to the dashboard
                header("Location: /../app/views/dashboard.php");
                exit();
            } else {
                echo "Invalid credentials. Please try again.";
                // Consider adding a delay to mitigate brute-force attacks
                sleep(2);
            }


            // Load login view if not POST request
            View::render('login');
        }
    }
}
