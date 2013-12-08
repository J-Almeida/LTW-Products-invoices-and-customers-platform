<?php
	function isValidLargeTextField($textField) {
		$pattern = '/^[a-zA-Z0-9 ,#.-]{0,199}$/';
		return(preg_match($pattern, $textField));
	}

	function isValidTextField($textField) {
		$pattern = '/^[a-zA-Z0-9 ,#.-]{0,19}$/';
		return(preg_match($pattern, $textField));
	}

	function isValidUsername($username) {
		$pattern = '/^[A-Za-z][A-Za-z0-9]{5,31}$/';
		return(preg_match($pattern, $username));
	}

	function isValidEmail($email) {
		$pattern = "[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?";
		return(preg_match($pattern, $email));
	}
?>