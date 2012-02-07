<?
	$layout = "developer";
	sqlquery("DELETE FROM bigtree_callouts WHERE id = '".end($path)."'");
	
	$admin->growl("Developer","Deleted Callout");
	header("Location: ".$saroot."callouts/view/");
	die();
?>