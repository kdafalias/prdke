	var w;
    var h;
	var force;
	var linkDistance;
	var edgelabels;

function graph(data,width,height){
	// mittels width und height kann die größe des containers geändert werden
	w = width;
    h = height;
    linkDistance=120;

    var colors = d3.scale.category10();
    var svg = d3.select("#chart").append("svg").attr({"width":w,"height":h});
	
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
		
		// Verbindung einfügen, ev. absichern, falls keine ordentliche Verbindung zurückkommt
    	links.push({
        	source: sourceNode,
        	target: targetNode,
        	time: e.time,
			num: e.num
    	});
	});
	
	// force layout object erstellen und grundsätzliche properties definieren
    force = d3.layout.force()
        .nodes(data.nodes)
		.links(links)
        .size([w,h])
        .linkDistance([linkDistance])
        .charge([-600])
        .theta(0.1)
        .gravity(0.05)
        .start();
	
	// marker für edge setzen	
    var edge = svg.selectAll("path")
      .data(links)
      .enter()
      .append("line")
      .attr("id",function(d,i) {return "edge"+i})
      .attr('marker-end','url(#arrowhead)')
      .style("stroke","#ccc")
      .style("pointer-events", "none");
    
	// Kreis für Node zeichnen
    var nodes = svg.selectAll("circle")
      .data(data.nodes)
      .enter()
      .append("circle")
      .attr({"r":11})
      .style("fill",function(d,i){return colors(i);})
      .call(force.drag);


    var nodelabels = svg.selectAll(".nodelabel") 
       .data(data.nodes)
       .enter()
       .append("text")
       .attr({"x":function(d){return d.x;},
              "y":function(d){return d.y;},
              "class":"nodelabel",
              "stroke":"black"})
       		  .text(function(d){return d.name;});
	
	// Verbindungen zwischen nodes erstellen
	// Beschriftung der Nodes ändern (edgepathtime/ edgepath)
    var edgepaths = svg.selectAll(".edgepath")
        .data(links)
        .enter()
        .append('path')
		.attr({"d": function(d) {
        	var dx = d.target.x - d.source.x,
            dy = d.target.y - d.source.y,
            dr = Math.sqrt(dx * dx + dy * dy);
        	return "M" + d.source.x + "," + d.source.y + "A" + dr + "," + dr + " 0 0,1 " + d.target.x + "," + d.target.y;
    	},
			/*.attr({'d': function(d) {return 'M '+d.source.x+' '+d.source.y+' L '+ d.target.x +' '+d.target.y},*/
        	'class':'edgepath',
        	'fill-opacity':0,
        	'stroke-opacity':0,
        	'fill':'blue',
        	'stroke':'red',
        	'id':function(d,i) {return "edgepath"+i},
			  })
        .style("pointer-events", "none");

	//Beschriftung der Kanten platzieren und definieren
	edgelabels = svg.selectAll(".edgelabel")
        .data(links)
        .enter()
        .append('text')
        .style("pointer-events", "none")
        .attr({'class':'edgelabel',
               'id':function(d,i){return 'edgelabel'+i},
               'dx':85,
               'dy':-8,
               'font-size':6,
               'fill':'black'});
	
	// Pfeil 
    svg.append('defs').append('marker')
        .attr({'id':'arrowhead',
               'viewBox':'-0 -5 10 10',
               'refX':23,
               'refY':0,
               'markerUnits':'strokeWidth',
               'orient':'auto',
               'markerWidth':10,
               'markerHeight':10,
               'xoverflow':'visible'})
        .append('svg:path')
            .attr('d', 'M 0,-5 L 10 ,0 L 0,5')
            .attr('fill', '#ccc')
            .attr('stroke','#ccc');
     

   force.on("tick", function(){

        edge.attr({"x1": function(d){return d.source.x;},
                    "y1": function(d){return d.source.y;},
                    "x2": function(d){return d.target.x;},
                    "y2": function(d){return d.target.y;}
        });

        nodes.attr({"cx":function(d){return d.x;},
                    "cy":function(d){return d.y;}
        });

        nodelabels.attr("x", function(d) { return d.x; }) 
                  .attr("y", function(d) { return d.y; });

         edgepaths.attr('d', function(d) { var path='M '+d.source.x+' '+d.source.y+' L '+ d.target.x +' '+d.target.y;
                                           //console.log(d)
                                           return path});   
	   
	   /*edgepaths.attr("d", function(d) {
        	var dx = d.target.x - d.source.x,
            dy = d.target.y - d.source.y,
            dr = Math.sqrt(dx * dx + dy * dy);
        	return "M" + d.source.x + "," + d.source.y + "A" + dr + "," + dr + " 0 0,1 " + d.target.x + "," + d.target.y;}); */


        edgelabels.attr('transform',function(d,i){
            if (d.target.x<d.source.x){
                bbox = this.getBBox();
                rx = bbox.x+bbox.width/2;
                ry = bbox.y+bbox.height/2;
                return 'rotate(180 '+rx+' '+ry+')';
                }
            else {
                return 'rotate(0)';
                }
        });
    });
}
	function text(){
		// graph ready boolean= true, false wenn alle elemente fertig geladen
		if(document.getElementById("time").checked){
			edgelabels.selectAll("textPath").remove();
	    	edgelabels.append('textPath')
        	.attr('xlink:href',function(d,i) {return '#edgepath'+i})
        	.style("pointer-events", "none")
        	.text(function(d,i){return +d.time})
		}else{
			// Beschriftung der Kanten auf Anzahl setzen
			edgelabels.selectAll("textPath").remove();
			edgelabels.append('textPath')
				.attr('xlink:href',function(d,i) {return '#edgepath'+i})
				.style("pointer-events", "none")
				.text(function(d,i){return +d.num})
		}		
	};
	function MTime(data){
			$("#MTime .txt").text( "Durchschnittliche Durchlaufzeit: "+ data.MeanRuntime+" Stunden");
	};
