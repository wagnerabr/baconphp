/*
 *	BaconPHP's Bootstrap js Includer
 */

function include(js) {
	head   = document.documentElement.getElementsByTagName('head')[0];
	script = document.createElement('script');
	
	script.setAttribute('src', js);
	script.setAttribute('type', 'text/javascript');
	
	head.appendChild(script);
}

include("/baconblog/resource/script/bootstrap/bootstrap-alerts.js");
include("/baconblog/resource/script/bootstrap/bootstrap-dropdown.js");
include("/baconblog/resource/script/bootstrap/bootstrap-modal.js");
include("/baconblog/resource/script/bootstrap/bootstrap-scrollspy.js");
include("/baconblog/resource/script/bootstrap/bootstrap-tabs.js");
include("/baconblog/resource/script/bootstrap/bootstrap-twipsy.js");
//include("/baconblog/resource/script/bootstrap/bootstrap-popover.js");