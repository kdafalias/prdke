/**
 * Process Query Language: Build query from frontend
 * @type Element
 */

var node= document.getElementById("request");
/**
 * Request-Array: collects all process query elements for sending to webservice
 * 
 * @type Array
 */
var request=[];
var jsonStr="";

/**
 * Inserts next Element in query array
 * @returns void
 */
function next(){
	var curSelect = new Object();
    curSelect.yesNo = document.getElementById("yesNo").options[document.getElementById("yesNo").selectedIndex].value;
   	curSelect.query  = document.getElementById("query").options[document.getElementById("query").selectedIndex].text;
   	curSelect.Übergang = document.getElementById("Übergang").options[document.getElementById("Übergang").selectedIndex].value;
    curSelect.id = $( "#query" ).val();
	request.push(curSelect);
	print();
}
// delete all elements
function reset(){
	request=[];
	print();
}
// delete last element
function ret(){
	request.pop(request.length);
	print();
}
// ausgabe auf website
function print(){
	node.innerHTML="";
	var query;
	for(i=0;i<request.length;i++){
		query="";
		query+=request[i].yesNo;
		query+=request[i].query;
		query+=request[i].Übergang;
		query+=" ";
		node.insertAdjacentHTML("beforeend",query);
	}
}
// Method to get inserted Query
function getQuery(){
	jsonStr = JSON.stringify({Object:request});
	console.log(jsonStr);
	// sending query is missing
	return jsonStr;
}