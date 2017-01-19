<<<<<<< HEAD:index.html
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
					<label for="caseID">Case:</label><br>
    				<input id="caseID" type = "text" ><br>

    				<label for="abdeckung" class="abstand">Abdeckung:</label><br>
    				<input id="abdeckung" type = "text" value ="1"><br>
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
									<option value=!>nicht</option>
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
		<script type="text/javascript">
            $(document).ready(function(){
                $('.menubutton').click(function(){
                    $('nav').slideToggle('slow');
                });
          	});
        </script>
		<!--Repositionieren des Graphen, falls Bildschirmgröße verändert wird-->
		<script>
			window.onresize = function() {
				reposChord();	
				reposEvent();
				document.getElementById("1a").innerHTML=window.innerWidth;
				document.getElementById("2a").innerHTML=window.innerHeight;
			};

		</script>
		<!--js für Aufspannen der Graphen-->
		<script>
			changeSizeEvent = function() {
				if(document.getElementById('left').style.width == "100%"){
					document.getElementById('left').style.width = "66.5%"
					document.getElementById('right').style.width = "33%"
					document.getElementById('e4').style.height = "height:100%;"
				}else{
					document.getElementById('left').style.width = "100%"
					document.getElementById('right').style.width = "100%"
				}
			};
		</script>
		<script src="https://d3js.org/d3.v4.min.js"></script>
		<script src="https://d3js.org/d3.v3.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<!--<script src="js/abdeckungsdiagramm.js"></script>-->
		<script src="js/query.js"></script>
		<script src="js/change.js"></script>
		<script src="js/resize.js"></script>
		<script src="js/requests.js"></script>
	</body>
	<footer>
	</footer>
=======
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
					<label for="cases">Anzahl Cases:</label><br>
    				<input id="cases" type = "text" ><br>

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
									<option value=!>nicht</option>
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
		<script type="text/javascript">
            
            $(document).ready(function(){
                $('.menubutton').click(function(){
                    $('nav').slideToggle('slow');
                });
                $('div#e4').on('click', 'div', function(event){
                  event.stopPropagation();
                  if(allCoverage) $('div.coverage').addClass("covInactive");
                  allCoverage = false;
                  if($(this).hasClass("covInactive"))
                  {
                    $(this).removeClass("covInactive");
                    variations.push($(this).attr('data-id'));
                  }
                  else
                  {
                    $(this).addClass("covInactive");
                    variations.splice(variations.indexOf($(this).attr('data-id')),1);
                  }
                  console.log(variations);
                  sendWebRequest();
                });
                $('body').on('click','div#e4',function(event) {
                  allCoverage = true;
                  $('div.coverage').removeClass("covInactive");
                  variations = new Array();
                  sendWebRequest();
                });
          	});
        </script>
		<!--Repositionieren des Graphen, falls Bildschirmgröße verändert wird-->
		<script>
			window.onresize = function() {
				reposChord();	
				reposEvent();
				document.getElementById("1a").innerHTML=window.innerWidth;
				document.getElementById("2a").innerHTML=window.innerHeight;
			};

		</script>
		<!--js für Aufspannen der Graphen-->
		<script>
			changeSizeEvent = function() {
				if(document.getElementById('left').style.width == "100%"){
					document.getElementById('left').style.width = "66.5%"
					document.getElementById('right').style.width = "33%"
					document.getElementById('e4').style.height = "height:100%;"
				}else{
					document.getElementById('left').style.width = "100%"
					document.getElementById('right').style.width = "100%"
				}
			};
		</script>
		<script src="https://d3js.org/d3.v4.min.js"></script>
		<script src="https://d3js.org/d3.v3.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<!--<script src="js/abdeckungsdiagramm.js"></script>-->
		<script src="js/query.js"></script>
		<script src="js/change.js"></script>
		<script src="js/resize.js"></script>
	</body>
	<footer>
	</footer>
>>>>>>> e6438ba60baa39f43a9c46028477bdf2e3e11f46:index.php
</html>