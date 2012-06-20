// Copyright 2009, Benjamin C. Wilson. Rights Reserved.
// Event.observe(window, 'load', init);
function init(){
    initCalcProd();
    initEveSkills();
    //scrollarea('page');
}
function accribitz(cost, times) {
    if (times == 0) { return 0; }
    if (times == 1) { return cost; }
    for (i=0; i<times; i++) { cost = Math.ceil(cost * 1.5); }
    return cost;
}
function scrollarea(target) {
    if (!$(target)) { return; }
    if (!$('trigger')) { return; }
    $(target).style.height = (document.viewport.getHeight() - 150) + 'px'; $(target).style.overflow = 'auto';
}
function tweakProd(n) {
    $("input[name^='"+n+"']").prop('class','span2');
    var r = parseFloat( $("input[name^='"+n+"']").parseNumber());
    if (isNaN(r)) { r = 0 };
    var c = 'astro-' + n;
    $.cookie(c, r);
    return r;
}
function costProd() {
    pCost('MF'); pCost('RF'); pCost('NF'); pCost('AF'); pCost('SY'); pCost('OSY'); pCost('balanced');
    pCost('CM2'); pCost('CM3'); pCost('PM2'); pCost('PM3');pCost('CY');
    var mr = tweakProd('MF'); var rf = tweakProd('RF'); var nf = tweakProd('NF');
    var af = tweakProd('AF'); var sy = tweakProd('SY'); var osy = tweakProd('OSY');
    var cm2 = tweakProd('CM2'); var cm3 = tweakProd('CM3');
    var pm2 = tweakProd('PM2'); var pm3 = tweakProd('PM3'); var cyb = tweakProd('CY');

    var m_cost = accribitz(1, mr); var r_cost = accribitz(5, rf); var n_cost = accribitz(80, nf);
    var a_cost = accribitz(1000, af); var s_cost = accribitz(5, sy); var o_cost = accribitz(10000, osy);
    var cy_cost = accribitz(8192, cyb); var total = m_cost + r_cost + n_cost + a_cost + s_cost + o_cost;
    $('#counter-MF').text("cum: "+parseInt(m_cost)); $('#counter-RF').text("cum: "+parseInt(r_cost));
    $('#counter-NF').text("cum: "+parseInt(n_cost)); $('#counter-AF').text("cum: "+parseInt(a_cost));
    $('#counter-SY').text("cum: "+parseInt(s_cost)); $('#counter-OSY').text("cum: "+parseInt(o_cost));
    $('#counter-CM3').text("per: "+parseInt(total/cm3)); $('#counter-PM3').text("per: "+parseInt(total/pm3));
    $('#counter-CM2').text("per: "+parseInt(total/cm2)); $('#counter-PM2').text("per: "+parseInt(total/pm2));
    $('#counter-CY').text("cum: "+parseInt(cy_cost)); $('#counter-balanced').text("Total: "+parseInt(total));
}
function pCost(id) { if ($('#counter-'+id).length == 0){ $('<div class="costcounter" id="counter-'+id+'"></div>').insertAfter('#'+id); } }
function unfreeze(n) { $("input[name^='"+n+"']").prop('disabled',false); }
function freeze(n) { $("input[name^='"+n+"']").prop('disabled',true); }
function calcProd() {
    var balanced = $('#balanced').val();
    if (balanced ==1) {
        var mr = tweakProd('MF'); freeze('RF'); freeze('NF'); freeze('AF'); if (isNaN(mr)) { mr = 0; };
        rf = mr - 4; nf = mr - 11; af = mr - 17; if (mr < 0) mr = 0; if (rf < 0) rf = 0; if (nf < 0) nf = 0; if (af < 0) af = 0;
        $("input[name=RF]").val(rf); $("input[name=NF]").val(nf); $("input[name=AF]").val(af);
    }
    else { unfreeze('RF'); unfreeze('NF'); unfreeze('AF'); }

    var mr = tweakProd('MF'); var rf = tweakProd('RF') * 2; var nf = tweakProd('NF') * 4;
    var af = tweakProd('AF') * 6; var sy = tweakProd('SY') * 2; var osy = tweakProd('OSY') * 8; var cy = tweakProd('CY');

    var cyber = 1 + (0.05 * cy); var shipyards = sy + osy; var fixed = rf + nf + af; var m2 = mr * 2; var m3 = mr * 3;
    $("input[name^='CM2']").val( parseInt((fixed + m2) * cyber) ); $("input[name^='CM3']").val( parseInt((fixed + m3) * cyber) );
    $("input[name^='PM2']").val( parseInt((fixed + m2 + shipyards) * cyber) );
    $("input[name^='PM3']").val( parseInt((fixed + m3 + shipyards) * cyber) );
    costProd();
}
function initCalcProd() {
    if (!$('#prod')) { return; }
    $('#prod').keyup(function(event){ calcProd(); }); 
    $('#balanced').change(function(event){ calcProd(); }); 
    calcProd();
}

