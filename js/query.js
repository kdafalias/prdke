var node= document.getElementById("request");
var jsonString="";
function kum(){
	var query="";
	query+=document.getElementById("yesNo").options[document.getElementById("yesNo").selectedIndex].value;
	query+=document.getElementById("query").options[document.getElementById("query").selectedIndex].text;
	query+=document.getElementById("Übergang").options[document.getElementById("Übergang").selectedIndex].value;
	query+=" ";
	node.insertAdjacentHTML("beforeend",query);
	
	var obj = new Object();
   obj.yesNo = document.getElementById("yesNo").options[document.getElementById("yesNo").selectedIndex].value;
   obj.query  = document.getElementById("query").options[document.getElementById("query").selectedIndex].text;
   obj.Übergang = document.getElementById("Übergang").options[document.getElementById("Übergang").selectedIndex].value;
   jsonString.push(obj);
	console.log(jsonString);
	/*neu machen
	var obj = JSON.parse(jsonStr);
obj['theTeam'].push({"teamId":"4","status":"pending"});
jsonStr = JSON.stringify(obj);*/
}
function reset(){
	node.innerHTML="";
	jsonString.removeAll;
}