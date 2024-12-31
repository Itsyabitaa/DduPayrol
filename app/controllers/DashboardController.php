<?php
class DashboardController
{
    public function directorDashboard()
    {

        include 'app/views/Dashboards/Director_dashboard.php';
    }

    public function employeeDashboard()
    {

        include 'app/views/Dashboards/Emp_dashboard.php';
    }

    public function HRMdashboard()
    {

        //die("gggg");
        include 'app/views/Dashboards/HRM_dashboard.php';
    }

    public function HRPdashboard()
    {

        include 'app/views/Dashboards/HRP_dashboard.php';
    }
    public function PAdashboard()
    {

        //die("gggg");
        include 'app/views/Dashboards/PA_dashboard.php';
    }

    public function ScDdashboard()
    {

        include 'app/views/Dashboards/ScD_dashboard.php';
    }
    // Add more dashboard methods for other roles
}
