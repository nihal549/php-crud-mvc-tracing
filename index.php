<?php
 session_unset();
    error_reporting(E_ERROR | E_PARSE);
    require_once  'controller/Controller.php';
   
    $controller = new Controller();
    $controller->mvcHandler();
?>