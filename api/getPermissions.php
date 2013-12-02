<?php
session_start(); 
if(isset($_SESSION['username']) && isset($_SESSION['permissions']) && !empty($_SESSION['permissions'])) {
	echo json_encode($_SESSION['permissions']);
}
else
	echo "none";
?>