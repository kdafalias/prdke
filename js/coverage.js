var d;
function coverage(json){
	d=json;
	
	d3.select("#e4").select("svg").remove();
	var margin ={top:20, right:30, bottom:30, left:22},
		
    width=document.getElementById("unten").offsetWidth-margin.left - margin.right, 
    height=document.getElementById("unten").offsetHeight-margin.top-margin.bottom;

// scale to ordinal because x axis is not numerical
var x = d3.scale.ordinal().rangeRoundBands([0, width], .1);

//scale to numerical value by height
var y = d3.scale.linear().range([height, 0]);

var chart = d3.select("#e4")  
              .append("svg")  //append svg element inside #chart
              .attr("width", width+(2*margin.left)+margin.right)    //set width
              .attr("height", height+margin.top+margin.bottom);  //set height
var xAxis = d3.svg.axis()
              .scale(x)
              .orient("bottom");  //orient bottom because x-axis will appear below the bars

var yAxis = d3.svg.axis()
              .scale(y)
              .orient("left");

d3.json(json, function(error, data){
	data=json.Variation;
  x.domain(data.map(function(d){ return d.VariationID}));
  y.domain([0, d3.max(data, function(d){return d.num})]);
  
  var bar = chart.selectAll("g")
                    .data(data)
                  .enter()
                    .append("g")
                    .attr("transform", function(d, i){
                      return "translate("+x(d.VariationID)+", 0)";
                    });
  var sClass = 'coverage';
  variations.every(function(value, index, array)
  {
    //  console.log(value);
     // console.log(data);
    //if(value == data['VariationID']) alert("gefunden"+value);
  });

	// balken
  bar.append("rect")
      .attr("y", function(d) { 
        return y(d.num); 
      })
      .attr("x", function(d,i){
        return x.rangeBand()+(margin.left/4);
      })
      .attr("height", function(d) { 
        return height - y(d.num); 
      })
  		.attr("class",sClass)
  		.attr("class",function(d){
	  		if(d.vis==false){
				return "covInactive";
			}
  		})
  		.attr("data-id", function(d){
    		return d.VariationID;
		})  
      .attr("width", x.rangeBand());
  bar.append("text")
      .attr("x", x.rangeBand()+margin.left )
      .attr("y", function(d) { return y(d.num) -10; })
      .attr("dy", ".75em")
      .text(function(d) { return d.num; });
  // x-Achse
  chart.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate("+margin.left+","+ height+")")        
        .call(xAxis);
    // y-Achse
  chart.append("g")
        .attr("class", "y axis")
        .attr("transform", "translate("+margin.left+",0)")
        .call(yAxis)
        .append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 6)
        .attr("dy", ".71em")
        .style("text-anchor", "end")
        .text("Anzahl");
});
// set data balken aktiv oder inaktiv
function setData(){
    this.d=d;
}
 
function type(d) {
    d.VariationID = +d.VariationID; // coerce to number
    return d;
  }
};
function redraw(){
	coverage(d);
};
//};