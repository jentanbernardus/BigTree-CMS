<?
	$id = end($commands);
	$module = $commands[0];
	
	sqlquery("DELETE FROM bigtree_module_forms WHERE id = '$id'");
	sqlquery("DELETE FROM bigtree_module_actions WHERE form = '$id'");
	
	$admin->growl("Developer","Deleted Form");
	header("Location: ".$saroot."modules/edit/$module/");
	die();
?>