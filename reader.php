<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Headers: X-Requested-With');
  header('Access-Control-Allow-Headers: Content-Type');
  header('Access-Control-Allow-Methods: POST, GET');

  include_once("classes/DAL.php");

  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 01 Jan 1996 00:00:00 GMT');
  header('Content-type: application/json');

  $events =  DAL::GetEvents('2013-09-01','2013-10-30');
  $response = $_GET["jsoncallback"] . "(" . json_encode($events) . ")";
  echo $response;




?>
