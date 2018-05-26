(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

jQuery(function ($) {
    $(document).on('click', '.notice[data-dismissible-id] .notice-dismiss', function () {
        var dismissibleId = $(this).closest('.notice').data('dismissible-id');
        var url = ajaxurl + '?dismissible-id=' + dismissibleId;

        $.ajax(url, {
            type: 'POST',
            data: {
                'action': 'aff_dismissed_notice'
            }
        });
    });
});

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2NvbW1vbi9qcy9jb21tb24uanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBLE9BQU8sVUFBUyxDQUFULEVBQVk7QUFDZixNQUFFLFFBQUYsRUFBWSxFQUFaLENBQWUsT0FBZixFQUF3Qiw4Q0FBeEIsRUFBd0UsWUFBVztBQUMvRSxZQUFJLGdCQUFnQixFQUFFLElBQUYsRUFBUSxPQUFSLENBQWdCLFNBQWhCLEVBQTJCLElBQTNCLENBQWdDLGdCQUFoQyxDQUFwQjtBQUNBLFlBQUksTUFBUyxPQUFULHdCQUFtQyxhQUF2Qzs7QUFFQSxVQUFFLElBQUYsQ0FBTyxHQUFQLEVBQVk7QUFDUixrQkFBTSxNQURFO0FBRVIsa0JBQU07QUFDRiwwQkFBVTtBQURSO0FBRkUsU0FBWjtBQU1ILEtBVkQ7QUFXSCxDQVpEIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24oKXtmdW5jdGlvbiByKGUsbix0KXtmdW5jdGlvbiBvKGksZil7aWYoIW5baV0pe2lmKCFlW2ldKXt2YXIgYz1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlO2lmKCFmJiZjKXJldHVybiBjKGksITApO2lmKHUpcmV0dXJuIHUoaSwhMCk7dmFyIGE9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitpK1wiJ1wiKTt0aHJvdyBhLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsYX12YXIgcD1uW2ldPXtleHBvcnRzOnt9fTtlW2ldWzBdLmNhbGwocC5leHBvcnRzLGZ1bmN0aW9uKHIpe3ZhciBuPWVbaV1bMV1bcl07cmV0dXJuIG8obnx8cil9LHAscC5leHBvcnRzLHIsZSxuLHQpfXJldHVybiBuW2ldLmV4cG9ydHN9Zm9yKHZhciB1PVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmUsaT0wO2k8dC5sZW5ndGg7aSsrKW8odFtpXSk7cmV0dXJuIG99cmV0dXJuIHJ9KSgpIiwialF1ZXJ5KGZ1bmN0aW9uKCQpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLm5vdGljZVtkYXRhLWRpc21pc3NpYmxlLWlkXSAubm90aWNlLWRpc21pc3MnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgbGV0IGRpc21pc3NpYmxlSWQgPSAkKHRoaXMpLmNsb3Nlc3QoJy5ub3RpY2UnKS5kYXRhKCdkaXNtaXNzaWJsZS1pZCcpO1xuICAgICAgICBsZXQgdXJsID0gYCR7YWpheHVybH0/ZGlzbWlzc2libGUtaWQ9JHtkaXNtaXNzaWJsZUlkfWA7XG5cbiAgICAgICAgJC5hamF4KHVybCwge1xuICAgICAgICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgICdhY3Rpb24nOiAnYWZmX2Rpc21pc3NlZF9ub3RpY2UnLFxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9KTtcbn0pO1xuIl19
