<?
	$page = end($path);
	
	if (is_numeric($page)) {
		$f = $cms->getPage($page);
		$parent = $f["parent"];
	} else {
		$f = $cms->getPendingChange(substr($page,1));
		$parent = $f["changes"]["parent"];
	}
	
	$admin->deletePage($page);
	
	header("Location: ".$admin_root."pages/view-tree/$parent/");
?>
