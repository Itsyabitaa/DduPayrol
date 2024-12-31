<?php
// helpers.php
function authorize($requiredRoleId)
{
    session_start();
    if (!isset($_SESSION['user'])) {
        // User is not logged in
        header("Location: /oop_pay/app/views/login.php");
        exit();
    }

    if ($_SESSION['user']['role_id'] !== $requiredRoleId) {
        // User does not have the required role
        echo "Unauthorized access. You do not have permission to view this page.";
        exit();
    }
}
