<?
	$admin->requireLevel(1);
	$success = $admin->updateUser(end($path),$_POST);
	
	if (!$success) {
		$_SESSION["bigtree"]["update_user"] = $_POST;
		$admin->growl("Users","Update Failed","error");
		header("Location: ".$aroot."users/edit/".end($path)."/");
		die();
	}
	
	$admin->growl("Users","Updated User");
	
	header("Location: ".$aroot."users/");
	die();
?>