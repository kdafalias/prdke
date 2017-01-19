
var processquery;
var allCoverage = true;
var variations = new Array();

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
	// send Request fehlt noch
	$.getJSON("/webservice/webservice.php",oData,function(json){
  		drawGraph(json);
	});
}

// draw graps and illustrate informations
function drawGraph(json){
	graph(json,1200,600);
	MTime(json);
	text();file:///C:/Users/Wolfgang/Documents/UNI/Praktikum%20DKE/Frontend_neu/index.html
	bchart(json);
	chord(json);
	coverage(json);
}