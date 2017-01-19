function coverage(json){
	var chart = d3.select("#e4");
	d3.json(json, function(d){
		data=json.Variation;

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
		.attr("class","coverage")
  .attr("data-id", function(d){
    return d.VariationID;
  })  
		.style("width",function(d){
			return Number((100/count))+"%";
		})
		.style("height",function(d){
			return Number((d.num/maxVal)*100)+"%";
		});
		

	});
};