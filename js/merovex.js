// Copyright 2009, Benjamin C. Wilson. Rights Reserved.
//Event.observe(window, 'load', init);
function init(){ initSceneCalc(); initBodyCalc();}
function initSceneCalc() { $('#scenecalculator').keyup(function(event){ calcScenes(); }); calcScenes();}
function initBodyCalc() { $('#fatbodycalculator').keyup(function(event){ calcBf(); }); calcBf();}
function addRow(vpc, count) {
    var table = $('scenecalculator_table');
    //var rowCount = table.rows.length;
    //var row = table.insertRow(rowCount);
    var row = '<tr class=\'temp\'><td>'+vpc+" Scenes</td><td colspan='4'><input size='4' class='inputbox' value='"+count+"' /></td></tr>";
    $('#scenecalculator_table').append(row);
}
function getVal(key) { return parseFloat( $("input[id^='"+key+"']").val()); }
function setVal(key,value) { $("input[id="+key+"]").val(value); }
function calcScenes() {
    $('.temp').remove();

    var kwords = getVal('kwords');
    if (isNaN(kwords) || kwords < 30) {return;}
    if (kwords > 250) { kwords = 250; setVal('kwords',kwords); } 

    kwords = kwords - (kwords % 5);
    var scenes = Math.ceil(kwords / 1.250);
    var hero = Math.round(scenes * .6);
    var balance = scenes - hero;
    var per = Math.floor(hero / 4);
    var alt = hero % 4;
    var a1 = per; var a2 = per; var a3 = per; var a4 = per;
    if (alt >= 1) { a1 += 1; }
    if (alt >= 2) { a3 += 1; }
    if (alt >= 3) { a2 += 1; }
    if (alt == 4) { a4 += 1; }

    setVal('scene_total', scenes);
    setVal('scenes_per_act',  scenes / 4);
    setVal('hero',  hero );
    setVal('hero1',  a1 );
    setVal('hero2',  a2 );
    setVal('hero3',  a3 );
    setVal('hero4',  a4 );


    if (balance < 20) { setVal('villain', balance); return; }
    if (balance < 30) {
        var v = Math.ceil(balance / 2);
        setVal('villain', v);
        addRow('Confidant (#3)', balance - v);
    }
    else if (balance > 29){
        var vps = Math.round(balance / 10);
        var left = vps;

        for(i=0;i<vps;i++) {
          var per = (balance / left--);
          var count = Math.ceil(per);
          balance -= count;

          var v = i + 2;
          if (v==2) { setVal('villain',  count); continue; } 
          if (v==3) { v = 'Confidant'} 
          else      { v = "VP#"+v    }
          addRow(v, count);
        }
    }
}
// Contact Me.
var cms = document.createElement('script'); 
var _cmo = { form: '4e24fc031ffafb000101e40b', text: 'Contact Me', align: 'left', valign: 'middle', lang: 'en', background_color: '#AF2E2E'}; 
cms.type = 'text/javascript';
cms.async = true;
cms.src = ('https:' == document.location.protocol ? 'https://d1uwd25yvxu96k.cloudfront.net' : 'http://static.contactme.com') + '/widgets/tab/v1/tab.js';
function putContactMeButton(s) { s.parentNode.insertBefore(cms, s); }

function calcBf() {
  //if (!$("input[name^='neck']")) { return; }
  var neck   = getVal('neck');
  var hips   = getVal('hips');
  var waist  = getVal('waist');
  var height = getVal('height');
  var weight = getVal('weight');
  if (waist == 0) {
   setVal('gender','Male');
  }
  else {
   setVal('gender','Female');
  }
}
