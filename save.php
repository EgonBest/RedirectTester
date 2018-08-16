<?php
file_put_contents("redirecttest.html", $_POST['daten']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>redirect Tester</title>
  <style>
    body {
      font-family:  sans-serif;
      font-size:  large;
    }

    div#demo {
      border: 1px blue solid;
      background: darkgray;
      position: relative;
    }

    div#Types {
      border:  1px dotted;
      background-color:  lightgray;
      margin: 0 50px;
    }
    div#Types div div {
      left: 50px;
      z-index: 30;
    }

    .urls{
      display: none;
      font-size:small;
    }
  </style>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script>
    var daten = [];
    $(document).ready(function() {
      var formData = {
        'testUrl': "redirecttest.html"
      };
      $.ajax({
        type: 'POST',
        url: 'runnertest.php',
        data: formData,
        dataType: 'json',
        encode: true
      })
      .done(function (data) {
        $('#Types').empty();
        var err = (data.Error===true)?"HTTP":"HTTPS";
        $('#Error').text(err);
        $('#Requests').text(data.Requests);
        $('#Size').text(data.Size);
        $('#Time').text(data.Time);
        i = 0;
        $.each(data.Type,function(index, item){
          $('#Types').append("<div id='"+index+"' class='Requests'>"+item['Requests']+" X "+index+" ( "+item['Size']+" Byte )</div>");
          $.each(item['Urls'],function(index1, item1){
            $('#'+index).append("<div class='"+index+" urls'><a href='"+item1['url']+"' target='_blank'>"+item1['url']+"</a> " +item1['size']+" Byte</div>");
          });
          daten[i] = [];
          daten[i]['value'] = [];
          daten[i]['key']=index;
          daten[i]['value']['Request']=item['Requests'];
          daten[i]['value']['Size']=item['Size'];
          i++;
        });
        $('#demo').show();
        $(".Requests").mouseenter(function(){
          $(this.childNodes).show();
        });
        $(".Requests").mouseleave(function(){
          $(this.childNodes).hide();
        });
        daten.clear;
        //console.log("JSON = " + JSON.stringify(data));
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
        data.addColumn({type:'string', role:'tooltip'});

        for (var i = 0; i < daten.length; i++) {
          var row = [daten[i]['key'].toString(), daten[i]['value']['Request'],daten[i]['value']['Request']+" " + daten[i]['key'].toString() + " Dateien mit "+daten[i]['value']['Size']+" Byte"];
          data.addRow(row);
        }
        // Optional; add a title and set the width and height of the chart
        var options = {title:'Verteilung Dateitypen', width:550, height:400, tooltip: {isHtml: true}, is3D:true};

        // Display the chart inside the <div> element with id="piechart"
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);

      }
    });
  </script>
</head>
<body>
<div id="piechart"></div>
<div id="demo" style="display: none">
  Protokoll <span id="Error"></span><br>
  Requests <span id="Requests"></span><br>
  Größe <span id="Size"></span><br>
  Ladezeit <span id="Time"></span><br>
  Dateitypen <div id="Types"></div><br>
</div>
<a href="redirecttest.html" target="_blank">Werbemittel anzeigen</a>
</body>
</html>