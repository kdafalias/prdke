function bchart(json){
	var chart = d3.select("#barchart");
	d3.json(json, function(d){
		long=json.nodes;	// long= array mit start und endknoten
		var index;
		// methode ohne start und endknoten für barchart
		data=long.filter(function(i) {
			return i.name != "Start"&& i.name!="End"
		});
		// konvertieren zu nummer
		data.forEach(function(d){
			d.value=+d.value;
		});
		
		// maximalen Wert berechnen
		var maxVal=d3.max(data,function(d){
			return d.value;
		});
		var count= data.length;
		
		data.sort(function(a,b){
			return Number(b.value)-Number(a.value);
		})
		
		var bar = d3.select("#barchart").selectAll("div")
		.data(data)
		.enter().append("div")
		.attr("id","bar")
		.style("width",function(d){
			return Number((d.value/maxVal)*100)+"%";
		})
		.style("height",function(d){
			return Number((100/count))+"%";
		});
		
		bar.append("span")
		.attr("class","label")
		.text(function(d){
			return d.value;
		});
		bar.append("span")
		.attr("class","name")
		.text(function(d){
			return d.name;
		});
	
	});
};