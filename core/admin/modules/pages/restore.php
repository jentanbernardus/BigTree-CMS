<?
	$id = end($path);
	$page = $cms->getPageById($id,false);
	$access = $admin->unarchivePage($id);

	$admin->growl("Pages","Restored Page");
	
	header("Location: ".$aroot."pages/view-tree/".$page["parent"]."/");
	die();
?>
