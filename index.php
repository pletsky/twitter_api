<?php
//header('Content-Type: application/json');
/*
header('Access-Control-Allow-Origin: mysite.com');
$http_origin = $_SERVER['HTTP_ORIGIN'];
if ( strrpos($http_origin, "mysite1.net") || strrpos($http_origin, "mysite2.com") ){  
    header("Access-Control-Allow-Origin: $http_origin");
}
*/

ini_set('display_errors', 1);
require_once('functions/debug.php');
require_once('classes/MyTwitterAPI.php');

$ar_twitter_accounts = array(
    'pletsky',
    'viterzbajraku',
);

$ar_twitter_keywords = array(
    'SEO',
    'Kyiv',
    'Kiev',
    '...',
);


// Set access tokens here - see: https://dev.twitter.com/apps/
$settings = array(
    'oauth_access_token' => "",
    'oauth_access_token_secret' => "",
    'consumer_key' => "",
    'consumer_secret' => ""
);


$twitter = new MyTwitterAPI($settings);
//$ar = json_decode($twitter->getUsersByScreenNames($ar_twitter_accounts));
//$ar_followers_list = $twitter->getFollowersByScreenName($ar_twitter_accounts[0]);
$ar_followers_profiles = $twitter->getFollowersProfileText($ar_twitter_accounts[0]);
vdump_e($ar_followers_profiles, '$ar_followers_profiles');
foreach ($ar_followers_profiles as $k=>$v) {
    $cnt = 0;
    $result = preg_replace(array_map(function ($str) {return '/('.$str.')/';}, $ar_twitter_keywords), '<b>$1</b>', $v, -1, $cnt);
    if ($cnt) {
        echo $result.'<hr>';
    }
}
exit;

$ar_followers_list = $twitter->getFollowersScreenNames($ar_twitter_accounts[0]);
vdump($ar_followers_list, '$ar_followers_list');
$ar_followers_info = $twitter->getUsersProfile(implode(',', $ar_followers_list));
vdump($ar_followers_info, '$ar_followers_info');

