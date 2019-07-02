<?php
	require_once('vendor/autoload.php');
	
	const DB_HOST = 'us-cdbr-iron-east-02.cleardb.net';
	const DB_USER = 'b8ee931ac989b3';
	const DB_PASS = '672ff549';
	const DB_NAME = 'heroku_3fd1c3b404a32b0';
	const CHAT_ID = 'chat_id';
	const FIRST_REQUEST = 'first_request';
	const SECOND_REQUEST = 'second_request';
	const THIRD_REQUEST = 'third_request';
	const FOURTH_REQUEST = 'fourth_request';
	const FIFTH_REQUEST = 'fifth_request';

	function initDB(): MysqliDb {
		$db = new MysqliDb (DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$db->autoReconnect = true;
		return $db;
	}

	function updateHistory(MysqliDb $db, $lastAction, $userId) {
		$record = getRow($db, $userId);
		if (count($record)) {
			$record[FIRST_REQUEST] = $record[SECOND_REQUEST];
			$record[SECOND_REQUEST] = $record[THIRD_REQUEST];
			$record[THIRD_REQUEST] = $record[FOURTH_REQUEST];
			$record[FOURTH_REQUEST] = $record[FIFTH_REQUEST];
			$record[FIFTH_REQUEST] = $lastAction;
			updateRow($db, $record);
		}
		else {
			$data = [
				CHAT_ID => $chat_id,
				FIRST_REQUEST => '<пусто>',
				SECOND_REQUEST => '<пусто>',
				THIRD_REQUEST => '<пусто>',
				FOURTH_REQUEST => '<пусто>',
				FIFTH_REQUEST => $lastAction
			];
			insertRow($db, $data);
		}
	}
	
	function getUserHistory(MysqliDb $db, $userId): string {
		$row = getRow($db, $userId);
		if (count($row)) {
			return formatRowToStr($row);
		}
		else {
			$data = [
				CHAT_ID => $chat_id,
				FIRST_REQUEST => '<пусто>',
				SECOND_REQUEST => '<пусто>',
				THIRD_REQUEST => '<пусто>',
				FOURTH_REQUEST => '<пусто>',
				FIFTH_REQUEST => '<пусто>'
			];
			insertRow($db, $data);
			return formatRowToStr($data);
		}
	}
	
	function formatRowToStr($data): string {
		$data = array_slice($data , 1);
		$res = "Последние действия:\n";
		foreach ($history as $record) {
			$res .= $record."\n";
		}
		return $res;
	}
	
	function getRow(MysqliDb $db, $userId): array {
		$db->where(CHAT_ID, $userId);
		$row = $db->getOne('user_request_history');
		return $row;
	}
	
	function insertRow(MysqliDb $db, $row) {
		$db->insert('user_request_history', $row);
	}
	
	function updateRow(MysqliDb $db, $row) {
		$db->update('user_request_history', $row);
	}