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
    ob_start(); // バッファリングON
    var_dump($_POST);
    $bar = ob_get_contents(); // バッファの内容を変数に格納
    ob_end_clean(); // バッファを消去してバッファリングOFF
}
error_log($bar);

$chatToken = getenv('chatToken');
$chatGroupId = getenv('chatGroupId');


// POSTデータを取得する

// 名前
$name=$_POST['data']['User']['name1'];
// メールアドレス
$email=$_POST['data']['User']['mail'];
// 電話番号
$tel=$_POST['data']['User']['tel'];
// シナリオ
$scenario=$_POST['data']['User']['scenario'];
// infotopかnoteかを判定する
$infoornote=$_POST['data']['User']['infoornote'];
// 登録のあったinfotop or note のID
$id=$_POST['data']['User']['id'];

$message = "シナリオ：". $_POST['data']['User']['scenario']."\n".
	"氏名：".$name."\n".
	"メールアドレス：".$email."\n".
	"電話番号：".$tel;

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

// ここからデータベース接続をし、テーブルにデータを格納する

	//データベースに格納する
	error_log("=================================");
	error_log("STEP1:データベースに接続をする");
	$pdo = new PDO('mysql:host='.getenv('SERVER').';dbname='.getenv('DATABASE').';charset=utf8',getenv('USERNAME'),getenv('PASSWORD'),array(PDO::ATTR_EMULATE_PREPARES => true));
	error_log("STEP2:SQL構文を作成する");
	if($infoornote==="infotop"){$INSERT=$pdo ->prepare('INSERT INTO users(name, email, tel, scenario,infotop) VALUES (:name,:email,:tel,:scenario,:id)');}
	if($infoornote==="note"){$INSERT=$pdo ->prepare('INSERT INTO users(name, email, tel, scenario,note) VALUES (:name,:email,:tel,:scenario,:id)');}
	error_log("STEP3-1:名前を設定する：".$name);
	$INSERT->bindParam(':name',$name,PDO::PARAM_STR);
	error_log("STEP3-2:メールアドレスを設定する：".$email);
	$INSERT->bindParam(':email',$email,PDO::PARAM_STR);
	error_log("STEP3-3:電話番号を設定する：".$tel);
	$INSERT->bindParam(':tel',$tel,PDO::PARAM_STR);
	error_log("STEP3-4:シナリオを設定する：".$scenario);
	$INSERT->bindParam(':scenario',$scenario,PDO::PARAM_STR);
	error_log("STEP4:SQLを実行する");
	$RESULT=$INSERT->execute();
	error_log("STEP5:SQLの実行結果");
	error_log($name."をデータベースに追加しました。");
	error_log("=================================");

}

?>
