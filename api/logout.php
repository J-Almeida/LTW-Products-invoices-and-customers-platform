<?php
session_start();

if(isset($_SESSION['username']))
	unset($_SESSION['username']);

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";
echo "<p style='text-align: center;'>Logged off! Redirecting... </p>";
header('Refresh: 2; URL=../index.php');
?>