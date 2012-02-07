<?
	$id = end($commands);
	$module = $commands[0];
		
	sqlquery("DELETE FROM bigtree_module_views WHERE id = '$id'");
	sqlquery("DELETE FROM bigtree_module_actions WHERE view = '$id'");
	
	$admin->growl("Developer","Deleted View");
	header("Location: ".$saroot."modules/edit/$module/");
	die();
?>