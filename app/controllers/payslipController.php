<?php

include('./app/autoloader.php');
include('/xampp/htdocs/oop_pay/app/core/Database.php');

use App\Core\Database;

class PayslipController
{
    // Display all payslips
    public function index()
    {
        $db = Database::getConnection();
        $sql = "SELECT users.id AS user_id, users.first_name, users.last_name, payslips.salary, payslips.deduction, 
                payslips.pension, payslips.bonus, payslips.addons, payslips.overtime, payslips.allowance, payslips.net_pay, payslips.created_at
                FROM payslips
                INNER JOIN users ON payslips.user_id = users.id";
        $stmt = $db->query($sql);
        $payslips = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include 'app/views/payslips.php';
    }

    // Generate payslip for a specific user
    public function generatePayslip($userId)
    {
        // Example: Fetch user salary details and generate a payslip
        $db = Database::getConnection();

        // Use parameterized query
        $sql = "
        SELECT u.first_name, u.salary, p.deductions, p.pension, p.bonus, p.addons, p.overtime, p.allowance, p.net_pay, p.created_at
        FROM users u
        LEFT JOIN payslips p ON u.id = p.user_id
        WHERE u.id = :user_id
    ";

        $stmt = $db->prepare($sql);

        // Bind the user_id parameter
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                // Calculate net pay (if needed, if not stored in DB)
                $netPay = $data['salary'] + $data['bonus'] + $data['addons'] + $data['overtime'] + $data['allowance'] - $data['deductions'];

                // Prepare the payslip data array
                $payslipData = [
                    'employee_name' => $data['first_name'],
                    'salary' => number_format($data['salary'], 2),
                    'deductions' => number_format($data['deductions'], 2),
                    'pension' => number_format($data['pension'], 2),
                    'bonus' => number_format($data['bonus'], 2),
                    'addons' => number_format($data['addons'], 2),
                    'overtime' => number_format($data['overtime'], 2),
                    'allowance' => number_format($data['allowance'], 2),
                    'net_pay' => number_format($netPay, 2),
                    'created_at' => date("F j, Y", strtotime($data['created_at'])) // Format date nicely
                ];

                // Send the payslip data to the view
                return $this->renderPayslipView($payslipData);  // This function should render the payslip view (e.g., a Blade template)
            } else {
                // If no user is found
                return $this->renderErrorView("No user found with ID $userId.");
            }
        } else {
            // If the query fails
            return $this->renderErrorView("Failed to generate payslip.");
        }
    }

    // Function to render payslip view (example)
    private function renderPayslipView($data)
    {

        // Here you can use your framework's view rendering system. For simplicity:
        include './app/views/employee/view_payslip.php'; // You can use any template engine like Blade or Twig
    }

    // Function to render error message (example)
    private function renderErrorView($message)
    {
        // Here you can send error messages to a view
        echo "<h3>Error:</h3><p>$message</p>";
    }
}
