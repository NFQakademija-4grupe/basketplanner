var isCollapsed = false;

function collapse(){
	if(isCollapsed){
		document.getElementById('body').className =
	    document.getElementById('body').className.replace( /(?:^|\s)aside-collapsed(?!\S)/g , '' );
		document.getElementById('sidebar_left').style.width = "";
		document.getElementById('sidebar').style.width = "";
	}
	else{
		document.getElementById('body').className += " aside-collapsed";
		document.getElementById('sidebar_left').style.width = "70px";
		document.getElementById('sidebar').style.width = "70px";
	}
	isCollapsed = !isCollapsed;
}
$('body.aside-toggled').removeClass('aside-toggled');

var $body = $('body');

$('.toggler')
    .on('click', function (e) {
        e.preventDefault();
        var classname = 'aside-toggled';

        if(classname) {
            if( $body.hasClass(classname) ) {
                $body.removeClass(classname);
            }
            else {
                $body.addClass(classname);
            }

        }

    });
(function ($) {
	"use strict";


	// upcoming event filter
	function matchPanelAnimated () {
		var matchPanelAnimatedContent = $('#match-list .tab-content-wrap');
		if (matchPanelAnimatedContent) {
			matchPanelAnimatedContent.mixItUp();
		};
	}

	// doc ready
	$(document).on('ready', function () {
		matchPanelAnimated();
	});
})(jQuery);