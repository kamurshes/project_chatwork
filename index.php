<?php

// var_dumpを文字列に変換する関数を作成
function get_str_var_dump($var){
    ob_start(); // バッファリングON
    var_dump($var);
    $bar = ob_get_contents(); // バッファの内容を変数に格納
    ob_end_clean(); // バッファを消去してバッファリングOFF
    return $bar;
}


if($_SERVER["REQUEST_METHOD"] == "POST"){

if(isset($_POST)) {
	get_str_var_dump($_POST);
}


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
