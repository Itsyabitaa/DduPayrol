<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign/Edit Roles and Positions</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .dashboard {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            margin-top: 50px;
            width: 100%;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #ecf0f1;
            font-size: 18px;
            transition: color 0.3s;
        }

        .sidebar ul li a:hover {
            color: #1abc9c;
        }

        .main-content {
            flex: 1;
            background-color: #ecf0f1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #2980b9;
            padding: 10px 20px;
            color: #fff;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .profile-area button {
            margin-left: 10px;
            background-color: #fff;
            color: #2980b9;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .profile-area button:hover {
            background-color: #1abc9c;
            color: #fff;
        }

        .content {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        form h3 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 22px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #34495e;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #1abc9c;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #1abc9c;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #16a085;
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
                <li><a href="/viewpayslip">View My Payslip</a></li>
                <li><a href="/viewprofile">Edit Profile</a></li>
                <li><a href="/edit_position">Edit Position</a></li>
                <li><a href="/Assign_position">Add New Position</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="profile-area">
                    <button class="profile-btn">Profile</button>
                    <button class="logout-btn"><a href='/logout'>Logout</a></button>
                </div>
            </header>
            <div class="content">
                <form action="/edit_role_position" method="POST">
                    <h3>Edit Employee Role and Position</h3>
                    <label for="user_id_edit">Employee ID:</label>
                    <input type="text" name="user_id_edit" id="user_id_edit" placeholder="Enter Employee ID" required>

                    <label for="role_id_edit">Select Role:</label>
                    <select name="role_id_edit" id="role_id_edit" required>
                        <option value="1">Payroll Analyst</option>
                        <option value="2">HR Manager</option>
                        <option value="3">Employee</option>
                        <option value="4">Director</option>
                        <option value="5">School Dean</option>
                        <option value="6">HR Personnel</option>
                    </select>

                    <label for="position_id_edit">Select Position:</label>
                    <select name="position_id_edit" id="position_id_edit" required>
                        <option value="1">Dean</option>
                        <option value="2">Head</option>
                        <option value="3">Advisor</option>
                        <option value="4">Employee</option>
                    </select>

                    <input type="submit" value="Edit Role and Position">
                </form>
            </div>
        </main>
    </div>
</body>

</html>