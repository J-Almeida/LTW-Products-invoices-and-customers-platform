<?php
require_once 'utilities.php';

function checkPassword($login, $password) {
	$db = new PDO(getDatabase());
	$query = $db->prepare("SELECT * FROM User WHERE (username = '$login' OR email = '$login') AND password = '$password'");
	$query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
   	$result = $query->fetch();

   	if($result)
   		return true;
   	else
   		return false;
}

function getUsername($login) {
	$db = new PDO(getDatabase());
	$query = $db->prepare("SELECT username FROM User WHERE (username = '$login' OR email = '$login')");
    $query->execute();
	$query->setFetchMode(PDO::FETCH_ASSOC);
   	$result = $query->fetch();

   	if($result)
   		return($result["username"]);
      else
         return(array());
}

function getPermissions($user) {
	$db = new PDO(getDatabase());
	$query = $db->prepare("SELECT * FROM User, Permission WHERE (username = '$user' AND User.permissionId = Permission.permissionId)");
	$query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
   	$result = $query->fetch();

   	if($result) {
   		$permissions = array('read' => $result["permissionRead"], 'write' => $result["permissionWrite"], 'promote' => $result["promote"]);
   		return $permissions;
   	}
}

function getSessionPermissions() {
   if(isset($_SESSION['username']) && isset($_SESSION['permissions']) && !empty($_SESSION['permissions'])) {
      return ($_SESSION['permissions']);
   }
   else
      return array();
}

function comparePermissions($neededPermissions) {
   $permissions = getSessionPermissions();

   if(empty($permissions))
      return false;

   foreach ($neededPermissions as $neededPermission) {
      if($permissions[$neededPermission] != 1) {
         return false;
      }
   }

   return true;
}

function evaluateSessionPermissions($neededPermissions) {
   $hasPermission = comparePermissions($neededPermissions);
   if(!$hasPermission) {
      header("Location: ./nopermission.html");
      exit;
   }
}
?>