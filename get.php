<?php
$curlhandle = curl_init();
curl_setopt($curlhandle, CURLOPT_URL, "http://gdata.youtube.com/feeds/api/videos?v=2&alt=jsonc&q=computers&max-results=10&orderby=viewCount");
curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($curlhandle);
curl_close($curlhandle);

$json = json_decode($response);
print_r($json);
foreach ($json->data->items as $result) {
    echo '<div class="video"><a href="'.$result->player->default.'" target="_blank">';
    echo '<img src="'.$result->thumbnail->hqDefault.'">';
    echo ' <div class="title"> '.$result->title.'</div><div class="rating">'.$result->likeCount.'</div></a></div>';
        //print_r($result);
}