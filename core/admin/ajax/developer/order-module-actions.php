<?
	parse_str($_GET["sort"]);
	$max = count($row);
	
	foreach ($row as $pos => $id) {
		sqlquery("UPDATE bigtree_module_actions SET position = '".($max-$pos)."' WHERE id = '$id'");
	}
?>