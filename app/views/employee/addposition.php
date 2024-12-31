<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .dashboard {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s;
        }

        .sidebar ul li a:hover {
            color: #1abc9c;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #ecf0f1;
        }

        .main-content h1 {
            margin-bottom: 20px;
        }

        .employee-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .employee-table th,
        .employee-table td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .employee-table th {
            background-color: #2c3e50;
            color: white;
        }

        .employee-table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="/Director/dashboard">HOME</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Employees</h1>
            <?php if (!empty($users)): ?>
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $users): ?>
                            <tr>
                                <td><?= htmlspecialchars($users['id']) ?></td>
                                <td><?= htmlspecialchars($users['first_name']) ?></td>
                                <td><?= htmlspecialchars($users['phoneNumber']) ?></td>
                                <td><?= htmlspecialchars($users['role_id']) ?></td>
                                <td><?= htmlspecialchars($users['salary']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No employees found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>