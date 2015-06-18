<?php
  function getTweets($hashtag, $count) {
    $consumer_key=''; //Provide your application consumer key
    $consumer_secret=''; //Provide your application consumer secret
    $oauth_token = ''; //Provide your oAuth Token
    $oauth_token_secret = ''; //Provide your oAuth Token Secret
    require_once('./twitteroauth/twitteroauth.php');
    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
    //$query = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=sylvhama&count=1'; //Your Twitter API query
    $query = 'https://api.twitter.com/1.1/search/tweets.json?q=%23'.$hashtag.'&count='.$count.'&result_type=recent';
    $response = $connection->get($query);

    foreach ($response->statuses as $tweet) {
      $tweet->created_time = strtotime($tweet->created_at);
    }

    return $response;
  }
?>