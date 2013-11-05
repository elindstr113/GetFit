<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Headers: X-Requested-With');
  header('Access-Control-Allow-Headers: Content-Type');
  header('Access-Control-Allow-Methods: POST, GET');

  include_once("classes/DAL.php");

  if(isset($_GET["id"])) {
    $id = $_GET["id"];
    if ($id !== ""){
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 01 Jan 1996 00:00:00 GMT');
      header('Content-type: application/json');
      echo(json_encode(DAL::GetEvent($id)));
    }
  }

  if(isset($_GET["x"])) {
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 01 Jan 1996 00:00:00 GMT');
      header('Content-type: application/json');
      echo(json_encode(DAL::GetTotalRows()));
  }


  if(isset($_POST["data"])) {
    $data = $_POST["data"];
    $event = json_decode($data);
    $id = DAL::SaveEvent($event);
  }



?>
