(function(){
	var matrixDiv = document.getElementById("e2");
    var abdeckungDiv = document.getElementById("e4");

    var matrixBtn = document.getElementById("Matrix_Btn");
    var abdeckungBtn = document.getElementById("Abdeckung_Btn");
			
	matrixDiv.setAttribute('class', 'visible');
	abdeckungDiv.setAttribute('class', 'hidden');
			
    matrixBtn.onclick = function() {
        abdeckungDiv.setAttribute('class', 'hidden');
        matrixDiv.setAttribute('class', 'visible');
		reposChord();
		redraw()
    };

    abdeckungBtn.onclick = function() {
        matrixDiv.setAttribute('class', 'hidden');
        abdeckungDiv.setAttribute('class', 'visible');
		reposChord();
		redraw()
    };
})();