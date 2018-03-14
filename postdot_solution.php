<?php

require_once('TwitterAPIExchange.php');
require_once('config/config.php');

/** sets authentication properties **/
$settings = array(
    'oauth_access_token' => YOUR_OAUTH_ACCESS_TOKEN,
    'oauth_access_token_secret' => YOUR_OAUTH_ACCESS_TOKEN_SECRET,
    'consumer_key' => YOUR_CONSUMER_KEY,
    'consumer_secret' => YOUR_CONSUMER_SECRET
);

$twitter = new TwitterAPIExchange($settings);

/** defines twitter api level details for user api **/
$api_user_details = "https://api.twitter.com/1.1/users/show.json";
$requestMethod = "GET";
$getfield = '?screen_name=yousufsyed5';

/** calls user api and handles response **/
$api_user_response =  json_decode($twitter->setGetfield($getfield)
             ->buildOauth($api_user_details, $requestMethod)
             ->performRequest());

if($response->errors[0]->message != "") {
	echo "twitter api returned error";
	exit();
}

/** user's follower count **/
$followers_count = $response->followers_count;

/** defines twitter api level details for retweets api **/
$api_user_retweets = "https://api.twitter.com/1.1/statuses/retweeters/ids.json";
$getfield = '?id=972628124893671432&cursor=12893764510938&stringify_ids=true';
$response =  json_decode($twitter->setGetfield($getfield)
             ->buildOauth($api_user_retweets, $requestMethod)
             ->performRequest());


if($response->errors[0]->message != "") {
	echo "twitter api returned error";
	exit();
}

/** number of people that have retweeted this tweet **/
$number_of_retweeters = count($response->ids);

$retweet_reachability_tracker = 0;

/** loops through each retweeter id and aggregates thier followers **/
// for($i=0; $i<$number_of_retweeters-1; $i++){
for($i=0; $i<5; $i++){
	$user_id = $response->ids[$i];	
	$getfield = '?user_id='.$user_id;	
	$api_user_response =  json_decode($twitter->setGetfield($getfield)
             ->buildOauth($api_user_details, $requestMethod)
             ->performRequest());
	
	if($api_user_response->errors[0]->message != "") {
		echo "twitter api returned error";
		exit();
	}

	$retweeters_follower_count = $api_user_response->followers_count;
	$retweet_reachability_tracker += $retweeters_follower_count;
}
/** calculate reachability value and prints to stdout **/
$reach_aggregator = $followers_count + $retweet_reachability_tracker;
print_r(array('total_users_reached'=>$reach_aggregator));




