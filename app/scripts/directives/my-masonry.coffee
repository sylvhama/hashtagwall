'use strict'

angular.module('hashtagwallApp').directive 'myMasonry', [() ->
  restrict: 'C'
  link: (scope, element, attrs) ->

    msnry = ''
    container = ''

    scope.applyMasonry = (feed) ->
      for post in feed
        if post.img == '' then img = ''
        else img = '<p><img src="'+post.img+'" alt="Instagram" width="640px" height="640px"></p>'
        $(element).append('<article id="'+post.id_post+'" class="post"><div class="content"><h5><i class="fi-social-'+post.network+'"></i>
        <a href="'+post.url+'" target="_blank">@'+post.user+'</a></h5><p>'+post.text+'</p>'+img+'</div></article>')

      container = document.querySelector('.my-masonry');
      msnry = new Masonry(container, {itemSelector: ".post", columnWidth: ".post"})

      images = $('.post').find('img')
      images.off()
      images.on "load", ->
        msnry.layout()

    scope.addMasonry = (newFeed) ->
      $("html, body").animate({ scrollTop: 0 }, "slow")
      elts = []
      fragment = document.createDocumentFragment()
      for post in newFeed
        if post.img == '' then img = ''
        else img = '<p><img src="'+post.img+'" alt="Instagram" width="640px" height="640px"></p>'
        elt = document.createElement('article')
        elt.className = 'post'
        elt.id = post.id;
        elt.innerHTML = '<div class="content"><h5><i class="fi-social-'+post.network+'"></i>
        <a href="'+post.url+'" target="_blank">@'+post.user+'</a></h5><p>'+post.text+'</p>'+img+'</div>'
        fragment.appendChild(elt)
        elts.push(elt)

      container.insertBefore(fragment, container.firstChild)
      msnry.prepended(elts)

      images = $('.post').find('img')
      images.off()
      images.on "load", ->
        msnry.layout()

    scope.removeMasonry = (hide) ->
      for post in hide
        elt = document.getElementById(post.id_post)
        msnry.remove(elt)
        msnry.layout()
]