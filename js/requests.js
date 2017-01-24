
var processquery;
var allCoverage = true;
var variations = new Array();
var d;	// nicht alle varianten ausgewählt

/* retrieve Data from process query entry and then send request to webservice
Method is called after pushing apply button*/
function getData(){
	processquery=getQuery();
	sendWebRequest();
}

// send request to webservice and retrieve data
function sendWebRequest(){
  var oData = {};
	oData['cases']=document.getElementById("cases").value;
	oData['aktivitaeten']=document.getElementById("aktivitaeten").value;
 	oData['varianten']=variations;
  oData['query']=request;
	// send Request fehlt noch "oData," muss wieder nach "json
	$.getJSON("/webservice/webservice.php",oData,function(json){
		
  		drawGraph(json);
	});
}

function highlightActivities(json)
{
	// alle Varianten werden initial angezeigt
	$.each(json.Variation,function(index, o){
		o["vis"]=true;	// Kennzeichen für anzeigen der balken
	});

}

// draw graps and illustrate informations
function drawGraph(json){
	graph(json,1200,600);
	MTime(json);
	text();///file:/C:/Users/Wolfgang/Documents/UNI/Praktikum%20DKE/Frontend_neu/index.html
	bchart(json);
	chord(json);
	highlightActivities(json);	// vor coverage aufruf alle mit vis:true kennzeichnen
	if(allCoverage){
		coverage(json);
	}else{
		coverage(d);
	}
}
function getDat(){
	return d;
}