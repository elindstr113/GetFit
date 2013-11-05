<?php
  error_reporting(E_ALL);
  ini_set("display_errors", "1");
  include_once("DAL.php");

  $targetMiles = 25;

?>
<html>
 	<meta name="HandheldFriendly" content="true"/>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style>
      body {background-color:black;}
			table {font-family:verdana;font-size:14pt;background-color:white;}
			th {background-color:darkgray;color:black;text-decoration:uppercase;}
			td {padding-left:5px;padding-right:5px;}
			.subhead {background-color:#dedede;color:black;text-align:center;}
			.button {background-color:#004068;color:white;width:75px;height:25px;font-size:10pt;}
      .time {width:25px;text-align:center;}
  </style>
<body onload="windows.scrollTo(0,1);">
    <?php
        list($thisWeeksActivities, $thisWeeksTotals, $thisWeeksTotal, $lastWeeksTotals, $lastWeeksTotal, $maxMiles) = DAL::GetMobileData();
    ?>
    <table border='1' cellspacing='0' width="100%">
      <?php
      	$headers = array("Date", "Activity", "Pace", "Miles");
				vprintf("<tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr>", $headers);
        foreach ($thisWeeksActivities as $row) {
          vprintf("<tr><td>%s</td><td align='center'>%s</td><td align='center'>%s</td><td align='right'>%.2f</td></tr>", $row);
        }
        printf("<tr style='font-weight:bold;'><td colspan='3'>This Week's Total</td><td align='right'>%.2f</td></tr>", $thisWeeksTotal);

        if ($thisWeeksTotal < $targetMiles) {
         printf("<tr style='color:red;font-weight:bold;'><td colspan='3'>Miles to Make Goal (%.2f)</td><td align='right'>%.2f</td></tr>", $targetMiles, $targetMiles - $thisWeeksTotal);
        }
        else {
          printf("<tr style='color:blue;font-weight:bold;'><td colspan='3'>Beat Goal (%.2f)</td><td align='right'>+ %.2f</td></tr>", $targetMiles, $thisWeeksTotal - $targetMiles);
        }


        //This weeks totals
        if ($thisWeeksTotal < $maxMiles) {
         printf("<tr style='color:red;font-weight:bold;'><td colspan='3'>Miles to Beat Max (%.2f)</td><td align='right'>%.2f</td></tr>", $maxMiles, $maxMiles - $thisWeeksTotal);
        }
        else {
          printf("<tr style='color:blue;font-weight:bold;'><td colspan='3'>New Max Set !!</td><td align='right'>%.2f</td></tr>", $maxMiles);
        }


				/*
        //print("<tr><td colspan='4' class='subhead'>This Week</td></tr>");
        foreach($thisWeeksTotals as $row) {
          vprintf("<tr><td colspan='3'>%s</td><td align='right'>%.2f</td></tr>", $row);
        }

        print("<tr><td colspan='4' class='subhead'>Last Week</td></tr>");
        foreach($lastWeeksTotals as $row) {
          vprintf("<tr><td colspan='3'>%s</td><td align='right'>%.2f</td></tr>", $row);
        }
        printf("<tr><td colspan='3'>Total Miles</td><td align='right'>%.2f</td></tr>", $lastWeeksTotal);
				*/

      ?>

    </table>
</body>
</html>

