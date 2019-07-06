<?php 
	function parseGreeting(string $name): string {
		if (strlen($name)) {
			return 'Добро пожаловать, '.$name.'!';		
		}
		else {
			return 'Добро пожаловать, Незнакомец!';
		}
	}