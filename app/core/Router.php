<?php

class Router
{
    public $routes = [

        '/' => [
            'controller' => 'LoginController',
            'method' => 'index', // Default method to show the login page
            'role_check' => null, // No role check needed
        ],
        '/login' => [
            'controller' => 'LoginController',
            'method' => 'index', // Show login page
            'role_check' => null, // No role check needed
        ],
        '/authenticate' => [
            'controller' => 'LoginController',
            'method' => 'authenticate', // Handle login submission
            'role_check' => null, // No role check needed
        ],
        '/logout' => [
            'controller' => 'LoginController',
            'method' => 'logout', // Handle logout
            'role_check' => null, // No role check needed
        ],
        '/Director/dashboard' => [
            'controller' => 'DashboardController',
            'method' => 'directorDashboard', // Method for the Director dashboard
            'role_check' => 1, // Role check for Director (role_id = 1)
        ],
        '/Emp/dashboard' => [
            'controller' => 'DashboardController',
            'method' => 'employeeDashboard', // Method for the Employee dashboard
            'role_check' => 2, // Role check for Employee (role_id = 2)
        ],
        // Add more routes as needed
    ];



    // Add a route to the router
    public function add($uri, $callback)
    {
        $this->routes[$uri] = $callback;
    }

    // Dispatch the requested URI
    public function dispatch($uri)
    {
        if (array_key_exists($uri, $this->routes)) {
            call_user_func($this->routes[$uri]);
        } else {
            echo "404 Not Found. <a href='/oop_pay/'>Go to homepage</a>";
        }
    }
}
