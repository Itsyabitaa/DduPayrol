<?php
include './app/autoloader2.php';
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Define the timeout duration (in seconds)
$timeoutDuration = 900; // 

// Check user activity and logout if inactive
if (isset($_SESSION['last_activity'])) {
    $inactiveTime = time() - $_SESSION['last_activity'];

    if ($inactiveTime > $timeoutDuration) {
        // Destroy session and redirect to login page with timeout flag
        $_SESSION['message'] = 'Session expired. Please log in again.';
        session_destroy();
        session_unset();
        session_destroy();
        header('Location: /login?timeout=1');
        exit;
    }
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();


// Define routes for view and dashboard
$viewRoutes = [
    '/' => [
        'controller' => 'LoginController',
        'method' => 'index',
        'role_check' => null,
    ],
    '/login' => [
        'controller' => 'LoginController',
        'method' => 'index',
        'role_check' => null,
    ],
    '/authenticate' => [
        'controller' => 'LoginController',
        'method' => 'authenticate',
        'role_check' => null,
    ]
];

$dashboardRoutes = [
    '/logout' => [
        'controller' => 'LoginController',
        'method' => 'logout',
        'role_check' => null,
    ],
    '/Director/dashboard' => [
        'controller' => 'DashboardController',
        'method' => 'directorDashboard',
        'role_check' => 4,
    ],
    '/attendance/store' => [
        'controller' => 'EmployeeController',
        'method' => 'store',
        'role_check' => 5,  // You can adjust this based on user roles if needed
    ],
    '/attendance/employees' => [
        'controller' => 'EmployeeController',
        'method' => 'listEmployeesBySchool',
        'role_check' => 5,  // You can adjust this based on user roles if needed
    ],
    '/attendance/edit' => [
        'controller' => 'EmployeeController',
        'method' => 'editAttendance',
        'role_check' => '5', // School Dean role
    ],
    '/attendance/view' => [
        'controller' => 'EmployeeController',
        'method' => 'viewAttendance',
        'role_check' => '5', // School Dean role
    ],

    '/Emp/dashboard' => [
        'controller' => 'DashboardController',
        'method' => 'employeeDashboard',
        'role_check' => 3,
    ],
    '/Hrm/dashboard' => [
        'controller' => 'DashboardController',
        'method' => 'HRMdashboard',
        'role_check' => 2,
    ],
    '/pa/dashboard' => [
        'controller' => 'DashboardController',
        'method' => 'PAdashboard',
        'role_check' => 1,
    ],
    '/scd/dashboard' => [
        'controller' => 'DashboardController',
        'method' => 'ScDdashboard',
        'role_check' => 5,
    ],
    '/hrp/dashboard' => [
        'controller' => 'DashboardController',
        'method' => 'HRPdashboard',
        'role_check' => 6,
    ],
    '/viewpayslip' => [
        'controller' => 'EmployeeController',
        'method' => 'generatePayslip',
        'role_check' => null,
    ],
    '/viewprofile' => [
        'controller' => 'EmployeeController',
        'method' => 'Viewprofile',
        'role_check' => null,
    ],
    '/edit_position' => [
        'controller' => 'EmployeeController',
        'method' => 'EditPosition',
        'role_check' => 4,
    ],
    '/Assign_position' => [
        'controller' => 'EmployeeController',
        'method' => 'index',
        'role_check' => 4,
    ],
    '/payroll/prepare' => [
        'controller' => 'EmployeeController',
        'method' => 'preparePayroll',
        'role_check' => '1', // Ensure only payroll analysts can access this route
    ],
    '/payroll/save-temporary' => [
        'controller' => 'EmployeeController',
        'method' => 'saveTemporary',
        'role_check' => 1, // Ensure only payroll analysts can access
    ],
    '/payroll/finalize' => [
        'controller' => 'EmployeeController',
        'method' => 'finalizePayroll',
        'role_check' => 1, // Ensure only payroll analysts can access
    ],
    '/payroll/edit' => [
        'controller' => 'EmployeeController',
        'method' => 'payrolledit',
        'role_check' => 1, // Ensure only payroll analysts can access
    ],
    '/payroll/update' => [
        'controller' => 'EmployeeController',
        'method' => 'updatePayroll',
        'role_check' => 1, // Ensure only payroll analysts can access
    ],
    '/payroll/confirm-finalize' => [
        'controller' => 'EmployeeController',
        'method' => 'confirmFinalizePayroll',
        'role_check' => 1, // Ensure only payroll analysts can access
    ],

    '/Add_employee' => [
        'controller' => 'EmployeeController',
        'method' => 'Addemployee',
        'role_check' => 6,
    ],
    '/PostAdd_employee' => [
        'controller' => 'EmployeeController',
        'method' => 'postAddemployee',
        'role_check' => null,
    ],
    '/post_edit_user' => [
        'controller' => 'EmployeeController',
        'method' => 'postEditUser',
        'role_check' => null,
    ],

];

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current request URI and normalize it
$request = $_SERVER['REQUEST_URI'];
$request = explode('?', $request)[0];

// Helper function to handle route execution
function executeRoute($route, $isDashboard = false)
{
    $controllerName = "" . $route['controller'];
    $methodName = $route['method'];

    // For dashboard routes, validate the role if needed
    if ($isDashboard && isset($route['role_check'])) {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != $route['role_check']) {
            header('Location: /login');
            exit;
        }
    }

    if (true) {
        $controller = new $controllerName();

        // Check if the method exists in the controller
        if (method_exists($controller, $methodName)) {
            // Handle methods with specific parameters
            switch ($methodName) {
                case 'generatePayslip':
                    $userId = $_SESSION['user_id'] ?? null;

                    if ($userId) {
                        $controller->$methodName($userId);
                    } else {
                        http_response_code(400);
                        echo "Bad Request: Missing required user ID.";
                    }
                    break;

                case 'editRoleAndPosition':
                    $userId = $_POST['user_id_edit'] ?? null;
                    $roleId = $_POST['role_id_edit'] ?? null;
                    $positionId = $_POST['position_id_edit'] ?? null;

                    if ($userId && $roleId && $positionId) {
                        $controller->$methodName($userId, $roleId, $positionId);
                    } else {
                        http_response_code(400);
                        echo "Bad Request: Missing required parameters.";
                    }
                    break;

                default:
                    // Call methods without parameters
                    $controller->$methodName();
                    break;
            }
        } else {
            http_response_code(404);
            echo "Error: Method $methodName not found in $controllerName.";
        }
    } else {
        http_response_code(404);
        echo "Error: Controller $controllerName not found.";
    }
}

// Check in dashboardRoutes first
if (isset($dashboardRoutes[$request])) {
    executeRoute($dashboardRoutes[$request], true);
}

// Check in viewRoutes if not found in dashboardRoutes
if (isset($viewRoutes[$request])) {
    executeRoute($viewRoutes[$request]);
}

// If no route matches, handle as 404
http_response_code(404);
//echo "404 - Not Found ";
exit;
