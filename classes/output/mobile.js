(function() {
    if (!window.postMessage || !window.addEventListener || window.videofrontResizerInitialized) {
        return;
    }
    window.videofrontResizerInitialized = true;

    var actionHandlers = {};

    actionHandlers.hello = function(iframe, data, respond) {

        iframe.style.width = '100%';

        var resize = function(event) {
            if (iframe.contentWindow) {
                respond('resize');
            }
            else {
                window.removeEventListener('resize', resize);
            }
        };
        window.addEventListener('resize', resize, false);

        respond('hello');
    };


    var videofrontPrepareResize = false;
    actionHandlers.prepareResize = function(iframe, data, respond) {
        if (videofrontPrepareResize)
            return;
        videofrontPrepareResize = true;

        if (iframe.clientHeight !== data.scrollHeight ||
            data.scrollHeight !== data.clientHeight) {

            iframe.style.height = data.clientHeight + 'px';
            respond('resizePrepared');
        }
    };

    var videofrontLastResize = -1;
    actionHandlers.resize = function(iframe, data, respond) {

        if (videofrontLastResize == data.scrollHeight)
            return;
        videofrontLastResize = data.scrollHeight;

        console.log("resize");
        iframe.style.height = data.scrollHeight + 'px';
    };

    window.addEventListener('message', function receiveMessage(event) {
        if (event.data.context !== 'videofront') {
            return;
        }

        var iframe, iframes = document.getElementsByTagName('iframe');
        for (var i = 0; i < iframes.length; i++) {
            if (iframes[i].contentWindow === event.source) {
                iframe = iframes[i];
                break;
            }
        }

        if (!iframe) {
            return;
        }

        if (actionHandlers[event.data.action]) {
            actionHandlers[event.data.action](iframe, event.data, function respond(action, data) {
                if (data === undefined) {
                    data = {};
                }
                data.action = action;
                data.context = 'videofront';
                event.source.postMessage(data, event.origin);
            });
        }
    }, false);

    var iframes = document.getElementsByTagName('iframe');
    var ready = {
        context : 'videofront',
        action  : 'ready'
    };
    for (var i = 0; i < iframes.length; i++) {
        if (iframes[i].src.indexOf('videofront') !== -1) {
            iframes[i].contentWindow.postMessage(ready, '*');
        }
    }

})();