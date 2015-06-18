'use strict'

angular.module('hashtagwallApp').controller 'HomeCtrl', ['$scope', '$http', ($scope, $http) ->

  $scope.hashtag = 'E3'
  feed = []

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
      else
        console.log "[Error][GetFeed] " + data.error
    ).error (data, status) ->
      console.log "[Error][GetFeed] " + status

  getFeed()

]
