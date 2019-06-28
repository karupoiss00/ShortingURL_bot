<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api;

    $telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY');
    $result = $telegram->getWebhookUpdates();
    $text = $result["message"]["text"];
    $chat_id = $result["message"]["chat"]["id"];
    $name = $result["message"]["from"]["username"];

    if($text){
        if ($text == '/start') {
            if (strlen($name) == 0) {
                $reply = 'Добро пожаловать, Незнакомец!';
            }
            else {
                $reply = 'Добро пожаловать, '.$name.'!';
            }
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
        }
        elseif ($text == '/help') {
            $reply = 'Данный бот предназначен для работы со ссылками. Он умеет сокращать или расшифровывать уже сокращённые ссылки. Для того, чтобы воспользоваться ботом, отправьте ему ссылку, которую необходимо сократить.';
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }
        else {
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => stringHandler($text)]);
        }
    }
    else {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Отправьте текстовое сообщение.' ]);
    }
	
	function stringHandler($string) {
		if(stristr($string, 'http://bit.ly/') === FALSE)) {
			return getSmallLink($string);
		}
		else {
			return getSmallLink($string);
		}
	}
	
    function getSmallLink($longurl){
        $url = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$longurl&login=o_4dkf0dhc6p&apiKey=R_a9dbe6c319fe4397946c86b8798b7abb&format=json&history=1";
        $s = curl_init();
        curl_setopt($s,CURLOPT_URL, $url);
        curl_setopt($s,CURLOPT_HEADER,false);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($s);
        curl_close( $s );
        $obj = json_decode($result, true);
        $res = $obj["results"]["$longurl"]["shortUrl"];
        if (strlen($res) != 0) {
            return 'Ссылка сокращена - '.$res;
        }
        elseif {
            return 'Ссылка некорректна';
        }
    }
	
	function getLongLink($shorturl) {
		
		$url = 'https://api-ssl.bitly.com/v3/expand?access_token=R_a9dbe6c319fe4397946c86b8798b7abb&shortUrl='.$shorturl.'&format=txt';
        $s = curl_init();
        curl_setopt($s,CURLOPT_URL, $url);
        curl_setopt($s,CURLOPT_HEADER,false);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($s);
        curl_close( $s );
        
        if (strlen($result) != 0) {
            return 'Ссылка расшифрована - '.$result;
        }
        elseif {
            return 'Ссылка некорректна';
        }
	}
?>