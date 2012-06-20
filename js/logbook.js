/* TWEAK set pathToPmWikiRoot to whatever is relevant to your installation for example if PmWiki lives in http://your.domain/pmwiki/ var pathToPmWikiRoot='/pmwiki/';. ATTENTION leading and trailing / matter! */

var pathToPmWikiRoot='/';

var clicklink = function (url) {

    return function (evt) {
        if (evt && evt.preventDefault) {
            evt.preventDefault();
            evt.stopPropagation();
        } else if (typeof(event) != 'undefined') {
            event.cancelBubble = false;
            event.returnValue = false;
        }

        var doReplace = function (req) {
            $('logbook').innerHTML = req.responseText;
        };

        var doReplaceError = function () {
            $('logbook').innerHTML = '(:logbook:) bug';
        };

        var res = MochiKit.Async.doSimpleXMLHttpRequest(url);
        res.addCallbacks(doReplace,doReplaceError);

    }
};

var convertA = function (linkelement) {

    var link=linkelement.toString();
    var index=link.indexOf('log_month=');
    var log_month=link.substr(index,link.length);
    var href=pathToPmWikiRoot+'cookbook/logbook.php?'+log_month;

    MochiKit.DOM.addToCallStack(linkelement,
                                'onclick',
                                clicklink(href)
                               );
};

var initpage = function () {
    MochiKit.Base.map(convertA,
                      MochiKit.DOM.getElementsByTagAndClassName('a','async')
                     );
};

MochiKit.DOM.addLoadEvent(initpage);

function logbookShow(what) {

    var res=MochiKit.Async.doSimpleXMLHttpRequest(
                pathToPmWikiRoot+'cookbook/logbook.php?log_month='+what
            );

    res.addCallback(
            function (req) {
                $('logbook').innerHTML = req.responseText;
            }
        );
}
