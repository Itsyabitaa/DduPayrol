<?php
include("./app/autoloader.php");
include('/xampp/htdocs/oop_pay/app/core/Database.php');

use App\Core\Database;

class LoginController
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 1800; // 30 minutes in seconds

    // Show login page
    public function index()
    {
        // Check if the user is already logged in
        if (isset($_SESSION['username']) && isset($_SESSION['role_id'])) {
            $this->redirectToDashboard();
        } else {
            include 'app/views/login.php';
        }
    }

    // Function to authenticate the user
    public function authenticate()
    {
        // Initialize session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user is locked out (per user, not globally)
        if (!isset($_SESSION['users'][$_POST['username']])) {
            $_SESSION['users'][$_POST['username']] = [
                'failed_attempts' => 0,
                'lockout_time' => 0
            ];
        }

        $userData = &$_SESSION['users'][$_POST['username']];

        // Check if the user is locked out
        if ($userData['failed_attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
            $remainingLockout = time() - $userData['lockout_time'];
            if ($remainingLockout < self::LOCKOUT_TIME) {
                $waitTime = self::LOCKOUT_TIME - $remainingLockout;
                echo "Too many failed login attempts. Please wait " . ceil($waitTime / 60) . " minutes before trying again.";
                exit;  // Exit, as the user is locked out
            } else {
                // Reset failed attempts after lockout period
                $userData['failed_attempts'] = 0;
                $userData['lockout_time'] = 0;
            }
        }

        // Process login if username and password are provided
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $db = Database::getConnection();
            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    // Reset failed attempts on successful login
                    $userData['failed_attempts'] = 0;
                    $userData['lockout_time'] = 0;

                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['phoneNumber'] = $user['phonenumber'];
                    $_SESSION['sch_id'] = $user['sch_id'];  // Assuming $user contains user data after authentication

                    // Redirect user to their dashboard or intended destination
                    $this->redirectToDashboard();
                } else {
                    // Increment failed attempts
                    $userData['failed_attempts']++;

                    if ($userData['failed_attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
                        $userData['lockout_time'] = time(); // Lock the user
                        echo "Too many failed login attempts. Please wait " . ceil(self::LOCKOUT_TIME / 60) . " minutes.";
                    } else {
                        $remainingAttempts = self::MAX_LOGIN_ATTEMPTS - $userData['failed_attempts'];
                        echo "Invalid password! You have $remainingAttempts attempt(s) left.";
                    }

                    // Optionally log failed login attempts
                    $this->logFailedLoginAttempt($username);
                }
            } else {
                echo "Invalid username or credentials!";
            }
        } else {
            echo "Please provide both username and password.";
        }
    }

    // Function to log failed login attempts (optional, for monitoring)
    private function logFailedLoginAttempt($username)
    {
        $logMessage = "Failed login attempt for user: $username at " . date('Y-m-d H:i:s') . "\n";
        file_put_contents('login_attempts.log', $logMessage, FILE_APPEND);
    }
    // Redirect user based on role
    public function redirectToDashboard()
    {
        if (isset($_SESSION['role_id'])) {
            switch ($_SESSION['role_id']) {
                case 4:
                    header('Location: /Director/dashboard');
                    break;
                case 3:
                    header('Location: /Emp/dashboard');
                    break;
                case 2:
                    header('Location: /Hrm/dashboard');
                    break;
                case 1:
                    header('Location: /pa/dashboard');
                    break;
                case 5:
                    header('Location: /scd/dashboard');
                    break;
                case 6:
                    header('Location: /hrp/dashboard');
                    break;
                default:
                    header('Location: /login');
                    break;
            }
            exit;
        } else {
            header('Location: /login');
            exit;
        }
    }

    // Log out the user
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
