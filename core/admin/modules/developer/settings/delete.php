<?
	$id = end($path);
	sqlquery("DELETE FROM bigtree_settings WHERE id = '$id'");
	
	$admin->growl("Developer","Deleted Setting");
	header("Location: ".$developer_root."settings/view/");
	die();
?>