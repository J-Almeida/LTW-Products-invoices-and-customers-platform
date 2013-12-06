<?php
require_once 'bootstrap.php';
require_once './api/authenticationUtilities.php';

$login = $_POST["login"];
$password = $_POST["password"];

header('Refresh: 1; URL=http://localhost/ltw/index.php');

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
exit;
?>