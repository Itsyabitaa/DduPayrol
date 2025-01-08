<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payslip</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #f4f6f8;
            color: #333;
            font-size: 16px;
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
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
            width: 100%;
        }

        .sidebar ul {
            list-style: none;
            margin-top: 20px;
            width: 100%;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            flex: 1;
            background-color: #f4f6f8;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            padding: 30px;
        }

        .header {
            background-color: #1e5799;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-area button {
            margin-left: 10px;
            background-color: white;
            color: #1e5799;
            border: 1px solid white;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-area button:hover {
            background-color: #1abc9c;
            color: white;
        }

        .content {
            padding: 30px;
        }

        .content h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .payslip-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .payslip-table th,
        .payslip-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #f1f1f1;
        }

        .payslip-table th {
            background: linear-gradient(135deg, #1e5799, #7db9e8);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .payslip-table tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .payslip-table td {
            color: #555;
        }

        .no-payslip {
            text-align: center;
            margin-top: 50px;
            font-size: 18px;
            color: #999;
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
            <header class="header">
                <div class="profile-area">
                    <button class="profile-btn">Profile</button>
                    <button class="logout-btn"><a href='/logout' style="color: inherit; text-decoration: none;">Logout</a></button>
                </div>
            </header>

            <div class="content">
                <h1>Payslips</h1>
                <?php
                if ($data) {
                    echo "
        <table class='payslip-table'>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Salary</th>
                    <th>Deduction</th>
                    <th>Pension</th>
                    <th>Bonus</th>
                    <th>Addons</th>
                    <th>Overtime</th>
                    <th>Allowance</th>
                    <th>Net Pay</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{$data['employee_name']}</td>
                    <td>{$data['salary']}</td>
                    <td>{$data['deductions']}</td>
                    <td>{$data['pension']}</td>
                    <td>{$data['bonus']}</td>
                    <td>{$data['addons']}</td>
                    <td>{$data['overtime']}</td>
                    <td>{$data['allowance']}</td>
                    <td>{$data['net_pay']}</td>
                    <td>{$data['date']}</td>
                </tr>
            </tbody>
        </table>
        ";
                } else {
                    echo "<p class='no-payslip'>No payslip found for this employee.</p>";
                }
                ?>
            </div>

        </main>
    </div>
</body>

</html>