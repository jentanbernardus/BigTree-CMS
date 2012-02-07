<?
	$page = end($path);
	
	if (is_numeric($page)) {
		$f = sqlfetch(sqlquery("SELECT parent FROM bigtree_pages WHERE id = '$page'"));
		$parent = $f["parent"];
	} else {
		$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '".substr($page,1)."'"));
		$changes = json_decode($f["changes"],true);
		$parent = $changes["parent"];
	}
	
	$admin->deletePage($page);
	
	header("Location: ".$aroot."pages/view-tree/$parent/");
?>
