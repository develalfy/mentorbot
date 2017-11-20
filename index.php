<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/
$hub_challenge 		= $_REQUEST['hub_challenge'];
$hub_verify_token 	= $_REQUEST['hub_verify_token'];

if ($hub_verify_token === 'develalfyAndMega') { echo $hub_challenge; }

$token = 'EAAbtJ4bWSdcBALbmnNqOGNZB0u7mUAxQQcoaYLCsgTWtOg6ruD11j786fuVgfsCYlPjZA6cl3OkZBsipcuU0ZB39KkUvQPCXiLlD5CzpjnW1zV0cneSGlTq5xOCOIYQr1UMt3tjRFM7gzew3xjgLhcUl0QbJdTUJLQmgk8ZBZBCCgTMxAIZAp7E';

$facebook_data = json_decode( file_get_contents("php://input"), true );

$data = date("Y-m-d H:i:s") . "\n\r" . print_r( $facebook_data, 1 ) . "\n\r";
file_put_contents('output.txt', $data);

// Skip echo Messages
if( isset($facebook_data['entry'][0]['messaging'][0]['message']['is_echo']) ) { die; }

// get user data
$user_id 		= $facebook_data['entry'][0]['messaging'][0]['sender']['id'];
$message_text 	= $facebook_data['entry'][0]['messaging'][0]['message']['text'];

// To do apply NLP to get message details
$message_text = trim($message_text);
$message_text = strtolower($message_text);


$data_to_send = [
	'recipient'	=>	['id'	=>	$user_id],
	'message'	=>	[
		'text'	=>	'Welcome back',
	]
];


// echo action
if( 'echo' == $message_text )
{
	$data_to_send['message']['text'] = json_encode($facebook_data);
}

// form action
if( true || 'form' == $message_text )
{
	$data_to_send['message'] = [
		'attachment' => [
				"type"		=>	"template",
				"payload" 	=> [
					"template_type"	=>	"button",
					"text"			=>	"Hi, What do you want to learn ?",
					"buttons"		=> [
							[
								"type"	=>	"web_url",
								"url"	=>	"https://mentorbot.me/facebook_bot/frontend.png",
								"title"	=>	"Show Front-end Path",
							],

							[
								"type"	=>	"web_url",
								"url"	=>	"https://mentorbot.me/facebook_bot/backend.png",
								"title"	=>	"Show Back-end Path",
							]
					]
				]
		]
	];
}

/*
// joke action
// send me a joke
// tell me a joke
// text me a joke
if( preg_match('/(send|tell|text)(.*?)joke/', $message_text)  )
{
	$get_contents = file_get_contents('http://api.icndb.com/jokes/random');
	$result_array = json_decode($get_contents, true);
	$data_to_send['message']['text'] = $result_array['value']['joke'];
}
else
{
	$log_message = date("Y-m-d H:i:s") . "\n\r" . print_r( $facebook_data['entry'][0]['messaging'], 1 ) . "\n\r";
	file_put_contents('log_message.txt', $log_message, FILE_APPEND);
}
*/

/*
$options = [
	'http'	=>	[
		'method'	=>	'POST',
		'content'	=>	json_encode($temp_data),
		'header'	=>	'Content-type:application/json'
	]
];


$context = stream_context_create( $options );
$uri_with_qs = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$token;
file_get_contents($uri_with_qs, false, $context);
*/

//API Url
$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$token;

//Initiate cURL.
$ch = curl_init($url);

//Encode the array into JSON.
$jsonDataEncoded = json_encode($data_to_send);

//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);

//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

//Execute the request
if(!empty($message_text))
{
	$result = curl_exec($ch);
}
