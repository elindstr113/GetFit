
var event = InitializeEvent();

  function ShowEditor() {
    divEditor.style.visibility = "visible";
  }

  function InitializeEvent() {
    newEvent = {
      "id":-1,
      "dteDate":"",
      "dblMiles":0,
      "nSeconds":0,
      "tActivity":"Run",
      "tShoe":"Ascis",
      "intWeight":0,
      "tNotes":""
    };
    return newEvent;
  }

  function HideEditor() {
    divEditor.style.visibility = "hidden";
  }

  function NewEvent(newDate) {
    gfEvent = InitializeEvent();
    gfEvent.dteDate = newDate;
    DisplayEvent();
  }

  function SetSelected(oDD, selection) {
    for (optionIndex=0; optionIndex<oDD.length; optionIndex++) {
      if (oDD.options[optionIndex].value === selection) {
        oDD.selectedIndex = optionIndex;
        break;
      };
    }
  }

  function DisplayEvent() {
    document.getElementById("idEvent").value = gfEvent.id;
    document.getElementById("dteDate").innerHTML = gfEvent.dteDate;
    document.getElementById("dblMiles").value = gfEvent.dblMiles;
    totalSeconds = gfEvent.nSeconds;
    hours = parseInt(totalSeconds / 3600);
    totalSeconds -= (hours * 3600);
    minutes = parseInt(totalSeconds / 60);
    totalSeconds -= (minutes * 60);
    seconds = totalSeconds;
    document.getElementById("nHours").value = hours;
    document.getElementById("nMinutes").value = minutes;
    document.getElementById("nSeconds").value = seconds;
    SetSelected(document.getElementById("tActivity"), gfEvent.tActivity);
    SetSelected(document.getElementById("tShoe"), gfEvent.tShoe);
    document.getElementById("intWeight").value = gfEvent.intWeight;
    document.getElementById("tNotes").value = gfEvent.tNotes;
    ShowEditor();
  }

  function EditEvent(id){
    req = new XMLHttpRequest();
    req.onreadystatechange = function() {
      if (req.readyState === 4) {
        gfEvent = JSON.parse(req.responseText);
        DisplayEvent();
      }
    };
    req.open("GET", "ajax.php?id=" + id, true);
    req.send();
  }

  function SaveEvent() {
    gfEvent.id = document.getElementById("idEvent").value;
    gfEvent.dteDate = document.getElementById("dteDate").innerHTML;
    gfEvent.dblMiles = document.getElementById("dblMiles").value;
    hours = document.getElementById("nHours").value;
    minutes = document.getElementById("nMinutes").value;
    seconds = document.getElementById("nSeconds").value;
    totalSeconds = (parseInt(hours) * 3600) + (parseInt(minutes) * 60) + parseInt(seconds);
    gfEvent.nSeconds = totalSeconds;
    gfEvent.tActivity = document.getElementById("tActivity").value;
    gfEvent.tShoe = document.getElementById("tShoe").value;
    gfEvent.intWeight = document.getElementById("intWeight").value;
    gfEvent.tNotes = document.getElementById("tNotes").value;
    postData = JSON.stringify(gfEvent);
    req = new XMLHttpRequest();
    req.onreadystatechange = function() {
      if (req.readyState === 4) {
        window.location.href="index.php";
      }
    };
    req.open("POST", "ajax.php", true);
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    req.send("data=" + encodeURIComponent(postData));

  }

  function CancelSave() {
    HideEditor();
  }