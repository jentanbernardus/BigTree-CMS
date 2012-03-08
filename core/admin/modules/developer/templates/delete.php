<?
	sqlquery("DELETE FROM bigtree_templates WHERE id = '".end($path)."'");
	
	$admin->growl("Developer","Deleted Template");
	header("Location: ".$developer_root."templates/view/");
	die();
?>