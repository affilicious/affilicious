(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2NvbW1vbi9qcy9jb21tb24uanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBLE9BQU8sVUFBUyxDQUFULEVBQVk7QUFDZixNQUFFLFFBQUYsRUFBWSxFQUFaLENBQWUsT0FBZixFQUF3Qiw4Q0FBeEIsRUFBd0UsWUFBVztBQUMvRSxZQUFJLGdCQUFnQixFQUFFLElBQUYsRUFBUSxPQUFSLENBQWdCLFNBQWhCLEVBQTJCLElBQTNCLENBQWdDLGdCQUFoQyxDQUFwQjtBQUNBLFlBQUksTUFBUyxPQUFULHdCQUFtQyxhQUF2Qzs7QUFFQSxVQUFFLElBQUYsQ0FBTyxHQUFQLEVBQVk7QUFDUixrQkFBTSxNQURFO0FBRVIsa0JBQU07QUFDRiwwQkFBVTtBQURSO0FBRkUsU0FBWjtBQU1ILEtBVkQ7QUFXSCxDQVpEIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsImpRdWVyeShmdW5jdGlvbigkKSB7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5ub3RpY2VbZGF0YS1kaXNtaXNzaWJsZS1pZF0gLm5vdGljZS1kaXNtaXNzJywgZnVuY3Rpb24oKSB7XG4gICAgICAgIGxldCBkaXNtaXNzaWJsZUlkID0gJCh0aGlzKS5jbG9zZXN0KCcubm90aWNlJykuZGF0YSgnZGlzbWlzc2libGUtaWQnKTtcbiAgICAgICAgbGV0IHVybCA9IGAke2FqYXh1cmx9P2Rpc21pc3NpYmxlLWlkPSR7ZGlzbWlzc2libGVJZH1gO1xuXG4gICAgICAgICQuYWpheCh1cmwsIHtcbiAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICAnYWN0aW9uJzogJ2FmZl9kaXNtaXNzZWRfbm90aWNlJyxcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSk7XG59KTtcbiJdfQ==
