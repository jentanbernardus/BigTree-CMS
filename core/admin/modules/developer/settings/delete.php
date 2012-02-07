<?
	$id = end($path);
	sqlquery("DELETE FROM bigtree_settings WHERE id = '$id'");
	
	$admin->growl("Developer","Deleted Setting");
	header("Location: ".$saroot."settings/view/");
	die();
?>