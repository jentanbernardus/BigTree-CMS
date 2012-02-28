<?
	$admin->updateProfile($_POST);
	$admin->growl("Users","Updated Profile");
	header("Location: ".$aroot."dashboard/");
	die();
?>