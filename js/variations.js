/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
  sendWebRequest();	// initial werden alle daten angezeigt
  $('.menubutton').click(function () {
    $('nav').slideToggle('slow');
  });

  // click function, für die Varianten
  $('div#e4').on('click', "rect", function (event) {
    event.stopPropagation();
    var id = $(this).attr('data-id');	// VariationID des geclickten rect's
    d = getDat();	// Datenobjekt holen
    // ganzes Array durchsuchen
    $.each(d.Variation, function (index, o) {
      // id gefunden
      if (o.VariationID == id) {
        // visible ist auf ja = true
        if (o["vis"] == true) {
          o["vis"] = false;
          variations.push(id);
          allCoverage = false;
          // visible ist auf nein , kennzeichen wird wieder auf true gesetzt
          // variations wird um dieses ergänzt
        } else {
          o["vis"] = true;
          variations.splice(id, 1);
        }
      }
    });
    console.log(variations);
    sendWebRequest();
  });
  $('div#e4').on('click', 'svg', function (event) {
    allCoverage = true;
    highlightActivities(getDat());
    variations = new Array();
    sendWebRequest();
  });
});
