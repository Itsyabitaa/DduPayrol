<?php

namespace App\Model;

require_once __DIR__ . '/../Core/Database.php'; // Include the Database class
use App\Core\Database;
use PDO;

class Employee
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();  // Using the Database class to get the connection
    }

    public function getEmployeeByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM  users  WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Get employee by ID
    public function getEmployeeById($employeeId)
    {
        $query = "SELECT * FROM users WHERE id = :employeeId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employeeId', $employeeId, PDO::PARAM_INT); // Bind as integer
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get employee payslip
    public function getPayslip($employeeId)
    {
        $query = "SELECT * FROM salary WHERE employee_id = :employeeId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employeeId', $employeeId, PDO::PARAM_INT); // Bind as integer
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update employee profile
    public function updateProfile($employeeId, $data)
    {
        $query = "UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :employeeId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':employeeId', $employeeId, PDO::PARAM_INT); // Bind as integer
        return $stmt->execute();
    }

    // Submit leave request
    public function submitLeaveRequest($employeeId, $reason)
    {
        $query = "INSERT INTO leave_requests (employee_id, reason, status) VALUES (:employeeId, :reason, 'Pending')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employeeId', $employeeId, PDO::PARAM_INT); // Bind as integer
        $stmt->bindParam(':reason', $reason);
        return $stmt->execute();
    }

    public function verifyCredentials($username, $password)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user exists, verify password

        if ($user && $password === $user['password']) {
            return $user; // Return user data if login is successful
            // Return user data if login is successful
        }

        return false; // Invalid credentials

    }
}
