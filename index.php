<?php
require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;

// Twillioの設定
$account_sid = getenv('account_sid');
$auth_token = getenv('auth_token');
$twilio_number = getenv('twilio_number');
$client = new Client($account_sid, $auth_token);
// Twillioの設定


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
// フリガナ
$kana=$_POST['data']['User']['kana'];
// メールアドレス
$email=$_POST['data']['User']['mail'];
// 電話番号
$tel=$_POST['data']['User']['tel'];
// シナリオ
$scenario=$_POST['data']['User']['scenario'];
// 郵便番号
$zipcode=$_POST['data']['User']['zipcode'];
// 所在地
$zip=$_POST['data']['User']['zip'];
// 支払い方法
//data[User][free3]=%free3%
$free3=$_POST['data']['User']['free3'];	
// 「その他」の場合の支払い方法
$free9=$_POST['data']['User']['free9'];
// 利用規約
$free10=$_POST['data']['User']['free10'];
	
$message = "シナリオ：". $_POST['data']['User']['scenario']."\n".
	"氏名：".$name."\n".
	"フリガナ：".$kana."\n".
	"メールアドレス：".$email."\n".
	"電話番号：".$tel."\n".
	"郵便番号：".$zipcode."\n".
	"住所：".$zip."\n".
	"支払い方法：".$free3."\n".
	"「その他」の場合の支払い方法：".$free9."\n".
	"利用規約：".$free10;

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
	$INSERT=$pdo ->prepare('INSERT INTO users(name, email, tel, scenario, kana,zipcode,zip,free3,free9,free10) VALUES (:name,:email,:tel,:scenario,:kana,:zipcode,:zip,:free3,:free9,:free10)');
	error_log("STEP3-1:名前を設定する：".$name);
	$INSERT->bindParam(':name',$name,PDO::PARAM_STR);
	error_log("STEP3-1-1:フリガナを設定する：".$name);
	$INSERT->bindParam(':kana',$kana,PDO::PARAM_STR);
	error_log("STEP3-2:メールアドレスを設定する：".$email);
	$INSERT->bindParam(':email',$email,PDO::PARAM_STR);
	error_log("STEP3-3:電話番号を設定する：".$tel);
	$INSERT->bindParam(':tel',$tel,PDO::PARAM_STR);
	error_log("STEP3-4:シナリオを設定する：".$scenario);
	$INSERT->bindParam(':scenario',$scenario,PDO::PARAM_STR);
	error_log("STEP3-5:郵便番号：".$zipcode);
	$INSERT->bindParam(':zipcode',$zipcode,PDO::PARAM_STR);
	error_log("STEP3-6:所在地：".$zip);
	$INSERT->bindParam(':zip',$zip,PDO::PARAM_STR);
	error_log("STEP3-7:支払い方法：".$free3);
	$INSERT->bindParam(':free3',$free3,PDO::PARAM_STR);
	error_log("STEP3-8:「その他」の場合の支払い方法：".$free9);
	$INSERT->bindParam(':free9',$free9,PDO::PARAM_STR);
	error_log("STEP3-9:利用規約：".$free10);
	$INSERT->bindParam(':free10',$free10,PDO::PARAM_STR);
	
	error_log("STEP4:SQLを実行する");
	$RESULT=$INSERT->execute();
	error_log("STEP5:SQLの実行結果");
	error_log($name."をデータベースに追加しました。");
	error_log("=================================");

	//error_log("=====TwillioでSMSを送信する=====");
	//$client->messages->create(
		// Where to send a text message (your cell phone?)
	//	 '+81'.$tel,
	//	 array(
	//		 'from' => $twilio_number,
	//		 'body' => getenv('SMS')
	//	 )
	//);
}

?>
