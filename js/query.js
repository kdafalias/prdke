var node= document.getElementById("request");
function kum(){
	var query="";
	query+=document.getElementById("yesNo").options[document.getElementById("yesNo").selectedIndex].value;
	query+=document.getElementById("query").options[document.getElementById("query").selectedIndex].text;
	query+=document.getElementById("Übergang").options[document.getElementById("Übergang").selectedIndex].value;
	query+=" ";
	node.insertAdjacentHTML("beforeend",query);
}
function reset(){
	node.innerHTML="";
}