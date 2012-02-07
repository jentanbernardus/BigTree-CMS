<?
	sqlquery("DELETE FROM bigtree_templates WHERE id = '".end($path)."'");
	
	$admin->growl("Developer","Deleted Template");
	header("Location: ".$saroot."templates/view/");
	die();
?>