<?
	$admin->updateAPIToken($_POST["id"],$_POST["user"],$_POST["readonly"]);
	
	$admin->growl("Users","Updated API Token");
	
	header("Location: ".$admin_root."users/view-tokens/");
	die();
?>