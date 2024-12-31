// routes.php or equivalent
$router->get('/employee/view/{id}', 'EmployeeController@viewProfile');
$router->get('/employee/edit/{id}', 'EmployeeController@editProfile');
$router->post('/employee/edit/{id}', 'EmployeeController@editProfile');
$router->get('/employee/payslip/{id}', 'EmployeeController@viewPayslip');
$router->get('/employee/leave-request/{id}', 'EmployeeController@submitLeaveRequest');
$router->post('/employee/leave-request/{id}', 'EmployeeController@submitLeaveRequest');