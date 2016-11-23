
function abdeckungsdiagramm(){
	var xhr = new XMLHttpRequest();
	var url="js/json.json";
						
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){ // xhr.status ==200 funktioniert nicht
			console.log('Ready');
			var myArr =JSON.parse(xhr.responseText);
			myFunction(myArr);
		}else{
			console.log('Not Ready');
			console.log(xhr.responseText);
		}
	}
	xhr.open("GET",url,true);
	xhr.send();
						
	function myFunction(arr){
		svg dia;
		var i;
		for(i=0;i<arr.length;i++){
			output += '<a>'+arr[i].firstName+''+arr[i].lastName+'</a><br/>';
		}
		document.getElementById("e3").innerHTML= output;
	}
}