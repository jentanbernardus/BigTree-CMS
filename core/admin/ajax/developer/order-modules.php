<?
	parse_str($_GET["sort"]);
	$max = count($parse);
	
	foreach ($row as $pos => $id) {
		sqlquery("UPDATE bigtree_modules SET position = '".($max-$pos)."' WHERE id = '$id'");
	}
?>