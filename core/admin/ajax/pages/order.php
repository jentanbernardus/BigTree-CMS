<?
	$r = $admin->getPageAccessLevelByUser($_GET["id"],$admin->ID);
	if ($r == "p") {
		parse_str($_GET["sort"]);
		
		$max = count($row);
		foreach ($row as $pos => $id)
			sqlquery("UPDATE bigtree_pages SET position = '".($max-$pos)."' WHERE id = '$id'");
	}
?>