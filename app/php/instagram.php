<?php
  //Ask permission: https://instagram.com/oauth/authorize/?client_id=6ffc3278a74040df9104f90f866002f8&redirect_uri=http://localhost&response_type=token
  //Permission result: http://localhost/#access_token=607829426.6ffc327.cd3676375dc54c188e0d129c5be6cb96#/
  //Recent hashtags https://api.instagram.com/v1/tags/pokemon/media/recent?access_token=607829426.f59def8.4e7de07e96b34d2f9b086971d0c227b1&count=2

  function getInstas($hashtag, $count) {
    $query = 'https://api.instagram.com/v1/tags/'.$hashtag.'/media/recent?access_token=TOKEN&count='.$count;
    $response = file_get_contents($query);
    return json_decode($response);
  }
?>