function resize(){
	
	var chart = document.getElementById("#chart");
	var svg= chart.contentDocument;
	var force=svg.getElementById("force");
	   // get width/height with container selector (body also works)
    // or use other method of calculating desired values
    var width = $('#chart').width(); 
    var height = $('#chart').height(); 

    // set attrs and 'resume' force 
    svg.attr('width', width);
    svg.attr('height', height);
    force.size([width, height]).resume();
}