<html>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript">
    function GetStuff() {
      $(function() {

        //retrieve comments to display on page
        $.getJSON("reader.php?jsoncallback=?", function(data) {


          //create a container for each comment
          var div = $("<table id='tblMiles' style='border:solid black 1px;' border='1' cellspacing='0' cellpadding='0'>").addClass("row").appendTo("#divDisplay");

          //add author name and comment to container
          //loop through all items in the JSON array
          for (var x = 0; x < data[0].length; x++) {

            //alert(data[0][x].date)

            var tRow = $("<tr>").appendTo(div);
            $("<td>").text(data[0][x].date).appendTo(tRow);
            $("<td>").addClass("comment").text(data[0][x].miles).appendTo(tRow);
          }
        });
      });
    }

  </script>
<body>

<input type="button" value="Test" onclick="GetStuff()"/>
<div id="divDisplay" style="border:solid red 1px;height:20px;"></div>

</body>
</html>
