<?php

include_once 'utilities.php';

function checkPassword($login, $password) {
	$db = new PDO("sqlite:../database.db");
	$query = $db->query("SELECT * FROM User WHERE (username = '$login' OR email = '$login') AND password = '$password'");
	$query->setFetchMode(PDO::FETCH_ASSOC);
   	$result = $query->fetch();

   	if($result)
   		return true;
   	else
   		return false;
}

function getUsername($login) {
	$db = new PDO("sqlite:../database.db");
	$query = $db->query("SELECT username FROM User WHERE (username = '$login' OR email = '$login')");
	$query->setFetchMode(PDO::FETCH_ASSOC);
   	$result = $query->fetch();

   	if($result)
   		return($result["username"]);
}

function getPermissions($user) {
	$db = new PDO("sqlite:../database.db");
	$query = $db->query("SELECT * FROM User, Permission WHERE (username = '$user' AND User.permissionId = Permission.permissionId)");
	$query->setFetchMode(PDO::FETCH_ASSOC);
   	$result = $query->fetch();

   	if($result) {
   		$permissions = array('read' => $result["permissionRead"], 'write' => $result["permissionWrite"], 'promote' => $result["promote"]);
   		return $permissions;
   	}
}

session_start();
$login = $_POST["login"];
$password = $_POST["password"];

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";

if(checkPassword($login, $password)) {
	$user = getUsername($login);
	$permissions = getPermissions($user);
	$_SESSION['username'] = $user;
	$_SESSION['permissions'] = $permissions;
	echo "<p style='text-align: center;'>Welcome " . $user . "</p>";
}
else
	echo "<p style='text-align: center;'>Wrong username or password. </p>";

echo "<p style='text-align: center;'>Redirecting... </p>";
header('Refresh: 2; URL=../index.php');
exit;
?>