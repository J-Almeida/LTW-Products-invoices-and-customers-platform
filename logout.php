<?php
require_once 'bootstrap.php';

header('Refresh: 1; URL=./index.php');

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";

if(isset($_SESSION['permissions'])) {
	unset($_SESSION['permissions']);
}

if(isset($_SESSION['username'])) {
	unset($_SESSION['username']);
}
else {
	echo "<p style='text-align: center;'>You aren't even logged in! Redirecting... </p>";
	exit;
}

echo "<p style='text-align: center;'>Logged off! Redirecting... </p>";
?>