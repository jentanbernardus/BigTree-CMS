<?
	$admin->requireLevel(1);
	
	$breadcrumb = array(
		array("link" => "dashboard/", "title" => "Pages"),
		array("link" => "dashboard/integrity/", "title" => "Integrity Check")
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