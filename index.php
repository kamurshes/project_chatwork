<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){

$chatToken = getenv('chatToken');
$chatGroupId = getenv('chatGroupId');

$message = "This is test.\nMy name is test.";

$headers = [
    'X-ChatWorkToken: '.$chatToken
];

$option = [
    'body' => $message
];

$ch = curl_init( 'https://api.chatwork.com/v2/rooms/'.$chatGroupId.'/messages' );
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($option));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

}

?>
