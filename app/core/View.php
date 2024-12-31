<?php


namespace App\Core;

class View
{
    public static function render($viewName)
    {
        // Build the path to the view file
        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';

        if (file_exists($viewPath)) {
            include_once($viewPath);
        } else {
            // Handle error if view doesn't exist
            echo "View not found: " . $viewName;
        }
    }
}
