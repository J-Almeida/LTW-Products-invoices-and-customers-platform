<?php
require_once 'bootstrap.php';
require_once './api/authenticationUtilities.php';

header('Refresh: 1; URL=http://localhost/ltw/index.php');

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";

//Check if the credentials were correctly sent
if(isset($_POST["login"]) && isset($_POST["password"])) {
	$login = $_POST["login"];
	$password = $_POST["password"];
}
else {
	echo "<p style='text-align: center;'>Please log in using the correct form, thank you!</p>";
	echo "<p style='text-align: center;'>Redirecting... </p>";
	exit;
}

//Don't let a user log in if there's currently a logged in one on the same session
if(isset($_SESSION['username'])) {
	echo "<p style='text-align: center;'>You're already logged in.</p>";
	echo "<p style='text-align: center;'>Redirecting... </p>";
	exit;
}

//If the credentials are correct, log in user!
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
exit;
?>