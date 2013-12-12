<?php

include_once("dbparam.inc");
include_once("event.php");
include_once("weight.php");


class DAL {

  static function GetEvents($startDate, $endDate) {
    $conn = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME.";port=".DBPORT.";" ,DBUSER, DBPASS);
    $results = $conn->query("SELECT id, dteDate, tActivity, dblMiles, RIGHT(SEC_TO_TIME(nSeconds / dblMiles), 5) AS Pace FROM tblLog WHERE dteDate>='$startDate' and dteDate<='$endDate' AND dblMiles > 0 ORDER BY dteDate");
    $rows = $results->fetchall();
    $events = array();
    foreach ($rows as $row){
      $event = new event();
      $event->id = $row["id"];
      $event->date = date("Y-m-d", strtotime($row["dteDate"]));
      $event->activity = $row["tActivity"];
      $event->miles = $row["dblMiles"];
      $event->pace = $row["Pace"];
      $events[] = $event;
    }
    $results = $conn->query("SELECT dteDate, intWeight FROM tblLog WHERE dteDate>='$startDate' and dteDate<='$endDate' AND intWeight>0 ORDER BY dteDate");
    $rows = $results->fetchall();
    $weights = array();
    foreach ($rows as $row){
      $weight = new weight();
      $weight->date = date("Y-m-d", strtotime($row["dteDate"]));
      $weight->pounds = $row["intWeight"];
      $weights[] = $weight;
    }

    $conn = null;
    return array($events, $weights);
  }

  static function GetEvent($id) {
    $conn = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME.";port=".DBPORT.";" ,DBUSER, DBPASS);
    $results = $conn->query("SELECT id, dteDate, intWeight, dblMiles, tActivity, tShoe, nSeconds, tNotes FROM tblLog WHERE id = $id");
    $row = $results->fetch();
    $conn = null;
    return $row;
  }

  static function SaveEvent($event) {
    $conn = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME.";port=".DBPORT.";" ,DBUSER, DBPASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("CALL usp_UpdateEvent(:id, :dteDate, :intWeight, :dblMiles, :tActivity, :tShoe, :nSeconds, :tNotes, @out_id)");
		$stmt->bindParam(":id", $event->id, PDO::PARAM_INT);
		$stmt->bindParam(":dteDate", $event->dteDate, PDO::PARAM_STR);
		$stmt->bindParam(":intWeight", $event->intWeight, PDO::PARAM_INT);
		$stmt->bindParam(":dblMiles", $event->dblMiles, PDO::PARAM_STR);
		$stmt->bindParam(":tActivity", $event->tActivity, PDO::PARAM_STR);
		$stmt->bindParam(":tShoe", $event->tShoe, PDO::PARAM_STR);
		$stmt->bindParam(":nSeconds",$event->nSeconds,PDO::PARAM_INT);
		$stmt->bindParam(":tNotes",$event->tNotes,PDO::PARAM_STR);
		$stmt->execute();
    $stmt->closeCursor();
    $id = $conn->query("select @out_id")->fetch(PDO::FETCH_ASSOC);
    unset($stmt);
    unset($conn);
    return $id["@out_id"];
  }


  static function GetTotalMiles() {
    $conn = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME.";port=".DBPORT.";", DBUSER, DBPASS);
    $results = $conn->query("SELECT tActivity, SUM(dblMiles) AS miles FROM tblLog WHERE dteDate>='2013-03-31' and dblMiles>0 GROUP BY tActivity");
    $rows = $results->fetchall();
    $walk = 0;
    $run = 0;
    $walkrun = 0;
    foreach ($rows as $row) {
      $miles = $row["miles"];
      switch ($row["tActivity"]) {
        case "Walk":
          $walk = $miles;
          break;
        case "Run":
          $run = $miles;
          break;
        case "Walk/Run":
          $walkrun = $miles;
          break;
      }
    }
    $results = $conn->query("SELECT SUM(dblMiles) AS aMiles FROM tblLog WHERE dteDate>='2013-03-31' and tActivity='Run' AND tShoe='Ascis'");
    $row = $results->fetch();
    $aMiles = $row["aMiles"];
    $results = $conn->query("SELECT COUNT(tActivity) AS entries FROM tblLog WHERE dteDate>='2013-03-31' AND dblMiles>0");
    $row = $results->fetch();
    $entries = $row["entries"];

    $conn = null;
    $total = $walk + $run + $walkrun;
    return array($walk, $run, $walkrun, $total, $entries, $aMiles);
  }

  static function GetTotalRows() {
    $queries = array();
    $output = array(); 
    $conn = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME.";port=".DBPORT.";", DBUSER, DBPASS);
    $queries[] = "SELECT tActivity, SUM(dblMiles) AS miles FROM tblLog WHERE dteDate>='2013-03-31' and dblMiles>0 GROUP BY tActivity";
    $queries[] = "SELECT SUM(dblMiles) AS aMiles FROM tblLog WHERE dteDate>='2013-03-31' and tActivity='Run' AND tShoe='Ascis'";
    $queries[] = "SELECT COUNT(tActivity) AS entries FROM tblLog WHERE dteDate>='2013-03-31' AND dblMiles>0";
    $queries[] = "SELECT RIGHT(SEC_TO_TIME(nSeconds / dblMiles), 5) AS Pace, dblMiles FROM tblLog WHERE dteDate>='2013-03-31' AND dblMiles>0 AND nSeconds > 0 ORDER BY Pace LIMIT 30";
    $results = $conn->query(join(";",$queries));
    for ($i=0;$i<count($queries);$i++) {
      $rows = $results->fetchall(PDO::FETCH_ASSOC);
      $output[] = $rows;
      $results->nextRowset();
    }
    $conn = null;
    return $output;
  }

  static function GetSummary($limit) {

    $records = array();

    $conn = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME.";port=".DBPORT.";", DBUSER, DBPASS);

    #TOP FIVE MONTHS
    $results = $conn->query("SELECT YEAR(dteDate) as year, MONTH(dteDate) as month, SUM(dblMiles) AS miles FROM tblLog WHERE dteDate>'2013-03-30' AND tActivity='Run' GROUP BY year, month ORDER BY miles DESC LIMIT $limit");
    $records['months'] = $results->fetchall(PDO::FETCH_ASSOC);

    #TOP FIVE WEEKS
    $results = $conn->query("SELECT YEAR(dteDate) as year, MONTH(dteDate) as month, WEEK(dteDate) AS week, SUM(dblMiles) AS miles FROM tblLog WHERE dteDate>'2013-03-30' AND tActivity='Run' GROUP BY year, month, week ORDER BY miles DESC LIMIT $limit");
    $records['weeks'] = $results->fetchall(PDO::FETCH_ASSOC);

    #TOP FIVE MILES 
    $results = $conn->query("SELECT dteDate, dblMiles FROM tblLog WHERE dteDate>'2013-03-30' AND tActivity='Run' ORDER BY dblMiles DESC LIMIT $limit");
    $records['miles'] = $results->fetchall(PDO::FETCH_ASSOC);

    #TOP FIVE PACE
    $results = $conn->query("SELECT RIGHT(SEC_TO_TIME(nSeconds / dblMiles), 5) AS pace, dblMiles, dteDate FROM tblLog WHERE dteDate>'2013-03-31' AND tActivity='Run' AND dblMiles>0 AND nSeconds>0 ORDER BY pace, dteDate DESC LIMIT $limit");
    $records['pace'] = $results->fetchall(PDO::FETCH_ASSOC);

    $conn = null;
    return $records;
  }

}

?>
