// save player callbacks and load javascript dynamically
var _sti = []; var _stl = false;
jQuery(function() {
  var st_test_re = /ooyala|youtube|vimeo|brightcove|blip|soundcloud/;
  if( jQuery("div.STplayer").length != 0 )
    loadSTjs();
});
function loadSTjs() { if( !_stl ) { _stl = true; jQuery.getScript("http://jb.speakertext.com/player/jquery.speakertext.js", function() { passVPCallbacks(_sti); } ); } }
function onTemplateLoaded(experienceID) { _sti.push( ['onTemplateLoaded', [experienceID]] ); loadSTjs();} // Brightcove
function onYouTubePlayerReady(playerID) { _sti.push( ['onYouTubePlayerReady', [playerID]] ); loadSTjs(); } // YouTube
function st_ooyala_callback(playerId, eventName, eventParams) { _sti.push( ['st_ooyala_callback', [playerId, eventName, eventParams]] ); loadSTjs(); } // Ooyala
function getUpdate(type, arg1, arg2) { _sti.push( ['getUpdate', [type, arg1, arg2]] ); loadSTjs(); } // Blip.tv
function playerReady(obj) { _sti.push( ['playerReady', [obj]] ); loadSTjs(); } // jwPlayer