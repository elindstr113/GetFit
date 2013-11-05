<?php

include_once("classes/week.php");
include_once("classes/DAL.php");

class calendar {

  public $weeks = array();
  private $startDate;
  private $endDate;
  private $firstSunday;

  function __construct($firstDayOfMonth) {
    $this->startDate = $firstDayOfMonth;
    list($firstSunday, $lastSunday) = self::GetCalendarDates($firstDayOfMonth);
    $this->firstSunday = $firstSunday;
    $currentSunday = $firstSunday;
    while ($currentSunday <= $lastSunday) {
      $this->weeks[] = new week($currentSunday);
      $currentSunday = date('Y-m-d', strtotime($currentSunday.' + 7 day'));
    }
    $this->endDate = end(end($this->weeks)->days)->date;
  }


  private function GetCalendarDates($firstDayOfMonth) {
    $dayOfWeekOfFirstDay = date('w', strtotime($firstDayOfMonth));
    $firstSunday = date('Y-m-d', strtotime($firstDayOfMonth.' - '.$dayOfWeekOfFirstDay.' day'));
    $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));
    $dayOfWeekOfLastDay = date('w', strtotime($lastDayOfMonth));
    $lastSunday = date('Y-m-d', strtotime($lastDayOfMonth.' - '.$dayOfWeekOfLastDay.' day'));
    return array($firstSunday, $lastSunday);
  }


  public function LoadEvents() {
    list($events, $weights) = DAL::GetEvents($this->firstSunday, $this->endDate);
    foreach($events as $event) {
      $dayToModify = self::FindDay($event->date);
      if ($dayToModify) {
        $dayToModify->events[] = $event;
      }
    }
    foreach($weights as $weight) {
      $dayToModify = self::FindDay($weight->date);
      if ($dayToModify) {
        $dayToModify->weight = $weight;
      }
    }
  }

  public function FindDay($date) {
    $cell = null;
    foreach($this->weeks as $week){
      foreach($week->days as $day) {
        if ($day->date == $date) {
          $cell = $day;
          break;
        }
      }
    }
    return $cell;
  }


  function GetSunsetAndSunrise($startingMonth, $date){
    $currentMonth = date('m',strtotime($date));
    $shading = (($currentMonth == $startingMonth) ? "" : " shaded");
    $sun_info = date_sun_info(strtotime($date), 43.25, -86.317);
    $sunrise = date("g:i", $sun_info["sunrise"]);
    $sunset = date("g:i", $sun_info["sunset"]);
    $dayToPrint = date('d', strtotime($date));
    return array("shading"=>$shading, "sunrise"=>$sunrise, "sunset"=>$sunset, "day"=>$dayToPrint);
  }


  function PrintEvent($event) {
    $activity = $event->activity;
    printf("<div class='event %s' onclick=EditEvent(%d) title='Click to edit entry'>%s <div class='ra'>%.2f%s</div></div>", strtolower($activity), $event->id, $activity, $event->miles, (($event->pace == '00:00') ? '' : "/$event->pace"));
  }

  function PrintTotals($weekToPrint) {
    $totals = array("Walk"=>0, "Run"=>0, "Total"=>0);
    foreach($weekToPrint->days as $day) {
      foreach($day->events as $event) {
        $miles = $event->miles;
        $totals[$event->activity] += $miles;
        $totals["Total"] += $miles;
      }
    }
    if ($totals["Total"] > 0) {
      print("<div class='dayNumber'>&nbsp;</div>");
      foreach($totals as $activity=>$total) {
        if ($total > 0 ) {
          printf("<div class='event %s'>%s <div class='ra'>%.2f</div></div>", strtolower($activity), $activity, $total);
        }
      }
    }
  }

  public function PrintCalendar($startDate) {
    $startingMonth = date('m', strtotime($startDate));

    print("<div id='calFrame'>");
    print("<table border='1' id='calTable'>");
    print("<colgroup><col span='7'/><col style='background-color:#eee!important;'/></colgroup>");
    print("<tr>");
    print("<td style='height:50px;vertical-align:middle;' colspan='8'>");
    print("<div id='floatLeft'>");
    print("<input type='submit' value='&#9668;' name='lastmonth'>");
    print("</div>");
    print("<div id='floatRight'>");
    print("<input type='submit' value='&#9658;' name='nextmonth'>");
    print("</div>");
    print("<div id='calTitle'>".date('F Y', strtotime($startDate))."</div>");
    print("</td>");
    print("</tr>");
    print("<tr>");
    foreach(array("Sun","Mon","Tue","Wed","Thu","Fri","Sat", "Week Totals") as $dayName) {
      print("<th>$dayName</th>");
    }
    print("</tr>");
    foreach($this->weeks as $week){
      print("<tr>");
      foreach($week->days as $day) {
        if ($day->date == date('Y-m-d')) {
          print("<td class='today'>");
        }
        else {
          if (date('m', strtotime($day->date)) != date('m',strtotime($this->startDate))) {
            print("<td class='off'>");
          }
          else {
            print("<td>");
          }
        }
        $printData = self::GetSunsetAndSunrise($startingMonth, $day->date);
        printf("<div class='dayNumber%s' title='Sunrise: %s am\nSunset:  %s pm' onclick='NewEvent(\"%s\")'>%s</div>", $printData["shading"],$printData["sunrise"], $printData["sunset"], $day->date, $printData["day"] );
        foreach($day->events as $event) {
          self::PrintEvent($event);
        }
        if ($day->weight) {
          $lost = (252 - $day->weight->pounds);
          printf("<div class='weight' title='Down %d pounds'>Weight %s</div>", $lost, $day->weight->pounds);
        }
        print("</td>");
      }
      print("<td>");
      self::PrintTotals($week);
      print("</td>");
      print("</tr>");
    }
    print("</table>");

    print("<div style='margin-top:8px;'>");
    print("<div style='float:right;width:100px;text-align:right;'>");
    print("<input type='submit' name='today' value='Today'>");
    print("</div>");
    list($walk, $run, $walkrun, $total, $entries) = DAL::GetTotalMiles();
    printf("<div id='summaryLine'>%.2f miles, in %d entries, logged since 3/31/2013. (%.2f walking, %.2f running)</div>", $total, $entries, $walk, $run, $walkrun);
    print("</div>");
    print("</div>");

    print("<input type='hidden' name='startDate' value='$startDate'>");
    $date = new DateTime();
    $timestamp = $date->getTimestamp();
    print("<div id='workoutGrid'>");
    print("<div id='svgGrid'>");
    print("<object data='http://lindstrom.hopto.org/workouts.svg?i=".(string)$timestamp."' type='image/svg+xml'></object>");
    print("</div>");
    print("</div>");

  }

}


?>
