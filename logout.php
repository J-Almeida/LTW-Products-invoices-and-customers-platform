<?php
require_once 'bootstrap.php';

if(isset($_SESSION['username'])) {
	unset($_SESSION['username']);
}

if(isset($_SESSION['permissions'])) {
	unset($_SESSION['permissions']);
}

header('Refresh: 1; URL=./index.php');

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";
echo "<p style='text-align: center;'>Logged off! Redirecting... </p>";
?>