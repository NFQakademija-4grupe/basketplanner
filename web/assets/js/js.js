var isCollapsed = false;

var $body = $('body');
var $logo = $('.has-user-block');

$('.toggler')
    .on('click', function (e) {
        e.preventDefault();
        var classname = 'aside-toggled';
        var classname2 = 'aside-collapsed';
        if (classname) {
            if ($body.hasClass(classname)) {
                $body.removeClass(classname);
                $body.addClass(classname2);
                $logo.attr('style', 'display:none');
            }
            else {
                $body.addClass(classname);
                $body.removeClass(classname2);
                $logo.removeAttr('style');
            }

        }

    });
(function ($) {
    "use strict";
    function matchPanelAnimated() {
        var matchPanelAnimatedContent = $('#match-list .tab-content-wrap');
        if (matchPanelAnimatedContent) {
            matchPanelAnimatedContent.mixItUp();
        };
    }
    $(document).on('ready', function () {
        matchPanelAnimated();
        if( $('aside').css('display')=='none') {
            $body.removeClass('aside-toggled');
            $body.addClass('aside-collapsed');
            $logo.attr('style', 'display:none');
        }
        $('aside').removeClass('mobile');
    });
})(jQuery);