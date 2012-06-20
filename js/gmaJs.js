// Copyright (c) 2006, Benjamin C. Wilson. All Rights Reserved.
// Google Map API for PmWiki.
// This copyright statement must accompany this script.

var geocoder = new GClientGeocoder();

var linked = Array();

var miniIcon = new GIcon();
miniIcon.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
miniIcon.iconSize = new GSize(12, 20);
miniIcon.shadowSize = new GSize(22, 20);
miniIcon.iconAnchor = new GPoint(6, 20);
miniIcon.infoWindowAnchor = new GPoint(5, 1);

var stdIcon = new GIcon();
stdIcon.iconSize = new GSize(32, 32);
stdIcon.iconAnchor = new GPoint(9, 34);
stdIcon.shadowSize = new GSize(59, 32);
stdIcon.infoWindowAnchor = new GPoint(9, 2);
stdIcon.infoShadowAnchor = new GPoint(18, 25);

var markerIcon = new GIcon();
markerIcon.shadow = "$shadow";
markerIcon.iconSize = new GSize(20, 34);
markerIcon.shadowSize = new GSize(37, 34);
markerIcon.iconAnchor = new GPoint(9, 34);
markerIcon.infoWindowAnchor = new GPoint(9, 2);
markerIcon.infoShadowAnchor = new GPoint(18, 25);


function showAddress(address) {
  geocoder.getLatLng(
    address,
    function(point) {
      if (!point) {
        alert(address + " not found");
      } else {
        map.setCenter(point, 13);
        var marker = new GMarker(point);
        map.addOverlay(marker);
        marker.openInfoWindowHtml(address);
      }
    }
  );
}
function setGmaMapCenter(map, type, zoom,lat,lon) {
  if (!zoom) zoom = map.getBoundsZoomLevel(bounds);
  var clat = (lat==null)
    ? (bounds.getNorthEast().lat() + bounds.getSouthWest().lat()) /2
    : lat;
  var clon = (lon==null)
    ? (bounds.getNorthEast().lng() + bounds.getSouthWest().lng()) /2
    : lon;
  map.setCenter(new GLatLng(clat,clon), zoom, type);
}
// This function picks up the click and opens the corresponding info window
function makeMarkerIcon(ba,ov) {
  var label = {
               'anchor':new GLatLng(4,4), 
               'size':new GSize(12,12),
               'url':overlay[ov], 
              };
  var icon = new GIcon(G_DEFAULT_ICON, background[ba], label);
  return icon;
}
function addGmaPoint(map,lat,lon,name,msg,fromto,icon) {
  var point = new GLatLng(lat,lon); 
  var marker = new GMarker(point);
  // TODO: Put Iconic stuff here.
  // http://www.econym.demon.co.uk/googlemaps/examples/label.htm
  //icon = makeMarkerIcon(icon);
  //var marker = new GMarker(point,icon);
  bounds.extend(point);

  // The info window version with the 'to here' form open
  if (msg) {
    name = (name) ? '<b>'+name+'</b>\n' : '';
    var to_directions = '';
    var from_directions = '';
    var inactive = '';
    if (fromto) {
        to_directions = '<br>Directions: <b>To here</b> -'
                    + '<a href="javascript:fromhere(' + i + ')">From here</a>' 
                    + '<br>Start address:<form action="http://maps.google.com/maps" method="get" target="_blank">' 
                    + '<input type="text" size=40 maxlength=80 name="saddr" id="saddr" value="" /><br>' 
                    + '<input value="Get Directions" type="submit">' 
                    + '<input type="hidden" name="daddr" value="'
                    + point.lat() + ',' + point.lng() + '"/>';
        // The info window version with the 'to here' form open
        from_directions = '<br>Directions: <a href="javascript:tohere(' + i + ')">To here</a> - <b>From here</b>' 
                    + '<br>Start address:<form action="http://maps.google.com/maps" method="get" target="_blank">' 
                    + '<input type="text" size=40 maxlength=80 name="daddr" id="saddr" value="" /><br>' 
                    + '<input value="Get Directions" TYPE="submit">' 
                    + '<input type="hidden" name="daddr" value="'
                    + point.lat() + ',' + point.lng() + '"/>';
        inactive = '<br />Directions: <a href="javascript:tohere('+i+')">To here</a> - <a href="javascript:fromhere('+i+')">From here</a>';
      }
      // The inactive version of the direction info
      //msg = name + msg + '<br />Directions: <a href="javascript:tohere('+i+')">To here</a> - <a href="javascript:fromhere('+i+')">From here</a>';

      to_htmls[i] = msg + to_directions
      from_htmls[i] = msg + from_directions
      msg = name + msg + inactive;
      GEvent.addListener(marker, 'click', function() { marker.openInfoWindowHtml(msg); map.panTo(point); });
  }
  points[i] = point;
  markers[i] = marker;
  htmls[i] = msg;
  i++;
  return marker;
}
function tohere(k) { markers[k].openInfoWindowHtml(to_htmls[k]); }
function fromhere(k) { markers[k].openInfoWindowHtml(from_htmls[k]); }
function makeGmalink(map, k) { 
    map.panTo(points[k]);
    markers[k].openInfoWindowHtml(htmls[k]); 
    window.scrollTo(map.top,0);
}
function doGmaOverlay(map) { for (k in markers) map.addOverlay(markers[k]); }
