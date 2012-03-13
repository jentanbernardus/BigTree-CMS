<?
	$admin->createAPIToken($_POST["user"],$_POST["readonly"]);

	$admin->growl("Users","Added API Token");
	header("Location: ".$admin_root."users/view-tokens/");
	die();
?>