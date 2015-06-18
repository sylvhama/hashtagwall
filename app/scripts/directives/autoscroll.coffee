'use strict'

angular.module('hashtagwallApp').directive 'autoscroll', ['$timeout', '$window', ($timeout, $window) ->
  restrict: 'A'

  timeoutId = 0

  link: (scope, element, attrs) ->
    startTimer = () ->
      timeoutId =  $timeout( ->
        if $('.wall').height() > 1000
          if($(window).scrollTop() + $(window).height() != $(document).height())
            $window.scrollBy(0,1);
          else
            $("html, body").animate({ scrollTop: 0 }, "slow")
        startTimer()
      ,100)

    stopTimer = ->
      $timeout.cancel(timeoutId)

    scope.$on "$destroy", () ->
      $timeout.cancel(timeoutId)

    startTimer()

]