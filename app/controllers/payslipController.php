<?php

class PayslipController
{
    public function generatePayslip($request)
    {
        $userId = $request['user_id'];
        $month = $request['month'];
        $year = $request['year'];

        // Step 1: Validate the data
        $validationResult = $this->validatePayslipData($request);
        if ($validationResult !== true) {
            return $this->jsonResponse('error', $validationResult);
        }

        // Step 2: Fetch user details
        $user = $this->getUser($userId);
        if (!$user) {
            return $this->jsonResponse('error', 'User not found');
        }

        // Step 3: Fetch attendance for the month
        $attendance = $this->getAttendance($userId, $month, $year);
        if (!$attendance) {
            return $this->jsonResponse('error', 'Attendance record not found');
        }

        // Step 4: Check if payslip can be generated for past/future months
        $validMonth = $this->validatePayslipMonth($month, $year);
        if ($validMonth !== true) {
            return $this->jsonResponse('error', $validMonth);
        }

        // Step 5: Calculate the net salary
        $netSalary = $this->calculateNetSalary($user['salary'], $attendance['days_worked'], $month);

        // Step 6: Save the payslip
        $this->savePayslip($userId, $month, $year, $user['salary'], $netSalary);

        return $this->jsonResponse('success', 'Payslip generated successfully', ['net_salary' => $netSalary]);
    }

    // Validate payslip data
    private function validatePayslipData($request)
    {
        if (empty($request['user_id']) || !is_numeric($request['month']) || !is_numeric($request['year'])) {
            return 'Invalid data provided';
        }
        return true;
    }

    // Get user details from the database
    private function getUser($userId)
    {
        // Implement database query to fetch user data (dummy response here)
        return ['id' => $userId, 'salary' => 1000];
    }

    // Get attendance for the month from the database
    private function getAttendance($userId, $month, $year)
    {
        // Implement database query to fetch attendance (dummy response here)
        return ['user_id' => $userId, 'days_worked' => 25];
    }

    // Validate whether payslip can be generated for the requested month
    private function validatePayslipMonth($month, $year)
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        if ($month < $currentMonth || ($month == $currentMonth && $year < $currentYear)) {
            return 'Cannot generate payslip for past months';
        }

        if ($month > $currentMonth || ($month == $currentMonth && $year > $currentYear)) {
            return 'Cannot generate payslip for future months';
        }

        return true;
    }

    // Calculate the net salary based on days worked
    private function calculateNetSalary($grossSalary, $daysWorked, $month)
    {
        $totalDaysInMonth = 30; // You can adjust this per month
        $workedPercentage = $daysWorked / $totalDaysInMonth;
        return $grossSalary * $workedPercentage;
    }

    // Save the payslip in the database
    private function savePayslip($userId, $month, $year, $grossSalary, $netSalary)
    {
        // Implement database query to save payslip (dummy logic here)
    }

    // Return JSON response
    private function jsonResponse($status, $message, $data = [])
    {
        return json_encode(array_merge([$status => $message], $data));
    }
}
