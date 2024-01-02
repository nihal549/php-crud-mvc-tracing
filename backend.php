<?php
error_reporting(E_ERROR | E_PARSE);
 require 'controller/ApiController.php';
 header("Content-type: application/json; charset=UTF-8");

 $parts = explode("/", $_SERVER["REQUEST_URI"]);
 
 if ($parts[1] != "containers") {
     http_response_code(404);
     exit;
 }
 
     $request = $parts[2] ?? null;
     $id =$parts[3]??null;
     $controller =new ApiController();
 
     $controller->handleRequest($request,$id);
?>