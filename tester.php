<html>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript">
    function GetStuff() {
      $(function() {
        $.getJSON("reader.php?jsoncallback=?", function(data) {
          var div = $("<table id='tblMiles' style='border:solid black 1px;' border='1' cellspacing='0' cellpadding='0'>").addClass("row").appendTo("#divDisplay");
          for (var x = 0; x < data[0].length; x++) {
            var tRow = $("<tr>").appendTo(div);
            $("<td>").text(data[0][x].date).appendTo(tRow);
            $("<td>").addClass("comment").text(data[0][x].miles).appendTo(tRow);
            $("<td>").addClass("comment").text(data[0][x].seconds).appendTo(tRow);
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
