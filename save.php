<?php
file_put_contents("redirecttest.html", $_POST['daten']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>redirect Tester</title>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script>
      var daten = [];
      $(document).ready(function() {
              var formData = {
                  'testUrl':'redirecttest.html'
              };
              $.ajax({
                  type: 'POST',
                  url: 'runnertest.php',
                  data: formData,
                  dataType: 'json',
                  encode: true
              })
              .done(function (data) {
                  var err = (data.Error===true)?"HTTP":"HTTPS";
                  $('#Error').text(err);
                  $('#Requests').text(data.Requests);
                  $('#Size').text(data.Size);
                  $('#Time').text(data.Time);
                  i = 0;
                  $.each(data.Type,function(index, item){$('#Types').append("<span>"+item+" X "+index+"</span><br>");daten[i] = [];daten[i]['key']=index;daten[i]['value']=item;i++;
                  });
                  console.log(data);
                  google.charts.load('current', {'packages':['corechart']});
                  google.charts.setOnLoadCallback(drawChart);
              });
              event.preventDefault();

          // Load google charts


          // Draw the chart and set the chart values
          function drawChart() {
              var data = new google.visualization.DataTable();
              data.addColumn('string','Dateityp');
              data.addColumn('number','Geladene Dateien');

              for (var i = 0; i < daten.length; i++) {
                  var row = [daten[i]['key'].toString(), daten[i]['value']];
                  data.addRow(row);
              }
              console.log(data);
              console.log(daten);
              // Optional; add a title and set the width and height of the chart
              var options = {'title':'Verteilung Dateitypen', 'width':550, 'height':400};

              // Display the chart inside the <div> element with id="piechart"
              var chart = new google.visualization.PieChart(document.getElementById('piechart'));
              chart.draw(data, options);

          }
      });
  </script>
</head>
<body>

<div id="demo">
  Protokoll <span id="Error"></span><br>
  Requests <span id="Requests"></span><br>
  Größe <span id="Size"></span><br>
  Ladezeit <span id="Time"></span><br>
  Dateitypen <div id="Types"></div><br>
</div>
<div id="piechart"></div>
<a href="redirecttest.html" target="_blank">Werbemittel anzeigen</a>
</body>
</html>