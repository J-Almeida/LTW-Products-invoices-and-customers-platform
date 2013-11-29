<?php
include 'utilities.php';

function checkPassword($login, $password) {
	$db = new PDO("sqlite:../database.db");
	$query = $db->query("SELECT * FROM User WHERE (username = '$login' OR userEmail = '$login') AND userPassword = '$password'");
	$query->setFetchMode(PDO::FETCH_ASSOC);
   	$result = $query->fetch();

   	if($result)
   		return true;
   	else
   		return false;
}

function getUsername($login) {
	$db = new PDO("sqlite:../database.db");
	$query = $db->query("SELECT username FROM User WHERE (username = '$login' OR userEmail = '$login')");
	$query->setFetchMode(PDO::FETCH_ASSOC);
   	$result = $query->fetch();

   	if($result)
   		return($result["username"]);
}

session_start();
$login = $_POST["login"];
$password = $_POST["password"];

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";

if(checkPassword($login, $password)) {
	$user = getUsername($login);
	$_SESSION['username'] = $user;
	echo "<p style='text-align: center;'>Welcome " . $user . "</p>";
}
else
	echo "<p style='text-align: center;'>Wrong username or password. </p>";

echo "<p style='text-align: center;'>Redirecting... </p>";
header('Refresh: 2; URL=../index.php');
exit;
?>