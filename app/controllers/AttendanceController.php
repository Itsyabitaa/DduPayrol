<?php

namespace App\Controllers;

// include './app/autoloader.php';
//include('/xampp/htdocs/oop_pay/app/core/Database.php');
//include("./app/autoloader.php");


use App\Core\Database;

class AttendanceController
{
    // Show the form to add attendance
    public function showForm()
    {
        require_once 'views/attendance/add-attendance.php';
    }

    // Store attendance
    public function store()
    {
        // Get the POST data
        $user_id = $_POST['user_id'];
        $days_worked = $_POST['days_worked'];
        $month = $_POST['month'];
        $year = $_POST['year'];

        // Get the current month and year
        $current_month = date('m'); // Current month (01-12)
        $current_year = date('Y');  // Current year (YYYY)

        // Validate the month and year (only allow current month)
        if ($year < $current_year || ($year == $current_year && $month < $current_month)) {
            echo "You cannot add attendance for past months.";
            return;
        }

        if ($year > $current_year || ($year == $current_year && $month > $current_month)) {
            echo "You cannot add attendance for future months.";
            return;
        }

        try {
            // Get the database connection
            $connection = Database::getConnection();

            // Check if attendance already exists for the user, month, and year
            $sql_check = "SELECT * FROM attendance WHERE user_id = :user_id AND month = :month AND year = :year";
            $stmt_check = $connection->prepare($sql_check);
            $stmt_check->execute([
                ':user_id' => $user_id,
                ':month' => $month,
                ':year' => $year,
            ]);

            if ($stmt_check->rowCount() > 0) {
                // Attendance already exists
                echo "Attendance for this user and month already exists.";
                return;
            }

            // Insert the attendance record
            $sql_insert = "INSERT INTO attendance (user_id, days_worked, month, year) VALUES (:user_id, :days_worked, :month, :year)";
            $stmt_insert = $connection->prepare($sql_insert);
            $stmt_insert->execute([
                ':user_id' => $user_id,
                ':days_worked' => $days_worked,
                ':month' => $month,
                ':year' => $year,
            ]);

            echo "Attendance successfully added for user ID: $user_id.";
        } catch (\PDOException $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }
}
