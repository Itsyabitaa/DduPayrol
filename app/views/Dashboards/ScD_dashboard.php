<?php


// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['username']) || !isset($_SESSION['role_id']) || (5 !== ($_SESSION['role_id']))) {
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
            /* Move the options further down */
            width: 100%;
        }

        .sidebar ul li {
            margin: 20px 0;
            /* Increase the space between options */
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
        }
    </style>

</head>

<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="/Emp/dashboard">Home</a></li>
                <li><a href="/viewprofile">Edit profile</a></li>
                <li><a href="/viewpayslip">View Payslip</a></li>
                <li><a href="/viewpayslip">Send Attendance</a></li>
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
                <h1>Welcome to the Dashboard<?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>Here is your main content area.</p>
            </div>
        </main>
    </div>
</body>

</html>