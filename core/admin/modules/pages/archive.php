<?
	$id = end($path);
	$page = $cms->getPageById($id,false);
	$access = $admin->archivePage($id);
	
	$admin->growl("Pages","Archived Page");

	header("Location: ".$aroot."pages/view-tree/".$page["parent"]."/");
	die();
?>
