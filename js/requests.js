
var processquery;

/* retrieve Data from process query entry and then send request to webservice
Method is called after pushing apply button*/
function getData(){
	processquery=getQuery();
	sendWebRequest();
}

// send request to webservice and retrieve data
function sendWebRequest(){
	var caseID=document.getElementById("caseID").value;
	var abdeckung=document.getElementById("abdeckung").value;

	// send Request fehlt noch
	$.getJSON("/webservice/webservice.php",function(json){
		drawGraph(json);
	});	
}

// draw graps and illustrate informations
function drawGraph(json){
	graph(json,1200,600);
	MTime(json);
	text();
	bchart(json);
	chord(json);
	coverage(json);
}