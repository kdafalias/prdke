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
	console.log(width);
	force = d3.layout.force()
		.nodes(data.nodes)
		.links(links)
		.size([width, height])
		.linkDistance(linkDistance)
		.charge(-width)
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
	   var x1 = d.source.x,
          y1 = d.source.y,
          x2 = d.target.x,
          y2 = d.target.y,
          dx = x2 - x1,
          dy = y2 - y1,
          dr = Math.sqrt(dx * dx + dy * dy),

          // Default für normale Kanten
          drx = dr,
          dry = dr,
          xRotation = 0, // winkelgrad
          largeArc = 0, // 1 oder 0
          sweep = 1; // 1 oder 0

          // Kanten für Knoten mit selben ein und ausgangsknoten
          if ( x1 === x2 && y1 === y2 ) {
            xRotation = -45;
			largeArc = 1;
            sweep = 0;

            // elliptische Verbindung zwischen beiden Knoten= kein Kreis
            drx = 30;
            dry = 20;
            
            // der Start und Endpunkt muss minimal auseinanderklaffen, ansonsten wird keine Verbindung angezeigt
            x2 = x2 + 1;
            y2 = y2 + 1;
          } 

     return "M" + x1 + "," + y1 + "A" + drx + "," + dry + " " + xRotation + "," + largeArc + "," + sweep + " " + x2 + "," + y2;
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
		edgelabels.text(function(d) { return d.time; })
	}else{
		// Beschriftung der Kanten auf Anzahl setzen
		edgelabels.text(function(d) { return d.num; })
	}		
};
//Beschriftungen
function MTime(data){
	$("#navmiddle .txt").text( "Durchschnittliche Durchlaufzeit1: "+ data.MeanRuntime+" Stunden");
};

//resize Eventgraph
function reposEvent(){
	console.log("hi");
  width = document.getElementById('chart').offsetWidth, height = document.getElementById('chart').offsetHeight;
  svg.attr('width', width).attr('height', height);
  force.size([width, height]).resume()
		.charge(-width);
};

