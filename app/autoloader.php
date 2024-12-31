<?php
spl_autoload_register(function ($class_name) {
    //include '' . $class_name . '.php';

    include 'app/core/' . $class_name . '.php';
});
