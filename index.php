<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  include_once("classes/calendar.php");

  if (isset($_POST["startDate"])) {
    $startDate = $_POST["startDate"];
  }
  else {
    $startDate = date('Y-m-01');
  }

  if (isset($_POST["lastmonth"])) {
    $startDate = date('Y-m-d', strtotime($startDate . ' - 1 month'));
  }

  if (isset($_POST["nextmonth"])) {
    $startDate = date('Y-m-d', strtotime($startDate . ' + 1 month'));
  }

  if (isset($_POST["today"])) {
    $startDate = date('Y-m-01');
  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>GetFit Logger</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <script type="text/javascript" src="scripts/getfit.js"></script>
  </head>
  <body>
    <form method="post">
      <?php
        $cal = new calendar($startDate);
        $cal->LoadEvents();
        $cal->PrintCalendar($startDate);
        ?>
    </form>
    <div id="divEditor">
      <table id="tblEditor" border="0" cellspacing="0" cellpadding="2" rules="rows" width="250px" bgColor="White">
        <tr>
          <th colspan="2">Edit Entry</th>
        </tr>
        <tr>
          <td align="right">Date:</td>
          <td><div id="dteDate"></div></td>
        </tr>
        <tr>
          <td align="right">Miles:</td>
          <td><input type="text" size="10" name="dblMiles" id="dblMiles"></td>
        </tr>
        <tr>
          <td align="right">Time:</td>
          <td>
            <input type="text" class="time" name="nHours" id="nHours">:<input type="text" class="time" name="nMinutes" id="nMinutes">:<input type="text" class="time" name="nSeconds" id="nSeconds">
          </td>
        </tr>
        <tr>
          <td align="right">Activity:</td>
          <td>
            <select name="tActivity" id="tActivity">
              <option value=""></option>
              <option value="Walk">Walk</option>
              <option value="Run" selected>Run</option>
              <option value="Walk/Run">Walk/Run</option>
            </select>
         </td>
        </tr>
        <tr>
          <td align="right">Shoe:</td>
          <td>
            <select name="tShoe" id="tShoe">
              <option value=""></option>
              <option value="Ascis" selected>Ascis</option>
              <option value="Nike">Nike</option>
            </select>
          </td>
        </tr>
        <tr>
          <td align="right">Weight:</td>
          <td><input type="text" size="10" name="intWeight" id="intWeight"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Notes:</td>
          <td><textarea rows="5" cols="30" name="tNotes" id="tNotes"></textarea></td>
        </tr>
        <tr>
          <td colspan="2" style="text-align:center;"><input type="button" value="Cancel" class="button" onclick="CancelSave()"> <input type="button" value="Save" class="button" onclick="SaveEvent()"></td>
        </tr>
        <input type="hidden" id="idEvent"/>
      </table>
    </div>

  </body>
</html>
