function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return false;
}


(function( $ ) {
  'use strict';

  $(document).ready(function(){
    var mcInterval = setInterval(function(){
      let list = window.MC.getWidgetList();
      for(var i = 0; i < list.length; i++){
        if(list[i].type == "checkbox"){
          let ref = window.MC.getWidget(list[i].widgetId).userRef;
          if(ref){
            setCookie("mc_ref", ref, 30);
            clearInterval(mcInterval);
            mcInterval = 0;
          }
        }
      }
    }, 300);
  });


})( jQuery );
