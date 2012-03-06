<?
	$admin->requireLevel(1);
	
	$breadcrumb = array(
		array("link" => "dashboard/", "title" => "Dashboard"),
		array("link" => "dashboard/vitals-statistics/", "title" => "Vitals &amp; Statistics"),
		array("link" => "dashboard/vitals-statistics/integrity/", "title" => "Integrity Check")
	);
	
	function _local_build_tree($id) {
		global $pages;
		$q = sqlquery("SELECT id FROM bigtree_pages WHERE parent = '$id' ORDER BY position");
		while ($f = sqlfetch($q)) {
			$pages[] = $f["id"];
			_local_build_tree($f["id"]);
		}
	}
?>