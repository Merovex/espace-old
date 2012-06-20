var clicklink = function (url) {
    return function (evt) {
        // prevent the normal 'click' action for a link
        evt.stopPropagation();
        evt.preventDefault();
        var doReplace = function (req) {
            $('infotarget').innerHTML = req.responseText;
        };
        var doReplaceError = function () {
            $('infotarget').innerHTML = 'Error!!!';
        };
        var res = MochiKit.Async.doSimpleXMLHttpRequest(url);
        res.addCallbacks(doReplace,doReplaceError);
    }
};
var convertA = function (linkelement) {
    MochiKit.DOM.addToCallStack(linkelement,'onclick',clicklink(linkelement.href));
};
var initpage = function () {
    MochiKit.Base.map(convertA,MochiKit.DOM.getElementsByTagAndClassName('a','async'));
};
MochiKit.DOM.addLoadEvent(initpage);
