function coverage(json){
	var chart = d3.select("#e4");
	d3.json(json, function(d){
		data=json.Variation;
		console.log(data);

		// maximalen Wert berechnen
		var maxVal=d3.max(data,function(d){
			return d.num;
		});
		var count= data.length;
		
		data.sort(function(b,a){
			return Number(a.num)-Number(b.num);
		})
		
		var bar = d3.select("#e4").selectAll("div")
		.data(data)
		.enter().append("div")
		.attr("id","coverage")
		.style("width",function(d){
			return Number((100/count))+"%";
		})
		.style("height",function(d){
			return Number((d.num/maxVal)*100)+"%";
		});
		
		bar.append("span")
		.attr("class","covLabel")
		.text(function(d){
			return d.VariationID;
		});

	});
};