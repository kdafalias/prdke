
<html>
    <head>
        <meta charset="utf-8">
        <title>Process-Miner</title>
        <link rel="stylesheet" href="style.css" type="text/css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=none">
        <script src="js/eventgraph.js"></script>
        <script src="js/queue.js"></script>
        <script src="js/underscore.js"></script>
        <script src="js/barchart.js"></script>
        <script src="js/chord.js"></script>
        <script src="js/coverage.js"></script>
        <script src="js/requests.js"></script>
        <script src="https://d3js.org/d3.v4.min.js"></script>
        <script src="https://d3js.org/d3.v3.min.js"></script>
        <script src="https://d3js.org/d3-selection-multi.v1.min.js"></script>
        <script src="lib/d3.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    </head>
    <body>
        <h1>Process-Miner</h1>
        <!--<p id="1a"></p>
        <p id="2a"></p>-->
        <nav>
            <!--Case und Abdeckung-->
            <div id=navleft>
                <div id=navleftleft>
                    <label for="abdeckung">Abdeckung:</label><br>
                    <input id="abdeckung" type = "text" ><br>

                    <label for="aktivitaeten" class="abstand">Anzahl Aktivitäten:</label><br>
                    <input id="aktivitaeten" type = "text" ><br>
                </div>
                <div id=navleftright>
                    <input type = "button" id="centered" value="Apply" onclick="getData()" class="button"/>
                    <input type = "button" id="centered" value="Reset" onclick="resetAll()" class="button"/>
                </div>
            </div>
            <!--Informationen-->
            <div id="navmiddle">
                <span class="txt"></span>
            </div>
            <!--Process Query Language-->
            <div id=navright>
                <div id="ausw">
                    <label>Auswahl:
                        <select name="Auswahl" id="yesNo" size="1">
                            <option value=""></option>
                            <option value="!">nicht</option>
                        </select>	
                        <select id="query" size="1">
                            <option value="">Select</option>
                            <option value=*>Alle</option>
                            <?php include_once "webservice/activities.php"; ?>
                        </select>
                        <select id="Übergang" size="1">
                            <option value="">Select</option>
                            <option value="->">Übergang</option>
                        </select>
                    </label>
                </div>
                <div id="buttns">
                    <input type = "button" value="Submit" onclick="next()" class="button"/>
                    <input type = "button" value="Return" onclick="ret()" class="button"/>
                    <input type = "button" value="Reset" onclick="reset()" class="button"/>
                </div>
                <div id="request"></div>
            </div>	
            <!--Informationen-->
        </nav>
        <main>
            <!--Eventgraph-->
            <div id=left>
                <h2>Eventgraph <input id="max" onClick = "changeSizeEvent();reposEvent();reposChord();" type="image" src="images/maximize_white.png" /><input type="radio" id="time" name="type" onChange="text()" checked="checked" class="radio"><label for="time"> Zeit</label>
                    <input type="radio" id="anz" name="type" onChange="text()" ><label for="anz" >  Anzahl</label></h2>
                <div id="chart">
                    <script type="text/javascript">
                      // Initiale Anforderung der Daten
                      sendWebRequest();
                    </script>
                </div>
            </div>
            <!--rechte Seite Main Bereich-->
            <div id=right>
                <!--BarChart-->
                <div id="e3">
                    <div id="barchart"></div>
                </div>
                <!--Abstand mit Buttons für Chord Diagramm-->
                <div id ="mitte">
                    <button id="Matrix_Btn">Chord Diagramm</button>
                    <button id="Abdeckung_Btn">Abdeckung</button>
                </div>
                <!--Chord/Abdeckungsdiagramm-->
                <div id= "unten">
                    <!--Chord-->
                    <div id="e2" >
                        <div id="tooltip"></div>
                    </div>
                    <!--Abdeckung-->
                    <div id="e4">
                    </div>
                </div>
            </div>

        </main>
        <script>window.jquery || document.write('<script src="js/jquery-3.1.0.js"><\/script>');</script>
        <script src="js/variations.js"></script>
        <!--Repositionieren des Graphen, falls Bildschirmgröße verändert wird-->
        <script>
          window.onresize = function () {
            reposChord();
            reposEvent();
            redraw();
          };

        </script>
        <!--js für Aufspannen der Graphen-->
        <script>
          changeSizeEvent = function () {
            /*Alles wird auf einer Seite dargestellt*/
            if (document.getElementById('left').style.width == "100%") {
              document.getElementById('left').style.width = "66.5%"
              document.getElementById('right').style.width = "33%"
              document.getElementById('right').style.marginLeft = "0.5%"
              document.getElementById('e3').style.height = "40%"
              document.getElementById('unten').style.height = "54.6%"
              redraw()
            } else {
              /*Eventgraph wird auf 100% Breite aufgespannt, alles andere rutscht nach unten*/
              document.getElementById('left').style.width = "100%"
              document.getElementById('right').style.width = "100%"
              document.getElementById('right').style.marginLeft = "0%"
              document.getElementById('e3').style.height = "60%"
              document.getElementById('unten').style.height = "110%"
              document.getElementById('unten').style.paddingBottom = "1%"
              redraw()
            }
          };
        </script>
        <script src="https://d3js.org/d3.v4.min.js"></script>
        <script src="https://d3js.org/d3.v3.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <!--<script src="js/abdeckungsdiagramm.js"></script>-->
        <script src="js/query.js"></script>
        <script src="js/change.js"></script>
    </body>
    <footer>
    </footer>
</html>