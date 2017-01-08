	var width;
    var height;
	var force;
	var linkDistance;
	var edgelabels;
	var min_zoom = 0.1;
	var max_zoom = 7;
	var zoom;

function graph(data,width,height){
	d3.select("#chart").select("svg").remove();	// removing excisting svg before redrawing
		// mittels width und height kann die größe des containers geändert werden
    linkDistance=120;
		// Links müssen gemapped werden, damit source und target gefunden werden können.
	var links = [];
	data.edges.forEach(function(e) {
		
		// sourceNode finden
		var sourceNode = data.nodes.filter(function(n) {
        	return n.name === e.source;
    	})[0],
			
		// targetNode finden
		targetNode = data.nodes.filter(function(n) {
			return n.name === e.target;
		})[0];
		// Verbindung einfügen
    	links.push({
        	source: sourceNode,
        	target: targetNode,
        	time: e.time,
			num: e.num,
			type:e.type
    	});
	});

	force = d3.layout.force()
		.nodes(data.nodes)
		.links(links)
		.size([width, height])
		.linkDistance(linkDistance)
		.charge(-height)
		.on("tick", tick)
		.start();
	var colors = d3.scale.category10();
	svg = d3.select("#chart").append("svg")
		.attr("width", width)
		.attr("height", height);

	// marker für unterschiedliche Verbindungstypen
	svg.append("defs").selectAll("marker")
		.data(["start", "normal", "end"])
	  	.enter().append("marker")
		.attr("id", function(d) { return d; })
		.attr("viewBox", "0 -5 10 10")
		.attr("refX", 15)
		.attr("refY", -1.5)
		.attr("markerWidth", 6)
		.attr("markerHeight", 6)
		.attr("orient", "auto")
	  	.append("path")
		.attr("d", "M0,-5L10,0L0,5");

	var path = svg.append("g").selectAll("path")
		.data(force.links())
	  	.enter().append("path")
		.attr("id", function(d) { return d.source.index + "_" + d.target.index; })
		.attr("class", function(d) { return "link " + d.type; })
		.attr("marker-end", function(d) { return "url(#" + d.type + ")"; });
	
	var circle = svg.append("g").selectAll("circle")
		.data(force.nodes())
	  	.enter().append("circle")
		.attr("r", 6)
		//.style("fill",function(d,i){return colors(i);})	// verschiedene Farben der Nodes
		.style("fill",function(d,i){if(d.name==="Start"){return "red"}else if(d.name==="End"){return "green"}else {return "grey"};})
		.call(force.drag);

	var text = svg.append("g").selectAll("text")
		.data(force.nodes())
	  	.enter().append("text")
		.attr("x", 8)
		.attr("y", ".31em")
		.text(function(d) { return d.name; });
	
	edgelabels = svg.append("svg:g").selectAll("edgepath")
    	.data(force.links())
  		.enter().append("svg:text")
    	.attr("class", "path_label")
    	.append("svg:textPath")
      	.attr("startOffset", "50%")
      	.attr("text-anchor", "middle")
      	.attr("xlink:href", function(d) { return "#" + d.source.index + "_" + d.target.index; })
      	.style("fill", "#000")
      	.style("font-family", "Arial")
      	.text(function(d) { return d.num; });
	
	// Use elliptical arc path segments to doubly-encode directionality.
	function tick() {
	  	path.attr("d", linkArc);
	  	circle.attr("transform", transform);
	  	text.attr("transform", transform);
		}
	function linkArc(d) {
	  var dx = d.target.x - d.source.x,
		  dy = d.target.y - d.source.y,
		  dr = Math.sqrt(dx * dx + dy * dy);
	  return "M" + d.source.x + "," + d.source.y + "A" + dr + "," + dr + " 0 0,1 " + d.target.x + "," + d.target.y;
	}

	function transform(d) {
	  return "translate(" + d.x + "," + d.y + ")";
	}
	reposEvent();
}
// Text
function text(){
	// graph ready boolean= true, false wenn alle elemente fertig geladen
	if(document.getElementById("time").checked){
		edgelabels.text(function(d) { return d.num; })
	}else{
		// Beschriftung der Kanten auf Anzahl setzen
		edgelabels.text(function(d) { return d.time; })
	}		
};
//Beschriftungen
function MTime(data){
	$("#navmiddle .txt").text( "Durchschnittliche Durchlaufzeit1: "+ data.MeanRuntime+" Stunden");
};

//resize Eventgraph
function reposEvent(){
  width = document.getElementById('chart').offsetWidth, height = document.getElementById('chart').offsetHeight;
  svg.attr('width', width).attr('height', height);
  force.size([width, height]).resume();
};

