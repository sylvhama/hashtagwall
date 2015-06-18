'use strict'

angular.module('hashtagwallApp').controller 'WallCtrl', ['$scope', '$http', '$timeout', ($scope, $http, $timeout) ->

  $scope.hashtag = 'E3'
  count = 10
  feed = []
  newFeed = []

  timeoutId = 0
  min = 0
  limit = 3

  compare = (a, b) ->
    t1 = parseInt(a.created_time)
    t2 = parseInt(b.created_time)
    if (t1 > t2) then return -1
    if (t1 < t2) then return 1
    return 0

  getFeed = () ->
    $http.post("./php/do.php?r=selectPosts"
    ).success((data, status) ->
      if !data.error
        console.log 'Feed'
        feed = data
        console.log feed
        feed.sort(compare)
        $scope.applyMasonry(feed)
        startTimer()
      else
        console.log "[Error][GetFeed] " + data.error
    ).error (data, status) ->
      console.log "[Error][GetFeed] " + status

  getNewFeed = (count) ->
    $http.post("./php/wall.php"
      data: {
        hashtag: $scope.hashtag,
        count: count
      }
    ).success((data, status) ->
      if !data.error
        console.log 'New Feed'
        newFeed = data
        console.log newFeed
        newFeed.sort(compare)
        $scope.addMasonry(newFeed)
        startTimer()
      else
        console.log "[Error][GetNewFeed] " + data.error
        startTimer()
    ).error (data, status) ->
      console.log "[Error][GetNewFeed] " + status

  getNotValidated = () ->
    $http.post("./php/do.php?r=selectPostsNotValidated"
    ).success((data, status) ->
      if !data.error
        console.log 'Posts to hide'
        hide = data
        console.log hide
        $scope.removeMasonry(hide)
      else
        console.log "[Error][GetFeed] " + data.error
    ).error (data, status) ->
      console.log "[Error][GetFeed] " + status

  myTimer = ->
    min++
    console.log min

  startTimer = () ->
    timeoutId =  $timeout( ->
      myTimer()
      if min == 1
        getNotValidated()
      if min == limit
        stopTimer()
      else
        startTimer()
    ,60000)

  stopTimer = ->
    $timeout.cancel(timeoutId)
    min = 0
    getNewFeed(count)

  $scope.$on "$destroy", () ->
    $timeout.cancel(timeoutId);

  getFeed()

]
