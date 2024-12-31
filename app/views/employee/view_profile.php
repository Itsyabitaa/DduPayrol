<?php

use App\Core\Database;

if (!isset($_SESSION['username']) || !isset($_SESSION['role_id'])) {
    include './index.php';
    exit;
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="form-container">
        <h2>Edit Your Details</h2>
        <form method="POST" action="/post_edit_user">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="username">New Username:</label>
            <input type="text" id="username" name="username" required>

            <button type="submit">Update</button>
        </form>

        </form>
    </div>
</body>

</html>