function spHour(p,s) { return ((p + (s/2)) * 60); }
function initEveSkills() { 
    if (!$("input[name^='Perc']")) { return; }
    if ($.cookie('eve-perception')) {
        $("input[name^='Int']").val( $.cookie('eve-intelligence') );
        $("input[name^='Perc']").val( $.cookie('eve-perception') );
        $("input[name^='Char']").val( $.cookie('eve-charisma') );
        $("input[name^='Wil']").val( $.cookie('eve-willpower') );
        $("input[name^='Mem']").val( $.cookie('eve-memory') );
    }

    $('#attributes').keyup(function(event){ calcEveSkills(); }); calcEveSkills();
}
function tweakEveSkill(n, c) {
    var r = parseFloat( $("input[name^='"+n+"']").parseNumber());
    if (r < 17 || isNaN(r)) {r = 17}
    $.cookie(c, r);
    return r;
}
function calcEveSkills() {
    var prc       = tweakEveSkill('Perc','eve-perception'); var intel     = tweakEveSkill('Int','eve-intelligence');
    var charisma  = tweakEveSkill('Char','eve-charisma'); var willpower = tweakEveSkill('Wil','eve-willpower');
    var memory    = tweakEveSkill('Mem','eve-memory');

    $("input[name=CI]").val(spHour(charisma,intel)); $("input[name=CM]").val(spHour(charisma,memory));
    $("input[name=CW]").val(spHour(charisma,willpower)); $("input[name=IM]").val(spHour(intel,memory));
    $("input[name=IP]").val(spHour(intel,prc)); $("input[name=MC]").val(spHour(memory,charisma));
    $("input[name=MI]").val(spHour(memory,intel)); $("input[name=MP]").val(spHour(memory,prc));
    $("input[name=PM]").val(spHour(prc,memory)); $("input[name=PW]").val(spHour(prc,willpower));
    $("input[name=WP]").val(spHour(willpower,prc)); $("input[name=WC]").val(spHour(willpower,charisma));
    var allbut = (intel + charisma + willpower + memory)/4; $("input[name=AB]").val(spHour(allbut,allbut));
    updateBurnRate();
}
function hoursToString(hours) {
  var burn = '';
  if (hours > 24)  { days = Math.floor(hours / 24); hours = hours - (days * 24); burn = days + 'd ' }
  minutes = Math.round((hours - Math.floor(hours)) * 60); hours = Math.floor(hours); return burn + hours + 'h ' + minutes + 'm';
}
function updateBurnRate() {
   var skill = 0; var timetotal = 0; var oldsphr = 1770;
   $('.skillpoints').each(function(i) {
      var burn = ''; skill = $(this).attr('max');
      sphr = $(this).parent().find('.sprate').attr('value');
      if (sphr < 0 || isNaN(sphr)) {sphr = oldsphr};
      hours = skill / sphr; timetotal = timetotal + hours; $(this).html(hoursToString(hours));
   }); 
   if ($('#grandtotal')) { $('#grandtotal').html(hoursToString(timetotal)); }
}
function makeHighlightable() {
    $('.hiable').hover(function() {
        var t = $(this).text();
        $('#page').removeHighlight().highlight(t);
    }, function() {
        $('#page').removeHighlight();
    });
}
