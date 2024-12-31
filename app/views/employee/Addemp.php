<?php
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['username']) || !isset($_SESSION['role_id']) || (6 !== ($_SESSION['role_id']))) {
    include './index.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
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
            padding: 20px;
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
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2980b9;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 16px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }

        .form-group select {
            appearance: none;
        }

        .form-group button {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 15px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: #1abc9c;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="/Hrm/dashboard">Home</a></li>
                <li><a href="/viewprofile">editprofile</a></li>
                <li><a href="/viewpayslip">View Payslip</a></li>
                <li><a href="/Add_employee">Add Employee</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="profile-area">
                    <button class="logout-btn"><a href='/logout' style="text-decoration: none; color: #fff;">Logout</a></button>
                </div>
            </header>
            <div class="content">
                <h1>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

                <!-- Add User Form -->
                <div class="form-container">
                    <h2>Add New User</h2>
                    <form action="/PostAdd_employee" method="POST">
                        <!-- First Name -->
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="FirstName" required>
                        </div>

                        <!-- Last Name -->
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="LastName" required>
                        </div>

                        <!-- Username -->
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <!-- Level (Role ID) -->
                        <div class="form-group">
                            <label for="Level">Level</label>
                            <select id="Level" name="Level" required onchange="updateRoleId()">
                                <option value="">Select Level</option>
                                <option value="1">High</option>
                                <option value="2">Medium</option>
                                <option value="3">low</option>
                                <!-- Other roles can be added as needed -->
                            </select>
                        </div>

                        <!-- Salary -->
                        <div class="form-group">
                            <label for="salary">Salary</label>
                            <input type="number" id="salary" name="Salary" required>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group">
                            <label for="phoneNumber">Phone Number</label>
                            <input type="text" id="phoneNumber" name="phoneNumber" required>
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Date of Birth (dob) -->
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" required>
                        </div>

                        <!-- Position -->
                        <div class="form-group">
                            <label for="position">Position</label>
                            <select id="position" name="position" required>
                                <option value="">Select Position</option>
                                <option value="1">Head</option>
                                <option value="2">Dean</option>
                                <option value="3">Advisor</option>
                                <option value="4">Employee</option>
                            </select>
                        </div>

                        <!-- Place of Birth -->
                        <div class="form-group">
                            <label for="place_of_birth">Place of Birth</label>
                            <input type="text" id="place_of_birth" name="place_of_birth" required>
                        </div>

                        <!-- Hidden role_id field -->
                        <input type="hidden" id="role_id" name="role_id" value="">

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit">Add User</button>
                        </div>
                    </form>
                </div>

                <script>
                    // JavaScript function to set role_id based on the selected level
                    function updateRoleId() {
                        var level = document.getElementById('Level').value;
                        var roleIdField = document.getElementById('role_id');
                        // Set role_id based on selected level
                        if (level == "1") {
                            roleIdField.value = 3; // Employee role
                        } else if (level == "2") {
                            roleIdField.value = 3; // Director role
                        } else {
                            roleIdField.value = 3; // Empty if no level selected
                        }
                    }
                </script>
</body>

</html>