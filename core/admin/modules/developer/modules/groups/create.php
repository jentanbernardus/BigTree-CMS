<?
	$admin->createModuleGroup($_POST["name"]);
	
	$admin->growl("Developer","Created Module Group");
	header("Location: ".$developer_root."modules/groups/view/");
	die();
?>