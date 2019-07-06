<?php
	require_once('db_handler.php');
	require_once('config.php');
	
	function getShortUrl($longUrl):string {
		$url = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$longUrl&login=o_4dkf0dhc6p&apiKey=R_a9dbe6c319fe4397946c86b8798b7abb&format=json&history=1";
		$s = curl_init();
		curl_setopt($s,CURLOPT_URL, $url);
		curl_setopt($s,CURLOPT_HEADER, false);
		curl_setopt($s,CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($s);
		curl_close( $s );
		$obj = json_decode($result, true);
		$res = $obj["results"]["$longUrl"]["shortUrl"];
		if (strlen($res) != 0) {
			return $res;
		}
		else {
			return ERROR_MESSAGE;
		}
	}
	
	function getLongUrl($shortUrl):string {
		$query = http_build_query(
			array(
				'login' => LOGIN,
				'apiKey' => API_KEY,
				'shortUrl' => $shortUrl,
				'format' => 'txt'
			)
		);
		$res = file_get_contents(sprintf('%s/%s?%s', END_POINT, 'expand', $query));
		if ($res == "NOT_FOUND") {
			return ERROR_MESSAGE;
		}
		else { 	
			return $res;
		}
	}
	
	function urlHandle($db, $userId, $url) {
		if (strpos($url, 'http') === false) {
			$url = 'http://'.$url;
		}
		
		if (strpos($url, 'bit.ly') === false) {
			$res = getShortUrl($url);
		}
		else {
			$res = getLongUrl($url);
		}
		
		if ($res != ERROR_MESSAGE) {
			updateHistory($db, $res, $userId);
		}
		
		return $res;
	}