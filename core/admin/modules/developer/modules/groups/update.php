<?
	$admin->updateModuleGroup(end($path),$_POST["name"]);	

	$admin->growl("Developer","Updated Module Group");
	header("Location: ".$developer_root."modules/groups/view/");
	die();
?>