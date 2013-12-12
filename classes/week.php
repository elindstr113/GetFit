<?php

include_once("classes/day.php");
date_default_timezone_set("America/Detroit");

class week {

  public $days = array();

  function __construct($currentDay) {
    for($i=0; $i<7; $i++) {
      $this->days[] = new day($currentDay);
      $currentDay = date('Y-m-d', strtotime($currentDay.' + 1 day'));
    }
  }

}

?>
